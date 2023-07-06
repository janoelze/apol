<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';

use Jenssegers\Blade\Blade;
use MiladRahimi\PhpRouter\Router;
use Laminas\Diactoros\Response\JsonResponse;
use MiladRahimi\PhpRouter\Routing\Route;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\RedirectResponse;
use Cake\Cache\Cache;

// config

Cache::setConfig('default', [
    'className' => 'File',
    'duration' => '+2 minutes',
    'path' => './cache',
    'prefix' => 'apol_'
]);

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
    public static function get_embeddable_video($data)
    {
        // get fallback_url
        $secure_media = $data['secure_media'] ?? [];

        if (isset($secure_media['reddit_video'])) {
            $width = $secure_media['reddit_video']['width'];
            $height = $secure_media['reddit_video']['height'];

            $mp4_url = $secure_media['reddit_video']['fallback_url'];

            foreach(['360', '1080'] as $w){
                $mp4_url = str_replace(
                    sprintf('DASH_%s.mp4', $w),
                    sprintf('DASH_720.mp4', $w),
                    $mp4_url
                );
            }

            $mp4_audio_url = str_replace(
                'DASH_720.mp4',
                'DASH_audio.mp4',
                $mp4_url
            );

            return [
                'mp4_video_url' => $mp4_url,
                'mp4_audio_url' => $mp4_audio_url,
                'has_audio' => isset($secure_media['reddit_video']['has_audio']) ? $secure_media['reddit_video']['has_audio'] : false,
                'width' => $width,
                'height' => $height,
                'poster' => $data['thumbnail'],
            ];
        }
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
                'active' => self::url_includes('r/all'),
                'label' => 'Home',
            ],
            [
                'href' => self::get_base_url() . '/r/popular',
                'icon' => __DIR__ . '/img/zap.svg',
                'active' => self::url_includes('r/popular'),
                'label' => 'Popular',
            ],
            [
                'href' => self::get_base_url() . '/subscriptions',
                'icon' => __DIR__ . '/img/list.svg',
                'active' => self::url_includes('subscriptions'),
                'label' => 'Subs',
            ],
            [
                'href' => self::get_base_url() . '/settings',
                'icon' => __DIR__ . '/img/list.svg',
                'active' => self::url_includes('settings'),
                'label' => 'Settings',
            ]
        ];
    }

    public static function extract_subreddit_id()
    {
        $url = $_SERVER['REQUEST_URI'];
        $url = explode('?', $url)[0];
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
    public static function is_production()
    {
        return !self::url_includes('localhost');
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
        $cache_key = md5($url);
        $data = Cache::remember($cache_key, function () use ($url) {
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
        });
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
        $url = sprintf('https://old.reddit.com%s.json?%s', $path, $params_encoded);
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

class UserSettings
{
    function __construct()
    {
        $cookie_contents = $_COOKIE['user_settings'] ?? '[]';
        $this->user_settings = json_decode($cookie_contents, true);
        $this->default_settings = [
            [
                'label' => 'Colorful nesting',
                'id' => 'colorful_nesting',
                'value' => true,
                'type' => 'boolean',
            ],
            [
                'label' => 'Blur NSFW content',
                'id' => 'blur_nsfw',
                'value' => true,
                'type' => 'boolean',
            ],
            [
                'label' => 'Theme',
                'id' => 'theme',
                'value' => 'dark',
                'type' => 'select',
                'options' => [
                    [
                        'label' => 'Dark',
                        'value' => 'dark',
                    ],
                    [
                        'label' => 'Light',
                        'value' => 'light',
                    ],
                ]
            ],
        ];
    }
    public function get_default_settings()
    {
        return $this->default_settings;
    }
    public function persist()
    {
        $cookie_contents = json_encode($this->user_settings);
        setcookie('user_settings', $cookie_contents, time() + (86400 * 30), "/");
    }
    public function get_user_settings()
    {
        $user_settings = [];

        foreach ($this->get_default_settings() as $setting) {
            $new_setting = $setting;
            $new_setting['value'] = $this->user_settings[$setting['id']] ?? $setting['value'];
            $user_settings[] = $new_setting;
        }

        return $user_settings;
    }
    public function set_user_setting($name, $value)
    {
        $this->user_settings[$name] = $value;
        $this->persist();
    }
}

$t = new Template();
$r = new RedditProxy();
$us = new UserSettings();
$s = new Subscriptions();
$base_url = Helpers::get_base_url();

// defaults

// routes

$router = Router::create();
$is_content_fetch = isset($_GET['fetch']);

$router->get($base_url . '.*', function (ServerRequest $request) use ($t, $r, $is_content_fetch) {
    $data = [];

    if ($is_content_fetch) {
        $data = $r->get($request);
        if (isset($data['error'])) {
            return new JsonResponse($data);
        }
    }

    $path = $request->getUri()->getPath() ?? '/r/all';
    $is_comments_page = strpos($path, '/comments/') !== false;
    $subreddit_id = Helpers::extract_subreddit_id();

    if (isset($data['kind']) && $data['kind'] == 'Listing') {
        $data = [$data];
    }

    return $t->render('page', [
        'data' => $data,
        'async_load' => true,
        'page_title' => sprintf('r/%s', $subreddit_id),
        'is_content_fetch' => $is_content_fetch,
        'subreddit_id' => $subreddit_id,
        'is_comments_page' => $is_comments_page,
    ]);
});

$router->get($base_url . '/subscriptions', function (ServerRequest $request) use ($t, $r, $s, $is_content_fetch) {
    $arr = $s->get_subsriptions();

    if (empty($arr)) {
        $arr = json_decode(file_get_contents('./default-subreddits.json'), true);
    }

    return $t->render('subscriptions', [
        'arr' => $arr,
        'page_title' => 'Subscriptions',
        'async_load' => false
    ]);
});

$router->get($base_url . '/settings', function (ServerRequest $request) use ($t, $r, $s, $us, $is_content_fetch) {
    return $t->render('settings', [
        'page_title' => 'Settings',
        'settings' => $us->get_user_settings(),
        'async_load' => false
    ]);
});

$router->post($base_url . '/settings', function (ServerRequest $request) use ($us, $base_url) {
    foreach ($_POST as $key => $value) {
        $us->set_user_setting($key, $value);
    }
    usleep(50);
    return 'OK';
});

$router->dispatch();
