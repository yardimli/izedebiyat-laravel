<?php
// Compile and lint templates without rendering them or accessing application data.
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$compiler = $app->make('blade.compiler');
$temporary = tempnam(storage_path('framework/views'), 'blade-lint-');
$failed = 0;
$count = 0;
try {
    foreach (Illuminate\Support\Facades\File::allFiles(resource_path('views')) as $file) {
        if (!str_ends_with($file->getFilename(), '.blade.php')) continue;
        $count++;
        try {
            file_put_contents($temporary, $compiler->compileString($file->getContents()));
            exec(escapeshellarg(PHP_BINARY).' -l '.escapeshellarg($temporary).' 2>&1', $output, $status);
            if ($status !== 0) {
                echo $file->getRelativePathname().': '.implode("\n", $output)."\n";
                $failed++;
            }
            $output = [];
        } catch (Throwable $error) {
            echo $file->getRelativePathname().': '.$error->getMessage()."\n";
            $failed++;
        }
    }
} finally {
    unlink($temporary);
}
printf("Checked %d Blade templates; %d failed.\n", $count, $failed);
exit($failed ? 1 : 0);
