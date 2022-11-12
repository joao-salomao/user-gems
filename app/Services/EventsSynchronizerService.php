<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Person;
use App\Integrations\CalendarApi;

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
                    $person = Person::firstOrCreate([
                        'email' => $participantEmail,
                    ]);

                    $event->participants()->create([
                        'person_id' => $person->id,
                        'status' => 'accepted',
                    ]);
                }

                $rejectedParticipantEmails = $eventData['rejected'];
                foreach ($rejectedParticipantEmails as $participantEmail) {
                    $person = Person::firstOrCreate([
                        'email' => $participantEmail,
                    ]);

                    $event->participants()->create([
                        'person_id' => $person->id,
                        'status' => 'rejected',
                    ]);
                }
            };
        });
    }
}
