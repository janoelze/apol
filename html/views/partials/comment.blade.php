@php
  //var_dump($data);
@endphp

<div class="thing t1">
  @if($data['author'] ?? false)
    <div class="comment-body tint-bg-down-3 tint-fg-up-51">
      <div class="meta">
        <div class="text tint-fg-up-65">{{ $data['author'] }}</div>
        <div class="tint-fg-up-35">{!! Helpers::embed('./img/arrow-up.svg') !!} {{ Helpers::formatk($data['ups']) }}</div>
        <div class="tint-fg-up-35 push-right">{{ Helpers::relative_time($data['created_utc']) }}</div>
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