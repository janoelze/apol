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
            <div>
                <form id="settings-form" hx-put="{{ Helpers::get_base_url() }}/settings" class="autosave" hx-trigger="change" hx-select="#settings-form">
                    <select name="theme" id="theme">
                        <option value="light">Light</option>
                        <option value="dark">Dark</option>
                    </select>
                </form>
            </div>
        </div>
    </div>
@endsection