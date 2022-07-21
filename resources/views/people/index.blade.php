@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <table class="table-fit table-striped">
                    <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Date of Birth</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($people->toArray()['data']) > 0)
                        @foreach ($people as $person)
                            <tr>
                                <td>{{ $person->id}}</td>
                                <td>{{ $person->name}}</td>
                                <td>{{ $person->email}}</td>
                                <td> {{$person->dob}}</td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            {{ $people->onEachSide(2)->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
@endsection
