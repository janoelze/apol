<div class="tab-bar">
  @foreach(Helpers::get_tab_bar_items() as $item)
    <a hx-boost href="{{ $item['href'] }}" class="{{ $item['class'] }}">
      {!! Helpers::embed($item['icon']) !!}
      <span>{{ $item['label'] }}</span>
    </a>
  @endforeach
</div>