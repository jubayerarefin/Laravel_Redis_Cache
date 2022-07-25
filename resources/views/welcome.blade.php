@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Browse People') }}</div>
                    <div class="card-body">
                        <a class="link-primary"
                           href="{{ route('people',['month' => 1,'year' => 2019]) }}">
                            Show Peoples List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
