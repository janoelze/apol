<?php

function reddit_request_v2($url)
{
  $opts = [
    'http' => [
      'method' => 'GET',
      'header' => [
        'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 16_5_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5 Mobile/15E148 Safari/604.1',
      ],
    ],
  ];
  $context = stream_context_create($opts);
  $data = file_get_contents($url, false, $context);
  $data = json_decode($data, true);
  return $data;
}

function reddit_request($path, $params = []) {
  $params_encoded = http_build_query($params);
  // set headers
  $opts = [
    'http' => [
      'method' => 'GET',
      'header' => [
        'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 16_5_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5 Mobile/15E148 Safari/604.1',
      ],
    ],
  ];
  $context = stream_context_create($opts);
  $url = sprintf('https://www.reddit.com/%s.json?%s', $path, $params_encoded);
  $data = file_get_contents(sprintf('https://www.reddit.com/%s.json?%s', $path, $params_encoded), false, $context);
  $data = json_decode($data, true);
  return $data;
}

