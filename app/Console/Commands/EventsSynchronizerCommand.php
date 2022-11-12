<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Person;
use App\Jobs\PersonEventsSynchronizerJob;

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
        $this->info('Starting events Synchronizer...');

        $people = Person::withCalendarApiToken()->get();

        if ($people->isEmpty()) {
            $this->info('No people found with calendar api token');
            return Command::SUCCESS;
        }

        $this->info('Synchronizing events for ' . $people->count() . ' people');

        $people->each(function ($person) {
            $this->info('Synchronizing events for ' . $person->name);
            dispatch(new PersonEventsSynchronizerJob($person->id));
        });

        $this->info('Events synchronized!');

        return Command::SUCCESS;
    }
}
