<?php

namespace App\Integrations;

use Illuminate\Support\Facades\Http;

class PersonApi
{

  public function getPersonInfo(string $email): array
  {
    $url = 'https://app.usergems.com/api/hiring/calendar-challenge/person/' . $email;
    $token = env('USER_GEMS_HIRING_API_TOKEN');

    $response = Http::withToken($token)->get($url);

    if ($response->failed()) {
      throw new \Exception('Error fetching person info');
    }

    return $response->json();
  }
}
