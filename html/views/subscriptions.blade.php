@extends('layouts.app')
 
@section('title', $page_title)
 
@section('content')
    <div class="sections" id="subscriptions">

        <div class="bg-gray-900 rounded-lg">
            <ul class="">
                @foreach ($subscriptions as $subreddit)
                    <li class="flex items-center justify-between px-5 py-4">
                        <a href="{{ Helpers::get_base_url() }}/r/{{ $subreddit }}" class="text-md">{{ $subreddit }}</a>
                        <button class="" hx-delete="/subscriptions?s={{ $subreddit }}" hx-target="#subscriptions" hx-swap="outerHTML" hx-select="#subscriptions">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <h3 class="tint-fg-up-35 list-header">Subscriptions</h3>

        @if(count($subscriptions))
            <div class="generic-list">
                @foreach ($subscriptions as $subreddit)
                    <a class="tint-bg-down-2 tint-fg-up-58" href="{{ Helpers::get_base_url() }}/r/{{ $subreddit }}">/r/{{ $subreddit }}</a>
                @endforeach
            </div>
        @else
            {!! var_dump($_POST) !!}
            <div class="section empty tint-bg-down-2 tint-fg-up-20">{{ rand(0,1000) }} You are not subscribed to any subreddits.</div>
        @endif

        <div class="form-wrapper tint-bg-down-2">
            <form class="subscription-form" hx-post="/subscriptions" hx-select="#subscriptions" hx-target="#subscriptions" hx-swap="outerHTML">
                <div class="input-field fill">
                    <span>r/</span>
                    <input type="text" name="subreddit" placeholder="Subreddit" required />
                </div>
                <div class="input-field">
                    <button type="submit">Subscribe</button>
                </div>
            </form>
        </div>

        <h3 class="tint-fg-up-35 list-header">Suggested subreddits</h3>

        @if(count($default_subreddits))
            <div class="generic-list">
                @foreach ($default_subreddits as $subreddit)
                    <a class="tint-bg-down-2 tint-fg-up-58" href="{{ Helpers::get_base_url() }}/r/{{ $subreddit }}">/r/{{ $subreddit }}</a>
                @endforeach
            </div>
        @else
            <div class="section empty tint-bg-down-2 tint-fg-up-20">You are not subscribed to any subreddits.</div>
        @endif

        <div class="section">
            {{-- <form hx-post="/subscriptions" hx-target="body" hx-swap="outerHTML">
                <textarea name="subscriptions" required rows="5"></textarea>
                <button type="submit">Subscribe</button>
            </form> --}}
        </div>

    </div>
@endsection