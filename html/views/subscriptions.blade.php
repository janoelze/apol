@extends('layouts.app')
 
@section('title', $page_title)
 
@section('content')
    <div class="sections" id="subscriptions">

        <h3 class="tint-fg-up-25 list-header">Subscribed Subreddits</h3>

        


        @if(count($subscriptions))
            <div class="rounded-lg">
                <ul class="">
                    @foreach ($subscriptions as $subreddit)
                        <li class="flex items-center justify-between my-1 px-5 py-4 tint-bg-down-2 rounded-lg">
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
        @else
            <div class="rounded-lg">
                <ul class="">
                    <li class="flex items-center justify-between px-5 py-4 tint-bg-down-2">
                        You are not subscribed to any subreddits.
                    </li>
                </ul>
            </div>
            {{-- {!! var_dump($_POST) !!} --}}
            {{-- <div class="section empty tint-bg-down-2 tint-fg-up-20">{{ rand(0,1000) }} You are not subscribed to any subreddits.</div> --}}
        @endif

        {{-- <div class="form-wrapper tint-bg-down-2">
            <form class="subscription-form" hx-post="/subscriptions" hx-select="#subscriptions" hx-target="#subscriptions" hx-swap="outerHTML">
                <div class="input-field fill">
                    <span>r/</span>
                    <input type="text" name="subreddit" placeholder="Subreddit" required />
                </div>
                <div class="input-field">
                    <button type="submit">Subscribe</button>
                </div>
            </form>
        </div> --}}

        <h3 class="tint-fg-up-25 list-header">Default Subreddits</h3>

        @if(count($default_subreddits))
            <div class="rounded-lg">
                <ul class="">
                    @foreach ($default_subreddits as $subreddit)
                        <li class="flex items-center justify-between my-1 px-5 py-4 tint-bg-down-2 rounded-lg">
                            <a href="{{ Helpers::get_base_url() }}/r/{{ $subreddit }}" class="text-md">{{ $subreddit }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endsection