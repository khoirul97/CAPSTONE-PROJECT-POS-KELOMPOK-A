/* <?php

use Illuminate\Foundation\Application;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$commands = [
    'config:clear',
    'cache:clear',
    'route:clear',
    'view:clear',
    'event:clear',
];

echo '<pre>';

foreach ($commands as $command) {
    echo "Menjalankan: php artisan {$command}\n";

    try {
        $status = $kernel->call($command);
        echo $kernel->output();
        echo "Status: {$status}\n\n";
    } catch (\Throwable $e) {
        echo "ERROR: {$e->getMessage()}\n\n";
    }
}

echo "SELESAI.\n";
echo '</pre>';

*/