@extends('layouts.app')
@section('content')
    <messenger 
        :current-user-id="{{ auth()->id() ?? 1 }}"
        pusher-key="{{ env('PUSHER_APP_KEY', 'your_key') }}"
        pusher-cluster="{{ env('PUSHER_APP_CLUSTER', 'eu') }}"
    ></messenger>
@endsection