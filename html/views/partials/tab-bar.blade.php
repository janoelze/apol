<div class="tab-bar tint-bg-down-5">
  @foreach(Helpers::get_tab_bar_items() as $item)
    <a hx-boost href="{{ $item['href'] }}" class="{{ $item['active'] ? 'tint-fg-up-65' : 'tint-fg-up-35' }}">
      {!! Helpers::embed($item['icon']) !!}
      <span>{{ $item['label'] }}</span>
    </a>
  @endforeach
</div>