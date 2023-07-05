@extends('layouts.app')

@section('title', 'Subscriptions')

@section('content')
    <div class="sections">
        <div class="generic-list">
            <div>
                <form id="settings-form" hx-post="{{ Helpers::get_base_url() }}/settings" hx-target="#result" hx-trigger="change" hx-select="#settings-form">
                    @foreach($settings as $setting)
                        @if($setting['type'] == 'boolean')
                            <label for="{{ $setting['id'] }}">{{ $setting['label'] }}</label>
                            @include('partials.switch', ['setting' => $setting])
                        @endif
                        @if($setting['type'] == 'select')
                            <label for="{{ $setting['id'] }}">{{ $setting['label'] }}</label>
                            <select name="{{ $setting['id'] }}" id="{{ $setting['id'] }}">
                                @foreach ($setting['options'] as $opt)
                                    <option {{ $opt['value'] == $setting['value'] ? 'selected' : '' }} value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                                @endforeach
                            </select>
                        @endif
                    @endforeach
                    {{-- <input type="submit" value="Save"> --}}
                </form>
                <div id="result"></div>
            </div>
        </div>
    </div>
@endsection