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
    .participant-card {
      display: flex;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      margin-bottom: 10px;
    }
  </style>
</head>

<body class="p-3">
  <h1 class="text-center">Your Morning Update</h1>
  @foreach ($events as $event)
  <div class="card card-body shadow-sm mb-3">
    <div class="mb-2">
      <span class="font-weight-bold">{{ $event->start_at->format('h:m A') }}</span>
      <span> - {{ $event->end_at->format('h:m A') }}</span>
      <span>| {{$event->title }}</span>
      <span>| {{ $event->start_at->diffInMinutes($event->end_at) }} min</span>
    </div>
    <div>
      @foreach ($event->participants as $participant)
      @if ($participant->person->hasInfos())
      <div class="d-flex mb-2">
        <img class="me-2" height="50" width="50" src="{{ $participant->person->avatar }}">
        <div>
          <div class="">
            <span>{{ $participant->person->name }}</span>
            <a href="{{ $participant->person->linkedin_url }}" target="_blank">LinkedIn</a>
            <span>{{ $participant->has_accepted ? 'accepted': 'rejected' }}</span>
          </div>
          <p>{{ $participant->person->role }}</p>
        </div>
      </div>
      @endif
      @endforeach
    </div>
  </div>
  @endforeach
</body>

</html>