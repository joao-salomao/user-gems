<?php

namespace App\Integrations;

use Illuminate\Support\Facades\Http;

class CalendarApi
{
  public function __construct(
    private string $token
  ) {
  }

  public function getEvents(): array
  {
    $events = [];
    $currentPage = 1;

    while (true) {
      $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token])
        ->get('https://app.usergems.com/api/hiring/calendar-challenge/events', [
          'page' => $currentPage,
        ]);

      if ($response->failed()) {
        throw new \Exception('Failed to get events from calendar API');
      }

      $pageEvents = $response->json('data');

      if (empty($pageEvents)) {
        break;
      }

      array_push($events, ...$pageEvents);

      $currentPage++;
    }

    return $events;
  }
}
