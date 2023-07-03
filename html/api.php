<?php

require_once 'src/src.php';

$action = $_GET['action'] ?? null;

$allowed_actions = [
  'get-subreddit',
  'get-story',
  'get-url',
];

if (!in_array($action, $allowed_actions)) {
  die('Invalid action');
}

if ($action === 'get-story') {
  $permalink = $_GET['permalink'] ?? null;

  if (!$permalink) {
    die('Invalid story');
  }

  $data = reddit_request($permalink);
  $data = $data;
  echo json_encode($data);
}

if ($action === 'get-url') {
  $url = $_GET['url'] ?? false;

  if (!$url) {
    die('No URL provided');
  }

  $decoded_url = base64_decode($url);

  $data = reddit_request_v2($decoded_url);
  die(json_encode($data));
}

if ($action === 'get-subreddit') {
  $id = $_GET['id'] ?? null;

  $params = [
    'count' => $_GET['count'] ?? 100
  ];

  if($_GET['after'] ?? false){
    $params['after'] = $_GET['after'];
  }

  if (!$id) {
    die('Invalid subreddit');
  }

  $data = reddit_request(sprintf('r/%s', $id), $params);
  $data = $data['data']['children'];
  $data = array_map(function($item) {
    return $item['data'];
  }, $data);
  echo json_encode($data);
}

?>