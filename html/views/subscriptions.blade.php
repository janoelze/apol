@extends('layouts.app')
 
@section('title', 'Subscriptions')
 
@section('content')
    <div class="tab-title">Subscriptions</div>
    <div class="sections">
        @if(count($arr))
            <div class="generic-list">
                @foreach ($arr as $subreddit)
                    <a class="tint-bg-down-2 tint-fg-up-58" href="{{ Helpers::get_base_url() }}/r/{{ $subreddit }}">/r/{{ $subreddit }}</a>
                @endforeach
            </div>
        @else
            <div class="section empty">You are not subscribed to any subreddits.</div>
        @endif
        <div class="section">
            {{-- <form hx-post="/subscriptions" hx-target="body" hx-swap="outerHTML">
                <textarea name="subscriptions" required rows="5"></textarea>
                <button type="submit">Subscribe</button>
            </form> --}}
        </div>
    </div>
@endsection