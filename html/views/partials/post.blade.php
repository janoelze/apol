@if($is_comments_page)
  <div class="thing t3 tint-bg-down-2">
@else
  @if($is_last && !$is_comments_page)
    {{-- hx-get="{!! Helpers::get_next_page($data) !!}" hx-select=".list-t3" --}}
    <section class="thing t3 tint-bg-down-2 {{ $data['over_18'] ? 'is-nsfw' : '' }}" data-url="{{ Helpers::get_base_url() }}{{ $data['permalink'] }}" hx-get="{!! Helpers::get_next_page($value['data']) !!}" hx-trigger="intersect once" hx-select=".list-t3 .thing" hx-swap="afterend" hx-indicator=".loading-indicator">
  @else
    <section class="thing t3 tint-bg-down-2 {{ $data['over_18'] ? 'is-nsfw' : '' }}" data-url="{{ Helpers::get_base_url() }}{{ $data['permalink'] }}">
  @endif
@endif
  <div class="title tint-fg-up-65 ">{{ $data['title'] }}</div>
  @if($is_comments_page && $data['selftext_html'] ?? false)
    <div class="selftext">{!! htmlspecialchars_decode($data['selftext_html']) !!}</div>
  @endif
  @if($picture = Helpers::get_embeddable_picture($data))
    <div class="image" href="{{ $data['url'] }}">
      <img src="{{ $picture['src'] }}" />
    </div>
  @endif
  @if($video = Helpers::get_embeddable_video($data))
    @include('partials.video', [
      'video' => $video,
      'data' => $data
    ])
  @endif
  @if($data['url'] ?? false)
    <a href="{{$data['url']}}" class="url tint-bg-up-2">
      <div class="url-icon tint-fg-up-35">{!! Helpers::embed('./img/link.svg') !!}</div>
      <div class="url-text tint-fg-up-35">
        <div>
          <span class="tint-fg-up-40">{{ Helpers::get_host($data['url']) }}</span><span class="tint-fg-up-18">{{ Helpers::get_path($data['url']) }}</span>
        </div>
      </div>
      <div class="url-icon tint-fg-up-35">{!! Helpers::embed('./img/chevron-right.svg') !!}</div>
    </a>
  @endif
  <div class="meta">
    <div class="tint-fg-up-35">{!! Helpers::embed('./img/arrow-up.svg') !!} {{ Helpers::formatk($data['ups']) }}</div>
    <div class="tint-fg-up-35">{!! Helpers::embed('./img/message-circle.svg') !!} {{ Helpers::formatk($data['num_comments']) }}</div>
    <div class="tint-fg-up-35">{!! Helpers::embed('./img/clock.svg') !!} {{ Helpers::relative_time($data['created_utc']) }}</div>
    @if($data['subreddit'] != $subreddit_id)
      <div class="tint-fg-up-35 subreddit push-right">
        <span class="">In</span>
        <span class="text-semibold"></span>
        <a href="{{ Helpers::get_base_url() }}/r/{{ $data['subreddit'] }}" class="tint-fg-up-35">r/{{ $data['subreddit'] }}</a>
      </div>
    @endif
  </div>
</section>