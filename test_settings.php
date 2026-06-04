<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$settings = DB::table('admin_settings')->get();
foreach($settings as $s) {
    if(strpos(strtolower($s->value), 'favourite') !== false || strpos(strtolower($s->value), 'favorite') !== false) {
        echo $s->key . PHP_EOL;
    }
}
echo "Done\n";
