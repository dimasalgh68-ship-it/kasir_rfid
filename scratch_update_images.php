<?php
use App\Models\MenuItem;
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$m1 = MenuItem::where('name', 'like', '%Nasi Goreng%')->first();
if($m1) $m1->update(['image' => 'menu/nasi_goreng.png']);

$m2 = MenuItem::where('name', 'like', '%Es Teh%')->first();
if($m2) $m2->update(['image' => 'menu/es_teh.png']);

$m3 = MenuItem::where('name', 'like', '%Bakso%')->first();
if($m3) $m3->update(['image' => 'menu/bakso.png']);

echo "Updated menu images successfully.";
