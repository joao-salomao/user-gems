<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\EventsSynchronizerService;

class EventsSynchronizerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:synchronize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize all users events';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        (new EventsSynchronizerService)->synchronize();
        return Command::SUCCESS;
    }
}
