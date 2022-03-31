<?php

namespace Flarone\Searchable\Console\Commands;

use Illuminate\Console\Command;
use Flarone\Searchable\Traits\Searchable;

class GenerateSearchIndex extends Command
{
    use Searchable;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a fresh search index';
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->generateSearchIndex();
    }
}