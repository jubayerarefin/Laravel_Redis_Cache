<html lang="">
<body>
<h1>People</h1>
<div class="container">
    <ul>
        @foreach ($people as $person)
            <li>{{ $person->name}} {{$person->dob}}</li>
        @endforeach
    </ul>
</div>
{{ $people->links() }}
</body>
</html>
