@extends('layouts.app')
 
@section('title', 'Subscriptions')
 
@section('content')
    <div class="sections">
        <div class="generic-list">
            @foreach(Helpers::get_settings() as $setting)
                <a>
                    <span>{{ $setting['name'] }}</span>
                     @include('partials.switch', ['isEnabled' => $setting['defaultValue']])
                </a>
               
            @endforeach
        </div>
    </div>
@endsection