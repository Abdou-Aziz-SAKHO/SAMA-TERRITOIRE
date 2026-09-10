<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Commentaires et plaintes — SAMA TERRITOIRE</title>
<style>
    * { box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; color: #1a2d22; font-size: 11px; margin: 0; padding: 0; }
    .page { padding: 24px 32px; }
    .header { border-bottom: 3px solid #267a47; padding-bottom: 14px; margin-bottom: 10px; }
    .header-center { text-align: center; }
    .logo-img { max-height: 90px; max-width: 260px; margin-bottom: 6px; }
    .logo { font-family: 'DejaVu Sans', sans-serif; font-size: 22px; font-weight: bold; color: #267a47; letter-spacing: .5px; }
    .logo span { color: #1a7abf; }
    .org { font-size: 11px; color: #4a6555; margin-top: 2px; }
    .ref-line { text-align: left; font-size: 10px; color: #4a6555; margin: 8px 0 12px; }
    .title { font-size: 17px; font-weight: bold; color: #1a2d22; margin: 0 0 4px; }
    .subtitle { font-size: 11px; color: #4a6555; margin-bottom: 18px; }
    .info-block { border: 1px solid #d0ddd4; border-radius: 8px; padding: 12px 16px; margin-bottom: 18px; background: #f7faf8; }
    .info-grid { display: flex; justify-content: space-between; gap: 16px; }
    .info-item { font-size: 11px; color: #1a2d22; }
    .info-item b { display: block; font-size: 9px; text-transform: uppercase; letter-spacing: .4px; color: #267a47; margin-bottom: 3px; }
    .dest { font-size: 11px; color: #1a2d22; }
    .dest b { display: block; font-size: 9px; text-transform: uppercase; letter-spacing: .4px; color: #267a47; margin-bottom: 3px; }
    .recap { display: flex; gap: 10px; margin-bottom: 18px; }
    .pill { border-radius: 20px; padding: 6px 14px; font-size: 10px; font-weight: bold; }
    .pill.total { background: #e8f3ec; color: #267a47; }
    .pill.plainte { background: #fdeceb; color: #c44030; }
    .pill.suggestion { background: #eafaf1; color: #267a47; }
    .pill.commentaire { background: #eef3f0; color: #4a6555; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
    th { background: #267a47; color: #fff; text-align: left; padding: 7px 8px; font-size: 9.5px; text-transform: uppercase; letter-spacing: .4px; }
    td { padding: 8px; border-bottom: 1px solid #e8efe9; vertical-align: top; font-size: 10px; }
    tr:nth-child(even) td { background: #f7faf8; }
    .type-plainte { color: #c44030; font-weight: bold; }
    .type-suggestion { color: #267a47; font-weight: bold; }
    .type-commentaire { color: #4a6555; font-weight: bold; }
    .statut { font-size: 9px; font-weight: bold; padding: 2px 8px; border-radius: 10px; }
    .st-attente { background: #fdf3e3; color: #c07b28; }
    .st-lu { background: #e8f0fa; color: #2e7fbb; }
    .st-traite { background: #e8f3ec; color: #267a47; }
    .msg { line-height: 1.5; max-width: 260px; }
    .empty { padding: 24px; text-align: center; color: #8aaa95; }
    .footer { margin-top: 28px; font-size: 9.5px; color: #8aaa95; text-align: center; border-top: 1px solid #e8efe9; padding-top: 10px; }
    .sign { margin-top: 40px; text-align: right; font-size: 11px; color: #1a2d22; }
    .sign .role { font-size: 9px; text-transform: uppercase; color: #4a6555; margin-top: 2px; }
    .page-break { page-break-before: always; }
</style>
</head>
<body>
<div class="page">

    {{-- ═══ En-tête officiel : image centrée + sous-titre ═══ --}}
    <div class="header header-center">
        {{-- Image : renseignez le nom du fichier dans config/sama.php
             (fichier déposé dans public/assets/img/). Si absent, repli texte. --}}
        @if(isset($logoPdf) && $logoPdf)
        <img src="{{ $logoPdf }}" alt="Logo" class="logo-img">
        @else
            <div class="logo">SAMA <span>TERRITOIRE</span></div>
        @endif
        <div class="org">Plateforme d'information et de gestion du territoire · République du Sénégal</div>
    </div>

    <div class="ref-line">
        N° {{ $commentaires->first()->id }}-{{ date('Y') }} · Kaolack, le {{ $date->format('d/m/Y') }}
    </div>

    <h1 class="title">Recueil des suggestions et plaintes</h1>
    <p class="subtitle">Document établi à partir de la plateforme SAMA TERRITOIRE pour transmission aux autorités concernées.</p>

    {{-- ═══ Infos du document ═══ --}}
    <div class="info-block">
        <div class="info-grid">
            <div class="info-item">
                <b>À l'attention de</b>
                <div class="dest">{{ $destinataire ?: '—' }}</div>
            </div>
            <div class="info-item">
                <b>Date d'édition</b>
                {{ $date->format('d/m/Y à H:i') }}
            </div>
            <div class="info-item">
                <b>Nombre total</b>
                {{ $commentaires->count() }}  plainte(s)
            </div>
        </div>
    </div>

    {{-- ═══ Récapitulatif par type ═══ --}}
    <div class="recap">
        <span class="pill total">{{ $commentaires->count() }} au total</span>
        <span class="pill plainte">{{ $recap['plainte'] }} plainte(s)</span>
        <span class="pill suggestion">{{ $recap['suggestion'] }} suggestion(s)</span>
        <span class="pill commentaire">{{ $recap['commentaire'] }} commentaire(s)</span>
    </div>

    {{-- ═══ Liste détaillée ═══ --}}
    <table>
        <thead>
            <tr>
                <th style="width:22px;">#</th>
                <th>Auteur</th>
                <th style="width:90px;">Type</th>
                {{-- <th style="width:70px;">Statut</th> --}}
                <th>Message</th>
                <th style="width:70px;">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($commentaires as $i => $c)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                    <b>{{ $c->nom ?:  'Anonyme' }}</b><br>
                    {{-- <span style="color:#8aaa95; font-size:9px;">{{ $c->email }}</span> --}}
                </td>
                <td>
                    <span class="type-{{ $c->type }}">
                        {{ ucfirst($c->type) }}
                    </span>
                </td>
                {{-- <td>
                    @php
                        $st = match ($c->statut) {
                            'en_attente' => ['st-attente', 'En attente'],
                            'lu'         => ['st-lu', 'Lu'],
                            'traite'     => ['st-traite', 'Traité'],
                            default      => ['', $c->statut],
                        };
                    @endphp
                    <span class="statut {{ $st[0] }}">{{ $st[1] }}</span>
                </td> --}}
                <td class="msg">{{ $c->message }}</td>
                <td>{{ $c->created_at->format('d/m/Y') }}<br><span style="color:#8aaa95; font-size:9px;">{{ $c->created_at->format('H:i') }}</span></td>
            </tr>
            @empty
            <tr><td colspan="6" class="empty">Aucun commentaire sélectionné.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- ═══ Signature ═══ --}}
    <div class="sign">
        <div>Agence Regional de Developement de kaolack</div>
        <div class="role">Plateforme SAMA TERRITOIRE</div>
    </div>

    <div class="footer">
        Ce document a été généré automatiquement par la plateforme SAMA TERRITOIRE.<br>
        Au service de la population et du développement du territoire.
    </div>

</div>
</body>
</html>
