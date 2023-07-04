<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';

use Jenssegers\Blade\Blade;
use MiladRahimi\PhpRouter\Router;
use Laminas\Diactoros\Response\JsonResponse;
use MiladRahimi\PhpRouter\Routing\Route;
use Laminas\Diactoros\ServerRequest;

foreach (glob(__DIR__ . '/blade-cache/*.php') as $filename) {
    unlink($filename);
}

class Helpers
{
    public static function relative_time($unix_ts)
    {
        $diff = time() - $unix_ts;
        if ($diff < 60) {
            return sprintf('%ds', $diff);
        } else if ($diff < 3600) {
            return sprintf('%dm', round($diff / 60));
        } else if ($diff < 86400) {
            return sprintf('%dh', round($diff / 3600));
        } else if ($diff < 604800) {
            return sprintf('%dd', round($diff / 86400));
        } else if ($diff < 2419200) {
            return sprintf('%dw', round($diff / 604800));
        } else if ($diff < 29030400) {
            return sprintf('%dM', round($diff / 2419200));
        } else {
            return sprintf('%dy', round($diff / 29030400));
        }
    }
    public static function get_next_page($data)
    {
        $url = $_SERVER['REQUEST_URI'];
        $new_url = self::add_or_update_params($url, [
            [
                'key' => 'count',
                'value' => 100,
            ],
            [
                'key' => 'after',
                'value' => $data['name'],
            ],
        ]);
        return $new_url;
    }
    public static function add_or_update_params($url, $params)
    {
        $url_parts = parse_url($url);
        parse_str($url_parts['query'] ?? '', $existing_params);
        foreach ($params as $param) {
            $existing_params[$param['key']] = $param['value'];
        }
        $query = http_build_query($existing_params);
        $updated_url = $url_parts['path'] . '?' . $query;
        return $updated_url;
    }
    public static function get_embeddable_picture($data)
    {
        $url = $data['url'];

        if (strpos($url, 'i.redd.it') !== false) {
            return [
                'src' => $url
            ];
        }
    }
    public static function get_tab_bar_items()
    {
        return [
            [
                'href' => self::get_base_url() . '/r/all',
                'icon' => __DIR__ . '/img/home.svg',
                'class' => self::url_includes('r/all') ? 'active' : '',
                'label' => 'Home',
            ],
            [
                'href' => self::get_base_url() . '/r/popular',
                'icon' => __DIR__ . '/img/zap.svg',
                'class' => self::url_includes('r/popular') ? 'active' : '',
                'label' => 'Popular',
            ],
            [
                'href' => self::get_base_url() . '/subscriptions',
                'icon' => __DIR__ . '/img/list.svg',
                'class' => self::url_includes('subscriptions') ? 'active' : '',
                'label' => 'Subs',
            ],
            [
                'href' => self::get_base_url() . '/settings',
                'icon' => __DIR__ . '/img/list.svg',
                'class' => self::url_includes('settings') ? 'active' : '',
                'label' => 'Settings',
            ]
        ];
    }

    public static function get_settings() {
        return [
            [
                'name' => 'Colorful Nests',
                'type' => 'toggle',
                'defaultValue' => false,
            ]
        ];
    }

    public static function set_setting($name, $value) {
        $settings = self::get_settings();
        foreach ($settings as $setting) {
            if ($setting['name'] === $name) {
                $setting['activation_func']($value);
            }
        }
    }

    public static function extract_subreddit_id(){
        $url = $_SERVER['REQUEST_URI'];
        $matches = [];
        preg_match('/\/r\/([^\/]+)/', $url, $matches);
        return $matches[1] ?? '';
    }
    public static function get_current_full_url()
    {
        return sprintf('%s://%s%s', $_SERVER['REQUEST_SCHEME'], $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']);
    }
    public static function url_includes($str)
    {
        return strpos(self::get_current_full_url(), $str) !== false;
    }
    public static function get_base_url()
    {
        if (self::url_includes('localhost')) {
            return '';
        } else {
            return '/apol';
        }
    }
    public static function get_host($url)
    {
        return parse_url($url, PHP_URL_HOST);
    }
    public static function get_path($url)
    {
        return parse_url($url, PHP_URL_PATH);
    }
    public static function embed($src)
    {
        return file_exists($src) ? file_get_contents($src) : '';
    }
    public static function formatk($val)
    {
        $val = (int) $val;
        if ($val > 1000000) {
            return sprintf('%sM', round($val / 1000000, 1));
        } else if ($val > 1000) {
            return sprintf('%sk', round($val / 1000, 1));
        } else {
            return $val;
        }
    }
}

# Types of Thing
# t1 Comment
# t2 Account
# t3 Link
# t4 Message
# t5 Subreddit
# t6 Award
# t8 PromoCampaign

class RedditProxy
{
    function __construct()
    {
    }
    public function request($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 16_5_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5 Mobile/15E148 Safari/604.1");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $data = @json_decode($output, true);
        if (!$data) {
            $data = [
                'error' => 'Could not parse JSON',
                'raw' => $output,
            ];
        }
        return $data;
    }
    public function get($request)
    {
        $path = $request->getUri()->getPath();
        if (strpos($path, '/apol') === 0) {
            $path = substr($path, 5);
        }
        $params = $request->getQueryParams();
        $params_encoded = http_build_query($params);
        $url = sprintf('https://www.reddit.com%s.json?%s', $path, $params_encoded);
        return $this->request($url);
    }
}

class Template
{
    function __construct()
    {
        $views = __DIR__ . '/views';
        $cache = __DIR__ . '/blade-cache';
        $this->blade = new Blade($views, $cache);
    }
    public function render($template, $data = [])
    {
        return $this->blade->make($template, $data)->render();
    }
}

class Subscriptions
{
    function __construct()
    {
        $cookie_contents = $_COOKIE['subsriptions'] ?? '[]';
        $this->subscriptions = json_decode($cookie_contents, true);
    }
    public function persist()
    {
        $cookie_contents = json_encode($this->subscriptions);
        setcookie('subsriptions', $cookie_contents, time() + (86400 * 30), "/");
    }
    public function get_subsriptions()
    {
        return $this->subscriptions;
    }
    public function add_subscription($subreddit)
    {
        $this->subscriptions[] = $subreddit;
        $this->persist();
    }
}

$t = new Template();
$r = new RedditProxy();
$s = new Subscriptions();
$base_url = Helpers::get_base_url();

// defaults

// routes

$router = Router::create();

$router->get($base_url . '.*', function (ServerRequest $request) use ($t, $r) {
    $data = $r->get($request);

    if (isset($data['error'])) {
        return new JsonResponse($data);
    }

    $path = $request->getUri()->getPath() ?? '/r/all';
    $is_comments_page = strpos($path, '/comments/') !== false;
    $subreddit_id = Helpers::extract_subreddit_id();

    if (isset($data['kind']) && $data['kind'] == 'Listing') {
        $data = [$data];
    }

    return $t->render('page', [
        'data' => $data,
        'subreddit_id' => $subreddit_id,
        'is_comments_page' => $is_comments_page,
    ]);
});

$router->get($base_url . '/subscriptions', function (ServerRequest $request) use ($t, $r, $s) {
    $arr = $s->get_subsriptions();

    if (empty($arr)) {
        $arr = json_decode(file_get_contents('./default-subreddits.json'), true);
    }

    return $t->render('subscriptions', [
        'arr' => $arr
    ]);
});

$router->get($base_url . '/get-video-embed', function (ServerRequest $request) use ($t, $r, $s) {
    function getVideoSrcFromVReddit($url, $r)
    {
        $url = str_replace('v.redd.it', 'old.reddit.com', $url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 16_5_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5 Mobile/15E148 Safari/604.1");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $output = curl_exec($ch);
        curl_close($ch);

        // var_dump($output);

        $html = str_get_html($output);

        // var_dump($html);

        
        
        // $res = $r->request($url);

        // var_dump($res);

        // $html = new simple_html_dom();
        // $html = $html->load($output);
        
        // // Make an HTTP request to the v.redd.it link and get the HTML content
        // $html = load($html);

        // // Find the video element using CSS selectors
        $videoElement = $html->find('video');

        var_dump($videoElement);

        // // Extract the video source (src) attribute
        // $videoSrc = $videoElement->src;

        // // Clean up the HTML DOM object
        // $html->clear();
        // unset($html);

        // return $videoSrc;
    }

    // Example usage
    $vRedditUrl = 'https://v.redd.it/05jtqmxop29b1'; // Replace with your v.redd.it URL
    $videoSrc = getVideoSrcFromVReddit($vRedditUrl, $r);

    // Output the video src
    echo 'Video src: ' . $videoSrc;

    return $t->render('subscriptions', [
        'arr' => $arr
    ]);
});

$router->get('/settings', function (ServerRequest $request) use ($t, $r, $s) {
    return $t->render('settings', []);
});

$router->dispatch();
