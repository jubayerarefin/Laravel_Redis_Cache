<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    @vite(['resources/js/app.js'])

</head>
<body>
<div class="container">
    <h1>People</h1>
    @if(count($people->toArray()['data']) > 0)
        @foreach ($people as $person)
            <li>{{ $person->name}} {{$person->dob}}</li>
        @endforeach
        {{ $people->onEachSide(2)->links('vendor.pagination.bootstrap-5') }}
    @endif
</div>
</body>
</html>
