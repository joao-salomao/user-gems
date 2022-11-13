<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Collection;

use App\Mail\DailyMeetingsMail;
use App\Models\Person;

class SendPersonDailyMeetingsEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Person $person;
    private Carbon $date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        int $personId,
        Carbon $date
    ) {
        $this->person = Person::find($personId);
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $events = $this->getEventsAtDate();

        if ($events->isEmpty()) return;

        $this->sendEmail($events);
    }

    private function getEventsAtDate(): Collection
    {
        return $this->person->events()
            ->atDate($this->date)
            ->with('internalParticipants.person', 'externalParticipants.person')
            ->get();
    }

    private function sendEmail(Collection $events)
    {
        Mail::to($this->person)->send(new DailyMeetingsMail($this->person, $events));
    }
}
