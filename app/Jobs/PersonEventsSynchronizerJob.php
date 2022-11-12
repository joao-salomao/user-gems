<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

use App\Integrations\CalendarApi;
use App\Integrations\PersonApi;
use App\Models\Person;
use App\Models\Event;
use App\Models\Company;

class PersonEventsSynchronizerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private int $personId)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::transaction(function () {
            $person = Person::find($this->personId);
            $this->syncEvents($person);
        });
    }

    private function syncEvents(Person $person)
    {
        $events = $this->getPersonEventsFromCalendarApi($person);

        foreach ($events as $eventData) {
            $event = $this->updateEvent($eventData);
            $event->participants()->delete();
            $this->addPeopleToEvent($event, $eventData['accepted'], 'accepted');
            $this->addPeopleToEvent($event, $eventData['rejected'], 'rejected');
        };
    }

    private function getPersonEventsFromCalendarApi(Person $person): array
    {
        $calendarApi = new CalendarApi($person->calendar_api_token);
        return $calendarApi->getEvents();
    }

    private function updateEvent(array $eventData): Event
    {
        return Event::updateOrCreate(
            ['calendar_api_id' => $eventData['id']],
            [
                'title' => $eventData['title'],
                'start_at' => $eventData['start'],
                'end_at' => $eventData['end'],
                'last_updated' => $eventData['changed']
            ]
        );
    }

    private function addPeopleToEvent(Event $event, array $participantEmails, string $status): void
    {
        foreach ($participantEmails as $participantEmail) {
            $person = $this->addPersonToEvent($event, $participantEmail, $status);
            $this->updatePersonInfo($person);
        }
    }

    private function addPersonToEvent(Event $event, string $participantEmail, string $status): Person
    {
        $person = Person::firstOrCreate([
            'email' => $participantEmail,
        ]);

        $event->participants()->create([
            'person_id' => $person->id,
            'status' => $status,
        ]);

        return $person;
    }


    private function updatePersonInfo(Person $person)
    {
        if ($person->is_internal) return;

        if ($person->last_updated?->greaterThan(now()->subMonth())) return;

        $personApi = new PersonApi;
        $personInfo = $personApi->getPersonInfo($person->email);

        $company = Company::firstOrCreate($personInfo['company']);

        $person->update([
            'company_id' => $company->id,
            'name' => "{$personInfo['first_name']} {$personInfo['last_name']}",
            'avatar' => $personInfo['avatar'],
            'role' => $personInfo['title'],
            'linkedin_url' => $personInfo['linkedin_url'],
            'last_updated' => now(),
        ]);
    }
}
