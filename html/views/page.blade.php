@extends('layouts.app')
 
@section('title', 'Page Title')
 
@section('content')
    @if($subreddit_id ?? false)
        <div class="tab-title tint-bg-down-2">/r/{{ $subreddit_id }}</div>
    @else
        <div class="tab-title tint-bg-down-2">Home</div>
    @endif
    <div class="listing">
        @foreach ($data as $key => $entry)
            @if($entry['kind'] == 'Listing' && $entry['data']['children'] && $entry['data']['children'][0]['kind'] == 't3')
                <div class="list-t3">
                    @foreach ($entry['data']['children'] as $key => $value)
                        @php
                            $is_last = $key == count($entry['data']['children']) - 1 && !$is_comments_page;
                        @endphp
                        @include('partials.post', ['data' => $value['data'], 'is_last' => $is_last, 'is_comments_page' => $is_comments_page])
                    @endforeach
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