@extends('layouts.app')

@section('title', $page_title)

@section('content')
    <div class="listing">
        @foreach ($data as $key => $entry)
            @if($entry['kind'] == 'Listing' && $entry['data']['children'] && $entry['data']['children'][0]['kind'] == 't3')
                <div class="list-t3">
                    @foreach ($entry['data']['children'] as $key => $value)
                        @php
                            $is_last = $key == count($entry['data']['children']) - 1 && !$is_comments_page;
                        @endphp
                        @include('partials.post', [
                            'data' => $value['data'],
                            'is_last' => $is_last,
                            'subreddit_id' => $subreddit_id,
                            'is_comments_page' => $is_comments_page
                        ])
                    @endforeach
                    @if(!$is_comments_page)
                        <div class="loading-indicator"><span class="tint-fg-up-16">Loading...</span></div>
                    @endif
                </div>
            @endif
            @if ($entry['kind'] == 'Listing' && count($entry['data']['children']) && $entry['data']['children'][0]['kind'] == 't1')
                @if($entry['data']['children'] ?? false && count($entry['data']['children']) > 0)
                    <div class="list-t1">
                        @foreach ($entry['data']['children'] as $key => $value)
                            @include('partials.comment', ['data' => $value['data']])
                        @endforeach
                    </div>
                @endif
            @endif
        @endforeach
    </div>
@endsection