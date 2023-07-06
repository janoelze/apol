<div class="raw-video">
  <video
    id="video-{{ $data['name'] }}"
    data-poster-url="{{ $data['thumbnail'] }}"
    data-mp4-url="{{ $video['fallback_url'] }}"
    data-width="{{ $video['width'] }}"
    data-has-audio="{{ $video['has_audio'] ? '1' : '0' }}"
    data-height="{{ $video['height'] }}"
    data-hls-url="{{ $video['hls_url'] }}"
    data-dash-url="{{ $video['dash_url'] }}">
  </video>
</div>