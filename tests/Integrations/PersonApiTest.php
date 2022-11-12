<?php

namespace Tests\Commands;

use Tests\TestCase;
use App\Integrations\PersonApi;

use Illuminate\Support\Facades\Http;

class PersonApiTest extends TestCase
{

  public function test_it_calls_the_http_client_with_the_expected_params()
  {
    Http::shouldReceive('get')
      ->andReturn(
        [
          'name' => 'Stephan',
          'email' => ''
        ]
      );

    $api = new PersonApi();

    $api->getPersonInfo('test@email.com');

    Http::assertSent(function ($request) {
      return ($request->url() === 'https://app.usergems.com/api/hiring/calendar-challenge/person/test@email.com' &&
        $request->method() === 'GET'
      );
    });
  }
}
