<?php

namespace App\Console\Commands;

use App\Writer\Models\AiCall;
use App\Writer\Services\OpenRouter;
use Illuminate\Console\Command;

class ReconcileWriterAiUsage extends Command
{
    protected $signature = 'writer:reconcile-usage';

    protected $description = 'Reconcile pending OpenRouter generation costs without repeating paid requests';

    public function handle(OpenRouter $router): int
    {
        $settled = 0;
        AiCall::whereNull('cost')->whereNotNull('provider_id')->chunkById(100, function ($calls) use ($router, &$settled) {
            foreach ($calls as $call) {
                if ($router->reconcile($call)) {
                    $settled++;
                }
            }
        });
        $this->info("Reconciled {$settled} requests. Unidentified or unavailable charges remain reserved.");

        return self::SUCCESS;
    }
}
