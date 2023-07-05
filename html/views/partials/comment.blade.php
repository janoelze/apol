@php
  //var_dump($data);
@endphp

<div class="thing t1">
  @if($data['author'] ?? false)
    <div class="comment-body tint-bg-down-3 tint-fg-up-51">
      <div class="meta">
        <div class="text">{{ $data['author'] }}</div>
        <div>{!! Helpers::embed('./img/arrow-up.svg') !!} {{ Helpers::formatk($data['ups']) }}</div>
        <div class="push-right">{{ Helpers::relative_time($data['created_utc']) }}</div>
      </div>
      <div class="body">{!! htmlspecialchars_decode($data['body_html']) !!}</div>
    </div>
  @endif
  @if ($data['replies'] ?? false)
      <div class="list-t1 replies">
        @foreach ($data['replies']['data']['children'] as $key => $value)
            @include('partials.comment', ['data' => $value['data']])
        @endforeach
      </div>
  @endif
</div>