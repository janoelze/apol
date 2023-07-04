@if($is_comments_page)
  <div class="thing t3">
@else
  @if($is_last)
    <a class="thing t3" href="{{ Helpers::get_base_url() }}{{ $data['permalink'] }}" hx-get="{!! Helpers::get_next_page($data) !!}" hx-select=".list-t3">
  @else
    <a class="thing t3" href="{{ Helpers::get_base_url() }}{{ $data['permalink'] }}">
  @endif
@endif
  <div class="title">{{ $data['title'] }}</div>
  @if($is_comments_page && $data['selftext_html'] ?? false)
      <div class="selftext">{!! htmlspecialchars_decode($data['selftext_html']) !!}</div>
    @endif
  @if(!$is_comments_page)
    @if($picture = Helpers::get_embeddable_picture($data))
      <div class="image" href="{{ $data['url'] }}">
        <img src="{{ $picture['src'] }}" />
      </div>
    @endif
    @if($data['url'])
      <div class="url">
        <div class="url-icon">{!! Helpers::embed('./img/link.svg') !!}</div>
        <div class="url-text">
          <div>
            <span>{{ Helpers::get_host($data['url']) }}</span><span>{{ Helpers::get_path($data['url']) }}</span>
          </div>
        </div>
        <div class="url-icon">{!! Helpers::embed('./img/chevron-right.svg') !!}</div>
      </div>
    @endif
  @endif
  <div class="meta">
    <div>{!! Helpers::embed('./img/arrow-up.svg') !!} {{ Helpers::formatk($data['ups']) }}</div>
    <div>{!! Helpers::embed('./img/message-circle.svg') !!} {{ Helpers::formatk($data['num_comments']) }}</div>
    <div>{!! Helpers::embed('./img/clock.svg') !!} {{ Helpers::relative_time($data['created_utc']) }}</div>
  </div>
</a>