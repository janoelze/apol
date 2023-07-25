@extends('layouts.app')

@section('title', $page_title)

@section('content')
    <div class="sections" id="subscriptions">

        <h3 class="tint-fg-up-40 list-header">Subscriptions</h3>

        @if (count($subscriptions))
            <div class="rounded-lg">
                <ul class="">
                    @foreach ($subscriptions as $subreddit)
                        <li class="flex items-center justify-between my-1 px-5 py-4 tint-bg-down-2 rounded-lg">
                            <a href="{{ Helpers::get_base_url() }}/r/{{ $subreddit }}"
                                class="text-md">{{ $subreddit }}</a>
                            <button class="tint-fg-up-25"
                                hx-delete="{{ Helpers::get_base_url() }}/subscriptions?s={{ $subreddit }}"
                                hx-target="#subscriptions" hx-swap="outerHTML" hx-select="#subscriptions">
                                Remove
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
        @endif

        <h3 class="tint-fg-up-40 list-header">Manually enter Subreddit</h3>
        <form hx-post="{{ Helpers::get_base_url() }}/subscriptions" hx-target="#subscriptions" hx-swap="outerHTML"
            hx-select="#subscriptions">
            <input type="text" name="subreddit" placeholder="Enter subreddit" required>
            <button type="submit" class="tint-fg-up-25">Add</button>
        </form>

        <h3 class="tint-fg-up-40 list-header">Defaults</h3>

        @if (count($default_subreddits))
            <div class="rounded-lg">
                <ul class="">
                    @foreach ($default_subreddits as $subreddit)
                        @if (!in_array($subreddit, $subscriptions))
                            <li class="flex items-center justify-between my-1 px-5 py-4 tint-bg-down-2 rounded-lg">
                                <a href="{{ Helpers::get_base_url() }}/r/{{ $subreddit }}"
                                    class="text-md">{{ $subreddit }}</a>
                                <button class="tint-fg-up-25"
                                    hx-put="{{ Helpers::get_base_url() }}/subscriptions?s={{ $subreddit }}"
                                    hx-target="#subscriptions" hx-swap="outerHTML" hx-select="#subscriptions">
                                    Add
                                </button>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endsection
