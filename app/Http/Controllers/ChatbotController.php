<?php

namespace App\Http\Controllers;

use App\Models\Actualite;
use App\Models\Commune;
use App\Models\Departement;
use App\Models\Indicateur;
use App\Models\Infrastructure;
use App\Models\Localite;
use App\Models\Region;
use App\Models\Secteur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Endpoint JSON appelé par le widget de chat (jamais exposé côté client).
     * Reçoit la question + un bref historique, construit un contexte métier
     * à partir des données de la plateforme puis interroge le fournisseur
     * d'IA actif (config services.chatbot.provider).
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:500'],
            'historique' => ['sometimes', 'array', 'max:15'],
            'historique.*.role' => ['required', 'in:user,assistant'],
            'historique.*.texte' => ['required', 'string', 'max:2000'],
        ]);

        $messages = $this->construireMessages($validated);

        $texte = $this->requeteIA($messages);

        if ($texte !== null) {
            return response()->json([
                'ok' => true,
                'response' => $texte,
            ]);
        }

        return $this->reponseLocale($validated['message']);
    }

    /**
     * Dialogue avec le fournisseur d'IA actif. Gère le mode "outils"
     * (recherche web Google via Serper) : si le modèle demande un outil,
     * on l'exécute et on relance une fois avec le résultat.
     */
    protected function requeteIA(array $messages): ?string
    {
        $provider = config('services.chatbot.provider', 'openai');
        $config = config('services.'.$provider);

        $cles = $this->clesFournisseur($config);

        if ($cles === []) {
            Log::warning("Chatbot SAMA TERRITOIRE : aucune clé valide pour le fournisseur « {$provider} ».");

            return null;
        }

        $url = rtrim($config['base_url'] ?? '', '/').'/chat/completions';
        $cleRecherche = config('services.search.key', '');
        $outils = $cleRecherche !== '' ? $this->outilsRechercheWeb() : [];

        for ($tour = 0; $tour < 3; $tour++) {
            $reponse = $this->appelChat($url, $config, $messages, $outils, $provider, $cles);

            if ($reponse === null) {
                return null;
            }

            $message = $reponse['choices'][0]['message'] ?? null;
            if (! is_array($message)) {
                return null;
            }

            $appels = $message['tool_calls'] ?? null;

            if (is_array($appels) && $appels !== []) {
                $messages[] = $message;

                foreach ($appels as $index => $appel) {
                    $arguments = json_decode($appel['function']['arguments'] ?? '{}', true);

                    $messages[] = [
                        'role' => 'tool',
                        'tool_call_id' => $appel['id'] ?? $appel['tool_call_id'] ?? ('appel_'.$index),
                        'content' => $this->rechercheSerper(
                            $cleRecherche,
                            is_array($arguments) ? ($arguments['query'] ?? '') : ''
                        ),
                    ];
                }

                continue;
            }

            $texte = is_string($message['content'] ?? null) ? trim($message['content']) : '';

            return $texte !== '' ? $texte : null;
        }

        return null;
    }

    /**
     * Liste des clés utilisables pour le fournisseur, dans l'ordre de priorité.
     * La première est celle par défaut ; les suivantes servent de secours
     * (ex. quotas gratuits Gemini épuisés).
     */
    protected function clesFournisseur(array $config): array
    {
        $cles = [];

        if (isset($config['key']) && is_string($config['key']) && $config['key'] !== '') {
            $cles[] = $config['key'];
        }

        if (! empty($config['keys']) && is_array($config['keys'])) {
            foreach ($config['keys'] as $cle) {
                if (is_string($cle) && $cle !== '' && ! in_array($cle, $cles, true)) {
                    $cles[] = $cle;
                }
            }
        }

        return array_values($cles);
    }

    /**
     * POST vers le fournisseur avec politique de relance sur les erreurs
     * transitoires. Si une clé atteint son quota (429 quota épuisé), on bascule
     * automatiquement sur les clés suivantes. Retourne le corps JSON décodé,
     * ou null.
     */
    protected function appelChat(string $url, array $config, array $messages, array $outils, string $provider, array $cles): ?array
    {
        $corps = [
            'model' => $config['model'],
            'messages' => $messages,
            'temperature' => $config['temperature'] ?? 0.5,
            'max_tokens' => $config['max_tokens'] ?? 600,
        ];

        if ($outils !== []) {
            $corps['tools'] = $outils;
        }

        $statutsTransitoires = [408, 429, 500, 502, 503, 504];
        $budgetSecondes = 35;
        $essaisParCle = 2;
        $debut = microtime(true);

        foreach ($cles as $index => $cle) {
            $numeroCle = $index + 1;

            // Clé signalée épuisée récemment (quotas gratuits) : on la saute,
            // sinon chaque question paierait un aller-retour inutile.
            if (Cache::get('chatbot.cle'.$numeroCle.'.eclipsee')) {
                continue;
            }

            for ($essai = 0; $essai < $essaisParCle; $essai++) {
                $reste = $budgetSecondes - (microtime(true) - $debut);

                if ($reste <= 0) {
                    return null;
                }

                $timeout = (int) min($config['timeout'] ?? 20, max(1, $reste));

                try {
                    $reponse = Http::withToken($cle)
                        ->asJson()
                        ->timeout($timeout)
                        ->post($url, $corps);
                } catch (\Throwable $e) {
                    Log::error('Chatbot SAMA TERRITOIRE : exception lors de l\'appel au fournisseur.', [
                        'provider' => $provider,
                        'cle' => $numeroCle,
                        'essai' => $essai + 1,
                        'erreur' => $e->getMessage(),
                    ]);

                    // Timeout réseau passager : petite pause puis clé suivante.
                    usleep(300000);

                    break;
                }

                if ($reponse->successful()) {
                    $decoded = $reponse->json();

                    return is_array($decoded) ? $decoded : null;
                }

                $statut = $reponse->status();
                $texteReponse = mb_strtolower($reponse->body());
                $quotaEpuise = $statut === 429
                    && (str_contains($texteReponse, 'resource_exhausted') || str_contains($texteReponse, 'quota'));

                if ($quotaEpuise) {
                    Log::warning('Chatbot SAMA TERRITOIRE : quota épuisé pour la clé n°'.$numeroCle.' du fournisseur « '.$provider.' », bascule sur la clé suivante.');
                    Cache::put('chatbot.cle'.$numeroCle.'.eclipsee', true, 600);

                    break;
                }

                Log::warning('Chatbot SAMA TERRITOIRE : réponse invalide du fournisseur.', [
                    'provider' => $provider,
                    'cle' => $numeroCle,
                    'essai' => $essai + 1,
                    'status' => $statut,
                    'contenu' => $reponse->body(),
                ]);

                // Erreur transitoire (503 « high demand », dépassement de limite…) :
                // on tente la clé suivante pour répartir la charge.
                if (! in_array($statut, $statutsTransitoires, true) || $reste <= 1) {
                    break;
                }

                usleep(500000);
            }
        }

        return null;
    }

    /**
     * Description de l'outil « recherche web » exposé au modèle d'IA.
     */
    protected function outilsRechercheWeb(): array
    {
        return [[
            'type' => 'function',
            'function' => [
                'name' => 'recherche_web',
                'description' => 'Effectue une recherche web (Google, résultats Serper) pour obtenir des informations à jour ou hors plateforme, par exemple sur les décrets, l\'économie, le « sel de Kaolack », etc.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => [
                            'type' => 'string',
                            'description' => 'La requête à rechercher sur le web.',
                        ],
                    ],
                    'required' => ['query'],
                ],
            ],
        ]];
    }

    /**
     * Exécute une recherche Google via Serper.dev et renvoie un texte
     * exploitable par le modèle (titres + liens + extraits).
     */
    protected function rechercheSerper(string $cle, string $requete): string
    {
        $requete = trim($requete);

        if ($requete === '' || $cle === '') {
            return 'Recherche impossible : paramètres manquants.';
        }

        try {
            $reponse = Http::withHeaders([
                'X-API-KEY' => $cle,
                'Content-Type' => 'application/json',
            ])
                ->timeout(20)
                ->post('https://google.serper.dev/search', [
                    'q' => $requete,
                    'gl' => 'fr',
                    'hl' => 'fr',
                    'num' => 5,
                ]);
        } catch (\Throwable $e) {
            Log::error('Chatbot SAMA TERRITOIRE : échec recherche web.', ['erreur' => $e->getMessage()]);

            return "Échec de la recherche web : {$e->getMessage()}";
        }

        if (! $reponse->successful()) {
            return 'La recherche web a échoué ('.$reponse->status().').';
        }

        $resultats = array_filter($reponse->json('organic') ?? [], is_array(...));

        if ($resultats === []) {
            return 'Aucun résultat web trouvé pour cette requête.';
        }

        $texte = "Résultats de la recherche web pour « {$requete} » :\n";
        $extraits = array_slice($resultats, 0, 5);

        foreach ($extraits as $r) {
            $texte .= '- '.($r['title'] ?? 'Sans titre').' — '.($r['link'] ?? '').($r['snippet'] ?? '' !== '' ? ' : '.$r['snippet'] : '')."\n";
        }

        return mb_substr($texte, 0, 3000);
    }

    /**
     * Réponse de repli : utilisée quand le fournisseur d'IA est indisponible
     * (clé absente, crédit épuisé, panne réseau...). Le serveur répond lui-même
     * à partir des données de la base grâce à une simple analyse de mots-clés.
     */
    protected function reponseLocale(string $question): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'fallback' => true,
            'response' => $this->repondreLocalement($question),
        ]);
    }

    /**
     * Moteur de repli : analyse la question et répond avec les données
     * réelles de la plateforme. Ne répond jamais "inventé" : si aucun mot-clé
     * ne correspond, il le dit honnêtement.
     */
    protected function repondreLocalement(string $question): string
    {
        $q = $this->normaliser($question);

        if ($this->contient($q, ['bonjour', 'salut', 'bonsoir', 'hello', 'coucou', 'salam', 'bjr'])) {
            return "Bonjour ! 😊 Je suis l'assistant de SAMA TERRITOIRE. Posez-moi une question sur les communes, départements, localités, infrastructures ou actualités de la région de Kaolack.";
        }

        if ($this->contient($q, ['merci'])) {
            return "Avec plaisir ! N'hésitez pas si vous avez d'autres questions. 🙌";
        }

        if ($this->contient($q, ['qui es', 'tu es', 'assistant', 'présentez'])) {
            return "Je suis l'assistant de **SAMA TERRITOIRE**, la plateforme de données territoriales de la région de Kaolack. Je réponds à partir des données saisies par les administrateurs : communes, départements, localités, secteurs, infrastructures, indicateurs et actualités. 📊";
        }

        $departements = Departement::query()->orderBy('nom')->get();
        $communes = Commune::query()->orderBy('nom')->get();
        $localites = Localite::query()->orderBy('nom')->get();
        $infrastructures = Infrastructure::query()->with(['secteur', 'commune'])->get();
        $secteurs = Secteur::query()->orderBy('nom')->get();
        $actualites = Actualite::query()->whereNotNull('date_publication')->orderByDesc('date_publication')->take(5)->get();

        if ($this->contient($q, ['département', 'departement'])) {
            $liste = $departements->pluck('nom')->implode(', ');
            $nb = $departements->count();

            return "La région de Kaolack compte **{$nb} département(s)** : {$liste}.";
        }

        if ($this->contient($q, ['commune'])) {
            $liste = $communes->pluck('nom')->implode(', ');
            $nb = $communes->count();

            return "La région de Kaolack compte **{$nb} commune(s)** : {$liste}.";
        }

        if ($this->contient($q, ['localité', 'localite', 'village'])) {
            if ($localites->isEmpty()) {
                return "Aucune localité n'est encore enregistrée sur la plateforme.";
            }

            $liste = $localites->pluck('nom')->implode(', ');

            return "La plateforme recense **{$localites->count()} localité(s)** : {$liste}.";
        }

        if ($this->contient($q, ['secteur'])) {
            $liste = $secteurs->pluck('nom')->implode(', ');

            return "Les secteurs d'activité recensés sont : **{$liste}**.";
        }

        if ($this->contient($q, ['infrastructure', 'hôpital', 'hopital', 'école', 'ecole', 'centre de santé', 'poste de santé', 'station', 'pompe'])) {
            if ($infrastructures->isEmpty()) {
                return "Aucune infrastructure n'est encore enregistrée sur la plateforme.";
            }

            $noms = $infrastructures->map(function (Infrastructure $i) {
                $lieu = $i->commune?->nom ?? 'localisation non précisée';
                $type = $i->type_infrastructure ?? 'type non précisé';

                return "{$i->nom} ({$type} — {$lieu})";
            })->implode(' ; ');

            return "La plateforme recense **{$infrastructures->count()} infrastructure(s)** : {$noms}.";
        }

        if ($this->contient($q, ['indicateur'])) {
            $indicateurs = Indicateur::query()->orderBy('nom_indicateur')->get();
            $liste = $indicateurs->pluck('nom_indicateur')->implode(', ');

            return $indicateurs->isEmpty()
                ? "Aucun indicateur n'est encore enregistré sur la plateforme."
                : "Les indicateurs enregistrés sont : **{$liste}**.";
        }

        if ($this->contient($q, ['actualité', 'actualite', 'nouvelle', 'news', 'publication', 'info'])) {
            if ($actualites->isEmpty()) {
                return "Aucune actualité n'a encore été publiée sur la plateforme.";
            }

            $liste = $actualites->map(fn ($a) => $a->titre.' (publiée le '.$a->date_publication?->format('d/m/Y').')')->implode(' ; ');

            return "Voici les dernières actualités de la plateforme : {$liste}.";
        }

        if ($this->contient($q, ['superficie'])) {
            $regions = Region::query()->orderBy('nom')->get();
            $liste = $regions->map(fn (Region $r) => $r->nom.' : '.$this->nbr($r->superficie).' km²')->implode(' ; ');

            return "Superficies enregistrées — {$liste}.";
        }

        if ($this->contient($q, ['population', 'habitants', 'combien de personnes'])) {
            $nb = $communes->sum(fn ($c) => (float) $c->taille_population);

            return "La population totale des communes enregistrées est d'environ **".$this->nbr($nb ?: 0).' habitants**.';
        }

        return "Pour l'instant, l'assistant répond essentiellement à partir des données saisies dans la plateforme : communes, départements, localités, infrastructures, secteurs, indicateurs et actualités. Essayez par exemple « combien y a-t-il de communes ? », « liste des localités » ou « quelles infrastructures ? ». Dès que le crédit de l'IA est rétabli, je pourrai répondre plus largement, notamment sur les décrets et textes administratifs. 🙏";
    }

    /**
     * Normalise une question : minuscules, sans accents ni apostrophes,
     * entourée d'espaces pour fiabiliser la recherche de mots-clés.
     */
    protected function normaliser(string $texte): string
    {
        $texte = mb_strtolower($texte);
        $texte = strtr($texte, [
            'à' => 'a', 'â' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'î' => 'i', 'ï' => 'i', 'ô' => 'o', 'ö' => 'o',
            'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ç' => 'c',
            "'" => ' ', '’' => ' ',
        ]);

        return ' '.$texte.' ';
    }

    /**
     * Vrai si la question normalisée contient au moins un des mots-clés.
     */
    protected function contient(string $question, array $mots): bool
    {
        foreach ($mots as $mot) {
            if (str_contains($question, $mot)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Construit la liste des messages envoyés au fournisseur :
     * un système métier (contexte plateforme) + un court historique
     * + la question courante.
     */
    protected function construireMessages(array $validated): array
    {
        $rechercheWebActivee = config('services.search.key', '') !== '';
        $regle1 = $rechercheWebActivee
            ? 'RÈGLE 1 : si la question concerne le territoire de Kaolack ou les données de la plateforme, réponds UNIQUEMENT à partir du « CONTEXTE PLATEFORME » fourni ci-dessous. Si l\'information n\'y figure pas, utilise la fonction recherche_web pour chercher des informations à jour avant d\'avouer ton ignorance.'
            : 'RÈGLE 1 : si la question concerne le territoire de Kaolack ou les données de la plateforme, réponds UNIQUEMENT à partir du « CONTEXTE PLATEFORME » fourni ci-dessous. Si l\'information n\'y figure pas, réponds honnêtement que cette information n\'est pas disponible sur la plateforme actuellement.';

        $system = [
            "Tu es l'assistant officiel de SAMA TERRITOIRE, la plateforme de données territoriales de la région de Kaolack (Sénégal).",
            'Réponds toujours en français, de façon claire, courtoise et concise (2 à 6 phrases, des puces si utile).',
            $regle1,
            'RÈGLE 2 : pour toute autre question (organisation administrative du Sénégal, communes, départements, localités, décrets, textes juridiques, actualités nationales, etc.), tu peux répondre avec tes connaissances générales, en précisant que ce sont des informations générales à vérifier auprès des sources officielles.',
            'RÈGLE 3 : ne jamais inventer de chiffres issus de la plateforme ni de coordonnées. Si une donnée manque, le dire ou chercher sur le web.',
            '',
            '=== CONTEXTE PLATEFORME (données saisies) ===',
            $this->contexteTerritoire(),
        ];

        $messages = [['role' => 'system', 'content' => implode("\n", $system)]];

        // Historique court, ordonné du plus ancien au plus récent.
        if (! empty($validated['historique'])) {
            foreach (array_slice($validated['historique'], -10) as $echange) {
                $messages[] = [
                    'role' => $echange['role'],
                    'content' => str_replace(["\r", "\n"], ' ', mb_substr($echange['texte'], 0, 2000)),
                ];
            }
        }

        $messages[] = ['role' => 'user', 'content' => $validated['message']];

        return $messages;
    }

    /**
     * Contexte texte des données de la plateforme, mis en cache 15 minutes
     * pour éviter d'interroger la base à chaque message et maîtriser les coûts.
     */
    protected function contexteTerritoire(): string
    {
        return Cache::remember('chatbot.contexte', 900, function () {
            $lignes = [];

            $regions = Region::query()->orderBy('nom')->with('departements.communes.localites')->get();
            $secteurs = Secteur::query()->orderBy('nom')->with('indicateurs')->get();
            $infrastructures = Infrastructure::query()
                ->with(['secteur', 'commune', 'departement', 'localitesCouvertes', 'indicateurs'])
                ->get();
            $indicateurs = Indicateur::query()->orderBy('nom_indicateur')->get();
            $actualites = Actualite::query()
                ->whereNotNull('date_publication')
                ->orderByDesc('date_publication')
                ->take(8)
                ->get();

            if ($regions->isNotEmpty()) {
                $lignes[] = 'Régions :';
                foreach ($regions as $region) {
                    $lignes[] = sprintf(
                        '  - %s : superficie %s km², population %s, ménages %s.',
                        $region->nom,
                        $this->nbr($region->superficie),
                        $this->nbr($region->taille_population),
                        $this->nbr($region->nbre_menage)
                    );

                    foreach ($region->departements as $departement) {
                        $lignes[] = sprintf(
                            '      Département %s : population %s, superficie %s km².',
                            $departement->nom,
                            $this->nbr($departement->taille_population),
                            $this->nbr($departement->superficie)
                        );

                        foreach ($departement->communes as $commune) {
                            $localites = $commune->localites->pluck('nom')->implode(', ');
                            $lignes[] = sprintf(
                                '        Commune %s : population %s, superficie %s km²%s.',
                                $commune->nom,
                                $this->nbr($commune->taille_population),
                                $this->nbr($commune->superficie),
                                $localites !== '' ? ', localités : '.$localites : ', aucune localité listée'
                            );
                        }
                    }
                }
            }

            if ($secteurs->isNotEmpty()) {
                $lignes[] = 'Secteurs d\'activité :';
                foreach ($secteurs as $secteur) {
                    $nomsIndicateurs = $secteur->indicateurs->pluck('nom_indicateur')->implode(', ');
                    $lignes[] = sprintf('  - %s%s.', $secteur->nom, $nomsIndicateurs !== '' ? ' | indicateurs : '.$nomsIndicateurs : '');
                }
            }

            if ($infrastructures->isNotEmpty()) {
                $lignes[] = 'Infrastructures :';
                foreach ($infrastructures as $infra) {
                    $lieu = $infra->commune?->nom ?? $infra->departement?->nom ?? 'non précisé';
                    $localites = $infra->localitesCouvertes->pluck('nom')->implode(', ');
                    $valeurs = $infra->indicateurs->take(4)->map(
                        fn ($ind) => $ind->nom_indicateur.' = '.($ind->pivot->valeur ?? 'non renseignée').($ind->unites ? ' '.$ind->unites : '')
                    )->implode(' ; ');

                    $lignes[] = sprintf(
                        '  - %s (%s), secteur %s, à %s%s%s%s.',
                        $infra->nom,
                        $infra->type_infrastructure ?? 'type non précisé',
                        $infra->secteur?->nom ?? 'non précisé',
                        $lieu,
                        $infra->etat_lieu ? ', état : '.$infra->etat_lieu : '',
                        $localites !== '' ? ', localités couvertes : '.$localites : '',
                        $valeurs !== '' ? ' | mesures : '.$valeurs : ''
                    );
                }
            }

            if ($indicateurs->isNotEmpty()) {
                $lignes[] = 'Indicateurs mesurables :';
                foreach ($indicateurs as $indicateur) {
                    $lignes[] = sprintf(
                        '  - %s%s%s.',
                        $indicateur->nom_indicateur,
                        $indicateur->unites ? ' ('.$indicateur->unites.')' : '',
                        $indicateur->secteur ? ' – secteur '.$indicateur->secteur->nom : ''
                    );
                }
            }

            if ($actualites->isNotEmpty()) {
                $lignes[] = 'Actualités récentes :';
                foreach ($actualites as $actualite) {
                    $extrait = mb_substr(trim(strip_tags($actualite->contenu ?? '')), 0, 140);
                    $lignes[] = sprintf(
                        '  - %s (publiée le %s)%s.',
                        $actualite->titre,
                        $actualite->date_publication?->format('d/m/Y') ?? 'date inconnue',
                        $extrait !== '' ? ' : '.$extrait : ''
                    );
                }
            }

            return implode("\n", $lignes);
        });
    }

    /**
     * Formatage d'un nombre en français (milliers séparés par une espace),
     * avec arrondi sûr en cas de valeur non numérique.
     */
    protected function nbr($valeur): string
    {
        if ($valeur === null || $valeur === '') {
            return 'non renseignée';
        }

        return number_format((float) $valeur, 0, ',', ' ');
    }
}
