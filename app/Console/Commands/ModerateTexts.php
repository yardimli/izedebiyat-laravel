<?php

namespace App\Console\Commands;

use App\Helpers\MyHelper;
use Illuminate\Console\Command;

class ModerateTexts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:moderate-texts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
	    MyHelper::returnIsHarmful();
	    MyHelper::returnReligiousReason();
	    MyHelper::returnKeywords();
	    MyHelper::returnKeywords();
	    MyHelper::updateArticleTable();
	    MyHelper::returnMarkdown();
    }
}
