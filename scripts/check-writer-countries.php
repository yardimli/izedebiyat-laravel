<?php
// Run with: php -d disable_classes=Locale scripts/check-writer-countries.php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
foreach (['en', 'tr'] as $locale) {
    $app->setLocale($locale);
    $countries = app(App\Writer\Http\Controllers\NameController::class)->countries();
    $turkey = collect($countries)->firstWhere('code', 'TR');
    if (!$turkey || $turkey['name'] === 'TR') throw new RuntimeException('Country names failed for '.$locale);
    echo $locale.': '.count($countries).' countries; TR = '.$turkey['name'].PHP_EOL;
}
