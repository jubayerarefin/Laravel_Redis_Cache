@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <form class="row row-cols-md-auto" action="{{ route('people') }}" method="GET">
                <div class="col-2">
                    <select class="form-select" name="month">
                        <option value="">Select</option>
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March</option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">August</option>
                        <option value="9">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>
                <div class="col-1">
                    <select class="form-select" name="year">
                        <option value="">Select</option>
                        @php( $years = range(1900,2019))
                        @foreach($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
                <div class="col-5">
                    @if(session('message'))
                        <div class="alert alert-warning">
                            {!! session('message') !!}
                        </div>
                    @endif
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Date of Birth</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($paginatedData->toArray()['data']) > 0)
                        @foreach ($paginatedData as $person)
                            <tr>
                                <td>{{ $person->id}}</td>
                                <td>{{ $person->name}}</td>
                                <td>{{ $person->email}}</td>
                                <td>{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$person->dob)->toDayDateTimeString()}}</td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            {{ $paginatedData->onEachSide(2)->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
@endsection
