<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

use App\Models\Person;
use App\Jobs\SendPersonDailyMeetingsEmailJob;

class DailyEmailSenderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:send-daily-meetings-email {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily meetings email to all users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting daily email sender...');

        $date = $this->getDate();
        $people = $this->getInternalPeopleWithEventsAtDate($date);

        if ($people->isEmpty()) {
            $this->info('No people found with events at date ' . $date->toDateString());
            return Command::SUCCESS;
        }

        $this->info('Sending emails to ' . $people->count() . ' people with events at date ' . $date->toDateString());

        $people->each(function ($person) use ($date) {
            $this->info('Sending daily email to ' . $person->name);
            dispatch(new SendPersonDailyMeetingsEmailJob($person->id, $date));
        });

        $this->info('Daily email sender finished');

        return Command::SUCCESS;
    }

    private function getDate(): Carbon
    {
        $date = $this->option('date');

        if ($date) {
            return Carbon::parse($date);
        }

        return Carbon::now();
    }

    private function getInternalPeopleWithEventsAtDate(Carbon $date): Collection
    {
        return Person::internal()->withEventsAtDate($date)->get();
    }
}
