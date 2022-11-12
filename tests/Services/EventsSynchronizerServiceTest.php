<?php

namespace Tests\Commands;

use Tests\TestCase;
use App\Models\User;
use App\Services\EventsSynchronizerService;

use Illuminate\Foundation\Testing\RefreshDatabase;

class EventsSynchronizerServiceTest extends TestCase
{
  use RefreshDatabase;


  public function test_it_synchronizes_all_users_events()
  {
    User::factory()->create([
      'name' => 'Stephan',
      'email' => 'stephan@usergems.com',
      'calendar_api_token' => '7S$16U^FmxkdV!1b'
    ]);

    $service = new EventsSynchronizerService();
    $service->synchronize();

    $this->assertDatabaseCount('events', 14);
    $this->assertDatabaseCount('event_participants', 50);
  }
}
