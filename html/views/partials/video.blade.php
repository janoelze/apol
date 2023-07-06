<video
  id="video-{{ $data['name'] }}"
  data-poster-url="{{ $data['thumbnail'] }}"
  data-mp4-url="{{ $video['fallback_url'] }}"
  data-width="{{ $video['width'] }}"
  data-height="{{ $video['height'] }}"
  data-hls-url="{{ $video['hls_url'] }}"
  data-dash-url="{{ $video['dash_url'] }}"
  muted loop controls playsinline autoplay>
</video>