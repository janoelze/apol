@if($is_comments_page)
  <div class="thing t3 tint-bg-down-2">
@else
  @if($is_last)
    {{-- hx-get="{!! Helpers::get_next_page($data) !!}" hx-select=".list-t3" --}}
    <a class="thing t3 tint-bg-down-2" href="{{ Helpers::get_base_url() }}{{ $data['permalink'] }}" hx-get="{!! Helpers::get_next_page($value['data']) !!}" hx-trigger="revealed" hx-select=".list-t3 .thing" hx-swap="afterend">
  @else
    <a class="thing t3 tint-bg-down-2" href="{{ Helpers::get_base_url() }}{{ $data['permalink'] }}" >
  @endif
@endif
  <div class="title tint-fg-up-65">{{ $data['title'] }}</div>
  @if($is_comments_page && $data['selftext_html'] ?? false)
    <div class="selftext">{!! htmlspecialchars_decode($data['selftext_html']) !!}</div>
  @endif
  @if($picture = Helpers::get_embeddable_picture($data))
    <div class="image" href="{{ $data['url'] }}">
      <img src="{{ $picture['src'] }}" />
    </div>
  @endif
  @if($data['url'] ?? false)
    <div class="url tint-bg-up-2">
      <div class="url-icon tint-fg-up-35">{!! Helpers::embed('./img/link.svg') !!}</div>
      <div class="url-text tint-fg-up-35">
        <div>
          <span class="tint-fg-up-40">{{ Helpers::get_host($data['url']) }}</span><span class="tint-fg-up-18">{{ Helpers::get_path($data['url']) }}</span>
        </div>
      </div>
      <div class="url-icon tint-fg-up-35">{!! Helpers::embed('./img/chevron-right.svg') !!}</div>
    </div>
  @endif
  <div class="meta">
    <div class="tint-fg-up-35">{!! Helpers::embed('./img/arrow-up.svg') !!} {{ Helpers::formatk($data['ups']) }}</div>
    <div class="tint-fg-up-35">{!! Helpers::embed('./img/message-circle.svg') !!} {{ Helpers::formatk($data['num_comments']) }}</div>
    <div class="tint-fg-up-35">{!! Helpers::embed('./img/clock.svg') !!} {{ Helpers::relative_time($data['created_utc']) }}</div>
  </div>
</a>