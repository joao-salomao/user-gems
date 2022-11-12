<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;
use App\Mail\DailyMeetingsMail;

use Illuminate\Support\Facades\Mail;

class DailyEmailSenderService
{
  public function sendUsersDailyEmail(Carbon $currentDay)
  {
    $users = User::all();

    foreach ($users as $user) {
      $events = $user->events()
        ->with('participants.person')
        ->where('start_at', '>=', $currentDay->startOfDay()->toDateTimeString())
        ->where('start_at', '<=', $currentDay->endOfDay()->toDateTimeString())
        ->get();

      Mail::to($user)->send(new DailyMeetingsMail($user, $events));
    }
  }
}
