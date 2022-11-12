<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Person;
use App\Models\Event;
use App\Models\Company;
use App\Integrations\CalendarApi;
use App\Integrations\PersonApi;

class EventsSynchronizerService
{
    public function synchronize()
    {
        $users = User::all();

        $users->each(function ($user) {
            $this->synchronizeEventsForUser($user);
        });
    }

    private function synchronizeEventsForUser(User $user)
    {
        DB::transaction(function () use ($user) {
            $calendarApi = new CalendarApi($user->calendar_api_token);
            $events = $calendarApi->getEvents();

            $user->events()->delete();

            foreach ($events as $eventData) {
                $event = $user->events()->create([
                    'calendar_api_id' => $eventData['id'],
                    'title' => $eventData['title'],
                    'start_at' => $eventData['start'],
                    'end_at' => $eventData['end'],
                    'last_updated' => $eventData['changed'],
                ]);

                $acceptedParticipantEmails = $eventData['accepted'];
                foreach ($acceptedParticipantEmails as $participantEmail) {
                    $person = $this->addPersonToEvent($event, $participantEmail, 'accepted');
                    $this->updatePersonInfo($person);
                }

                $rejectedParticipantEmails = $eventData['rejected'];
                foreach ($rejectedParticipantEmails as $participantEmail) {
                    $person = $this->addPersonToEvent($event, $participantEmail, 'accepted');
                    $this->updatePersonInfo($person);
                }
            };
        });
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
        print("Updating person info for $person->email. Last update $person->last_updated \n");

        if ($person->last_updated?->greaterThan(now()->subMonth())) {
            return;
        }

        $personApi = new PersonApi;
        $personInfo = $personApi->getPersonInfo($person->email);

        if (empty($personInfo)) {
            return;
        }

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
