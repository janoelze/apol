@extends('layouts.app')

@section('title', 'Subscriptions')

@section('content')
    <div class="sections" id="settings-container">
        <div class="generic-list">
            @foreach (Settings::getUserPreferences() as $settingName => $value)
                <div class="tint-bg-down-2 tint-fg-up-58">
                    <span>{{ $settingName }}</span>
                    @include('partials.switch', ['isEnabled' => $value, 'settingName' => $settingName])
                </div>
            @endforeach
        </div>
        <div class="generic-list">
            <pre>{{ json_encode(Settings::getUserPreferences(), JSON_PRETTY_PRINT) }}
            </pre>
        </div>
    </div>
@endsection
