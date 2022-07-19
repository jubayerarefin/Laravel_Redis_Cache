<html lang="">
<body>
<h1>People</h1>
<ul>
    @foreach ($people as $person)
        <li>{{ $person->name}} {{$person->dob}}</li>
    @endforeach
</ul>
</body>
</html>
