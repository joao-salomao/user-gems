<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSS only -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
  <!-- JavaScript Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>

  <style>
    a {
      text-decoration: none;
    }

    .participant-card {
      display: flex;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      margin-bottom: 10px;
    }

    .text-bold {
      font-weight: bold;
    }

    .text-primary {
      color: rgb(42, 201, 188) !important;
    }

    .text-icon {
      font-size: 14px;
    }

    .text-underscore {
      text-decoration: underline;
    }
  </style>
</head>

<body class="text-muted p-3">
  <h1 class="text-center text-primary">Your Morning Update</h1>
  @foreach ($events as $event)
  <div class="card card-body shadow-sm mb-3">
    <div class="mb-2">
      <span class="text-bold text-primary">{{ $event->start_at->format('h:m A') }}</span>
      <span class="text-bold"> - {{ $event->end_at->format('h:m A') }}</span>
      <span class="text-bold">| {{$event->title }}</span>
      <span>| ({{ $event->start_at->diffInMinutes($event->end_at) }} min)</span>
    </div>
    <div class="mb-2">
      <span>Joining from UserGems:</span>
      @foreach ($event->participants as $participant)
      <span class="text-bold">
        {{ $participant->person->name }} <span class="text-icon">{{ $participant->has_accepted ? '✅' : '❌' }}</span>
        {{ $loop->last ? '' : '|' }}
      </span>
      @endforeach
    </div>
    <div class="mb-2">
      <span class="text-bold text-underscore">{{ $event->company->name }}</span>
      <a href="{{ $event->company->linkedin_url }}">
        <img height="16px" src="{{ asset('icons/linkedin.png') }}" />
      </a>
      <span class="ms-1">
        {{ $event->company->employees }}
        <img height="22px" src="{{ asset('icons/group.svg') }}" />
      </span>
    </div>
    <div>
      @foreach ($event->externalParticipants as $participant)
      <div class="d-flex mb-2">
        <img class="me-2 my-auto" height="50" width="50" src="{{ $participant->person->avatar }}">
        <div>
          <div>
            <span class="text-bold text-primary">{{ $participant->person->name }}</span>
            <a href="{{ $participant->person->linkedin_url }}">
              <img height="16px" src="{{ asset('icons/linkedin.png') }}" />
            </a>
            <span class="text-icon">{{ $participant->has_accepted ? '✅' : '❌' }}</span>
          </div>
          <span class="text-bold">{{ $participant->person->role }}</span>
          <div>
            <span class="text-bold">{{ ordinal_number($participant->person->getMeetingsCountByInternalPeople(onlyPersonId: $person->id)->first()->meetings_count) }}</span>
            <span>Meeting</span>
            @if(count($participant->person->getMeetingsCountByInternalPeople(personIdToExclude: $person->id)) > 0)
            | Met with
            @endif
            @foreach ($participant->person->getMeetingsCountByInternalPeople(personIdToExclude: $person->id) as $personMeetingsCount)
            <span>
              {{ $personMeetingsCount->name }} ({{ $personMeetingsCount->meetings_count }}x)
              {{ $loop->last ? '' : ' & ' }}
            </span>
            @endforeach
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endforeach
</body>

</html>