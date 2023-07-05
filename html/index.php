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

    public static function extract_subreddit_id(){
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

class Settings
{
    public function __construct()
    {
        $this->initialize();
    }

    public static $defaultSettings = [
        [
            'label' => 'Colorful Nests',
            'id' => 'colorful_nests',
            'value' => true,
            'type' => 'boolean',
        ],
        [
            'label' => 'Blur NSFW',
            'id' => 'blur_nsfw',
            'value' => true,
            'type' => 'boolean',
        ],
    ];

    private static function getPreferencesFromCookie()
    {
        $cookieContents = $_COOKIE['preferences'] ?? '{}';
        return json_decode($cookieContents, true);
    }

    private static function savePreferencesToCookie($preferences)
    {
        $cookieContents = json_encode($preferences);
        setcookie('preferences', $cookieContents, time() + (86400 * 30), "/");
    }

    public static function getUserPreference($key)
    {
        $preferences = self::getPreferencesFromCookie();
        foreach ($preferences as $preference) {
            if ($preference['id'] == $key) {
                return $preference['value'];
            }
        }
        foreach (self::$defaultSettings as $defaultSetting) {
            if ($defaultSetting['id'] == $key) {
                return $defaultSetting['value'];
            }
        }
        return null;
    }

    public static function setUserPreference($key, $value)
    {
        $key = urldecode($key);
        $preferences = self::getPreferencesFromCookie();
        foreach ($preferences as &$preference) {
            if ($preference['id'] == $key) {
                $preference['value'] = $value;
            }
        }
        self::savePreferencesToCookie($preferences);
        //override the session variable as well, so we always have up to date values
        $_COOKIE['preferences'] = json_encode($preferences);
    }

    public static function getUserPreferences()
    {
        $userPreferences = self::getPreferencesFromCookie();
        $defaultPreferences = array_column(self::$defaultSettings, null, 'id');
        $userPreferences = array_column($userPreferences, null, 'id');

        $mergedPreferences = $userPreferences + $defaultPreferences;

        // If you want to preserve the original order of defaultSettings:
        $mergedPreferences = array_merge($defaultPreferences, $mergedPreferences);

        return array_values($mergedPreferences);
    }

    public static function revertToDefaultPreferences()
    {
        self::savePreferencesToCookie(self::$defaultSettings);
    }

    private function initialize()
    {
        $preferences = self::getPreferencesFromCookie();
        if (empty($preferences)) {
            self::savePreferencesToCookie(self::$defaultSettings);
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

class UserSettings {
    function __construct() {
        $cookie_contents = $_COOKIE['user_settings'] ?? '[]';
        $this->user_settings = json_decode($cookie_contents, true);
    }
    public function persist() {
        $cookie_contents = json_encode($this->user_settings);
        setcookie('user_settings', $cookie_contents, time() + (86400 * 30), "/");
    }
    public function get_user_settings() {
        return $this->user_settings;
    }
    public function set_user_setting($name, $value) {
        $this->user_settings[$name] = $value;
        $this->persist();
    }
}

$t = new Template();
$r = new RedditProxy();
$us = new UserSettings();
$s = new Subscriptions();
$p = new Settings();
$base_url = Helpers::get_base_url();

// defaults

// routes

$router = Router::create();
$is_content_fetch = isset($_GET['fetch']);

$router->get($base_url . '.*', function (ServerRequest $request) use ($t, $r, $is_content_fetch) {
    $data = [];

    if($is_content_fetch) {
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
        'async_load' => false
    ]);
});

$router->put($base_url . '/settings', function (ServerRequest $request) use ($t, $r, $s, $us, $is_content_fetch) {
    $data = $request->getParsedBody();
    $us = $us->get_user_settings();
    
    return $t->render('settings', [
        'page_title' => 'Settings',
        'async_load' => false
    ]);
});

$router->get($base_url . '/settings/toggle/{settingName}', function (ServerRequest $request, $settingName) use ($t, $base_url) {
    $settingName = urldecode($settingName);
    $currentValue = Settings::getUserPreference($settingName);
    Settings::setUserPreference($settingName, !$currentValue);
    return new RedirectResponse($base_url . '/settings');
});

$router->get($base_url . '/settings/revert', function (ServerRequest $request) use ($t, $base_url) {
    Settings::revertToDefaultSettings();
    return new RedirectResponse($base_url . '/settings');
});


$router->dispatch();
