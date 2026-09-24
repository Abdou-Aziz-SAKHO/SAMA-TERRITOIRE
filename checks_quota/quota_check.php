<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo 'eclipse cle1 = '.var_export(Cache::get('chatbot.cle1.eclipsee', false), true).PHP_EOL;
echo 'eclipse cle2 = '.var_export(Cache::get('chatbot.cle2.eclipsee', false), true).PHP_EOL;
echo 'fournisseur = '.config('services.chatbot.provider').PHP_EOL;
echo 'cles = '.json_encode(array_map(fn ($c) => '...'.substr((string) $c, -8), config('services.gemini.keys'))).PHP_EOL;
echo 'modele = '.config('services.gemini.model').PHP_EOL;
echo 'base_url = '.config('services.gemini.base_url').PHP_EOL;
echo 'timeout = '.config('services.gemini.timeout').PHP_EOL;
echo PHP_EOL;

$url = rtrim(config('services.gemini.base_url'), '/').'/chat/completions';

foreach (config('services.gemini.keys') as $i => $cle) {
    $debut = microtime(true);
    try {
        $r = Http::withToken($cle)->asJson()->timeout(15)->post($url, [
            'model' => config('services.gemini.model'),
            'messages' => [['role' => 'user', 'content' => 'Dis juste OK']],
            'max_tokens' => 4,
        ]);
        printf("cle%d -> status %d en %.1fs | %s\n", $i + 1, $r->status(), microtime(true) - $debut, substr($r->body(), 0, 260));
    } catch (\Throwable $e) {
        printf("cle%d -> EXCEPTION en %.1fs | %s\n", $i + 1, microtime(true) - $debut, $e->getMessage());
    }
    echo PHP_EOL;
}
