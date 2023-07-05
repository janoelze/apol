@extends('layouts.app')

@section('title', 'Subscriptions')

@section('content')
    <div class="sections">
        <div class="generic-list">
            @foreach (Settings::getUserPreferences() as $preference)
                <div class="tint-bg-down-2 tint-fg-up-58">
                    <span>{{ $preference['label'] }}</span>
                    @include('partials.switch', ['isEnabled' => $preference['value'], 'preferenceId' => $preference['id']])
                </div>
            @endforeach
        </div>
        <div class="generic-list">
            <pre>{{ json_encode(Settings::getUserPreferences(), JSON_PRETTY_PRINT) }}</pre>
        </div>
        <div class="generic-list">
            <pre>{{ json_encode(Settings::getUserPreference("blur_nsfw"), JSON_PRETTY_PRINT) }}</pre>
        </div>
        <div class="generic-list">
            <a 
            href="/settings/revert"
            hx-get="/settings/revert"
            hx-trigger="click"
            hx-target="#body"
            hx-swap="outerHTML"
            >Revert to default settings</a>
        </div>
    </div>
@endsection