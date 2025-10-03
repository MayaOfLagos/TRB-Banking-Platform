<?php

use App\Constants\Status;
use App\Lib\GoogleAuthenticator;
use App\Models\Extension;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use Carbon\Carbon;
use App\Lib\Captcha;
use App\Lib\ClientInfo;
use App\Lib\CurlRequest;
use App\Lib\FileManager;
use App\Lib\PDFManager;
use App\Models\Language;
use App\Models\Role;
use App\Models\TableConfiguration;
use App\Notify\Notify;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Laramin\Utility\VugiChugi;

function systemDetails()
{
    $system['name'] = 'TRB Banking System';
    $system['version'] = '3.7.0';
    $system['build_version'] = '5.0.10';

    return $system;
}

function formatBytes($size, $precision = 2)
{
    if ($size === 0)
        return '0 Bytes';
    $base = log($size, 1024);
    $suffixes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];

    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}

function slug($string)
{
    return Str::slug($string);
}

function verificationCode($length)
{
    if ($length == 0)
        return 0;
    $min = pow(10, $length - 1);
    $max = (int) ($min - 1) . '9';
    return random_int($min, $max);
}

function getNumber($length = 8)
{
    $characters = '1234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function activeTemplate($asset = false)
{
    $template = session('template') ?? gs('active_template');
    if ($asset)
        return 'assets/templates/' . $template . '/';
    return 'templates.' . $template . '.';
}

function activeTemplateName()
{
    $template = session('template') ?? gs('active_template');
    return $template;
}

function siteLogo($type = null)
{
    $name = $type ? "/logo_$type.png" : '/logo.png';
    return getImage(getFilePath('logoIcon') . $name);
}

function siteFavicon()
{
    return getImage(getFilePath('logoIcon') . '/favicon.png');
}

function loadReCaptcha()
{
    return Captcha::reCaptcha();
}

function loadCustomCaptcha($width = '100%', $height = 46, $bgColor = '#003')
{
    return Captcha::customCaptcha($width, $height, $bgColor);
}

function verifyCaptcha()
{
    return Captcha::verify();
}

function loadExtension($key)
{
    $extension = Extension::where('act', $key)->where('status', Status::ENABLE)->first();
    return $extension ? $extension->generateScript() : '';
}

function getTrx($length = 12)
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getAmount($amount, $length = 2)
{
    $amount = round($amount ?? 0, $length);
    return $amount + 0;
}

function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false, $currencyFormat = true)
{
    $separator = '';
    if ($separate) {
        $separator = ',';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if ($exceptZeros) {
        $exp = explode('.', $printAmount);
        if ($exp[1] * 1 == 0) {
            $printAmount = $exp[0];
        } else {
            $printAmount = rtrim($printAmount, '0');
        }
    }
    if ($currencyFormat) {
        if (gs('currency_format') == Status::CUR_BOTH) {
            return gs('cur_sym') . $printAmount . ' ' . __(gs('cur_text'));
        } elseif (gs('currency_format') == Status::CUR_TEXT) {
            return $printAmount . ' ' . __(gs('cur_text'));
        } else {
            return gs('cur_sym') . $printAmount;
        }
    }
    return $printAmount;
}


function removeElement($array, $value)
{
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet)
{
    return "https://api.qrserver.com/v1/create-qr-code/?data=$wallet&size=300x300&ecc=m";
}

function keyToTitle($text)
{
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}


function camelCaseToTitleCase($str)
{
    return preg_replace('/(?<!^)([A-Z])/', ' $1', $str);
}

function titleToKey($text)
{
    return strtolower(str_replace(' ', '_', $text));
}


function strLimit($title = null, $length = 10)
{
    return Str::limit($title, $length);
}


function getIpInfo()
{
    $ipInfo = ClientInfo::ipInfo();
    return $ipInfo;
}


function osBrowser()
{
    $osBrowser = ClientInfo::osBrowser();
    return $osBrowser;
}


function getTemplates()
{
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website'] = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url = VugiChugi::gttmp() . systemDetails()['name'];
    $response = CurlRequest::curlPostContent($url, $param);
    if ($response) {
        return $response;
    } else {
        return null;
    }
}


function getPageSections($arr = false)
{
    $jsonUrl = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}

function getImage($image, $size = null, $avatar = false)
{
    $clean = '';
    if (file_exists($image) && is_file($image)) {
        return asset($image) . $clean;
    }
    if ($size) {
        return route('placeholder.image', $size);
    }

    if ($avatar) {
        return asset('assets/images/avatar.png');
    }
    return asset('assets/images/default.png');
}


function notify($user, $templateName, $shortCodes = null, $sendVia = null, $createLog = true, $pushImage = null)
{
    $globalShortCodes = [
        'site_name' => gs('site_name'),
        'site_currency' => gs('cur_text'),
        'currency_symbol' => gs('cur_sym'),
    ];



    if (gettype($user) == 'array') {
        $user = (object) $user;
    }

    $shortCodes = array_merge($shortCodes ?? [], $globalShortCodes);


    $notify = new Notify($sendVia);
    $notify->templateName = $templateName;
    $notify->shortCodes = $shortCodes;
    $notify->user = $user;
    $notify->createLog = $createLog;
    $notify->pushImage = $pushImage;
    $notify->userColumn = isset($user->id) ? $user->getForeignKey() : 'user_id';
    $notify->send();
}

function getPaginate($paginate = null)
{
    if (!$paginate) {
        $paginate = gs('paginate_number');
    }
    return $paginate;
}

function paginateLinks($data, $view = null)
{
    return $data->appends(request()->all())->links($view);
}

function menuActive($routeName, $type = null, $param = null)
{
    if ($type == 3)
        $class = 'side-menu--open';
    elseif ($type == 2)
        $class = 'sidebar-submenu__open';
    else
        $class = 'active';

    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value))
                return $class;
        }
    } elseif (request()->routeIs($routeName)) {
        if ($param) {
            $routeParam = array_values(@request()->route()->parameters ?? []);
            if (strtolower(@$routeParam[0]) == strtolower($param))
                return $class;
            else
                return;
        }
        return $class;
    }
}


function fileUploader($file, $location, $size = null, $old = null, $thumb = null, $filename = null)
{
    $fileManager = new FileManager($file);
    $fileManager->path = $location;
    $fileManager->size = $size;
    $fileManager->old = $old;
    $fileManager->thumb = $thumb;
    $fileManager->filename = $filename;
    $fileManager->upload();
    return $fileManager->filename;
}

function fileManager()
{
    return new FileManager();
}

function getFilePath($key)
{
    return fileManager()->$key()->path;
}

function getFileSize($key)
{
    return fileManager()->$key()->size;
}

function getFileExt($key)
{
    return fileManager()->$key()->extensions;
}

function diffForHumans($date)
{
    $lang = session()->get('lang');
    if (!$lang) {
        $lang = getDefaultLang();
    }

    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}

function showDateTime($date, $format = 'Y-m-d h:i A')
{
    if (!$date) {
        return '-';
    }
    $lang = getDefaultLang();
    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}


function getContent($dataKeys, $singleQuery = false, $limit = null, $orderById = false)
{

    $templateName = activeTemplateName();
    if ($singleQuery) {
        $content = Frontend::where('tempname', $templateName)->where('data_keys', $dataKeys)->orderBy('id', 'desc')->first();
    } else {
        $article = Frontend::where('tempname', $templateName);
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if ($orderById) {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id')->get();
        } else {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id', 'desc')->get();
        }
    }
    return $content;
}

function verifyG2fa($user, $code, $secret = null)
{
    $authenticator = new GoogleAuthenticator();
    if (!$secret) {
        $secret = $user->tsc;
    }

    // Use verifyCode instead of getCode for better time tolerance
    // This allows for ±1 time window (30 seconds before/after) to account for time drift
    if ($authenticator->verifyCode($secret, $code, 2)) {
        $user->tv = Status::YES;
        $user->save();
        return true;
    } else {
        return false;
    }
}


function urlPath($routeName, $routeParam = null)
{
    if ($routeParam == null) {
        $url = route($routeName);
    } else {
        $url = route($routeName, $routeParam);
    }
    $basePath = route('home');
    $path = str_replace($basePath, '', $url);
    return $path;
}


function showMobileNumber($number)
{
    $length = strlen($number);
    return substr_replace($number, '***', 2, $length - 4);
}

function showEmailAddress($email)
{
    $endPosition = strpos($email, '@') - 1;
    return substr_replace($email, '***', 1, $endPosition);
}


function getRealIP()
{
    $ip = $_SERVER["REMOTE_ADDR"];
    //Deep detect ip
    if (filter_var(@$_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }
    if (filter_var(@$_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
}


function appendQuery($key, $value)
{
    return request()->fullUrlWithQuery([$key => $value]);
}

function dateSort($a, $b)
{
    return strtotime($a) - strtotime($b);
}

function dateSorting($arr)
{
    usort($arr, "dateSort");
    return $arr;
}

function gs($key = null)
{
    $general = Cache::get('GeneralSetting');
    if (!$general) {
        $general = GeneralSetting::first();
        Cache::put('GeneralSetting', $general);
    }
    if ($key)
        return @$general->$key;
    return $general;
}
function isImage($string)
{
    $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
    $fileExtension = pathinfo($string, PATHINFO_EXTENSION);
    if (in_array($fileExtension, $allowedExtensions)) {
        return true;
    } else {
        return false;
    }
}

function isHtml($string)
{
    if (preg_match('/<.*?>/', $string)) {
        return true;
    } else {
        return false;
    }
}


function convertToReadableSize($size)
{
    preg_match('/^(\d+)([KMG])$/', $size, $matches);
    $size = (int) $matches[1];
    $unit = $matches[2];

    if ($unit == 'G') {
        return $size . 'GB';
    }

    if ($unit == 'M') {
        return $size . 'MB';
    }

    if ($unit == 'K') {
        return $size . 'KB';
    }

    return $size . $unit;
}


function frontendImage($sectionName, $image, $size = null, $seo = false)
{
    if ($seo) {
        return getImage('assets/images/frontend/' . $sectionName . '/seo/' . $image, $size);
    }
    return getImage('assets/images/frontend/' . $sectionName . '/' . $image, $size);
}

function can($code)
{
    return Role::hasPermission($code);
}

function createBadge($type, $text)
{
    return "<span class='badge badge--$type'>" . trans($text) . '</span>';
}

function getOtpFields()
{
    $data = [];

    if (gs('modules')->otp_email) {
        $data[] = 'email';
    }

    if (gs('modules')->otp_sms) {
        $data[] = 'sms';
    }

    if (auth()->user()->ts) {
        $data[] = '2fa';
    }
    return $data;
}

function mergeOtpField($rules = [])
{

    $otpFields = getOtpFields();
    if (count($otpFields)) {
        $otpFields = implode(',', getOtpFields());
        $rules['auth_mode'] = "required|in:$otpFields";
    }
    return $rules;
}

function sessionVerificationId()
{
    $id = session()->get('otp_id');
    if (!$id) {
        throw ValidationException::withMessages(['error' => 'Invalid session'])->redirectTo(route('user.home'));
    }
    return $id;
}

function showBadge($status)
{

    if ($status) {
        $class = 'text--success';
        $text = trans('Yes');
    } else {
        $class = 'text--danger';
        $text = trans('No');
    }

    return '<span class="' . $class . '">' . $text . '</span>';
}

function prepareTableColumn($id, $name, $value = null, $sortable = true, $exportable = true, $filter = null, $filterColumn = null, $filterOptions = [], $link = null, $url = null, $className = null, $echoable = false)
{
    if (!$value) {
        $value = '$item->' . $id;
    }

    if (!$filterColumn) {
        $filterColumn = $id;
    }

    if ($echoable) {
        $value = 'echo ' . $value;
    } else {
        $value = 'return ' . $value;
    }

    return [
        'id' => $id,
        'name' => $name,
        'value' => trans($value),
        'sortable' => $sortable,
        'exportable' => $exportable,
        'filter_column' => $filterColumn,
        'filter' => $filter,
        'filter_options' => $filterOptions,
        'link' => $link,
        'url' => $url,
        'className' => $className,
    ];
}

function makeObject($array)
{
    return json_decode(json_encode($array));
}

function tableConfiguration($tableName)
{
    return TableConfiguration::where('admin_id', auth()->guard('admin')->id())->where('table_name', $tableName)->first();
}

function is_assoc(array $array)
{
    return array_values($array) !== $array;
}

function authStaff()
{
    return auth()->guard('branch_staff')->user();
}

function isManager()
{
    return authStaff()->designation == Status::ROLE_MANAGER;
}

function checkIsOtpEnable()
{
    if (gs('modules')->otp_email || gs('modules')->otp_sms || auth()->user()->ts) {
        return 1;
    }
    return 0;
}

function callApiMethod($routeName, $actionId)
{
    $action = \Route::getRoutes()->getByName($routeName)->getActionName();
    $data = explode('@', $action);
    $controller = new $data[0];
    $method = $data[1];
    return $controller->$method($actionId);
}

function downloadPdf($viewName, $data)
{
    $pdfManager = new PDFManager($viewName, $data);
    return $pdfManager->generatePDF();
}

function addCustomValidation($validator, $key, $message)
{
    $validator->after(function ($validator) use ($key, $message) {
        $validator->errors()->add($key, $message);
    });

    return $validator;
}

function verification()
{
    $verification = [];
    $general = gs();

    if (@$general->modules->otp_email || @$general->modules->otp_sms) {
        $verification['Email'] = @$general->modules->otp_email ? 1 : 0;
        $verification['Sms'] = @$general->modules->otp_sms ? 1 : 0;
    }
    return $verification;
}

function ordinal($number)
{
    $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
    if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
        return $number . 'th';
    } else {
        return $number . $ends[$number % 10];
    }
}

function displayRating(float $val)
{
    $result = '';
    for ($i = 0; $i < intval($val); $i++) {
        $result .= '<i class="la la-star"></i>';
    }
    if (fmod($val, 1) == 0.5) {
        $i++;
        $result .= '<i class="las la-star-half-alt"></i>';
    }
    for ($k = 0; $k < 5 - $i; $k++) {
        $result .= '<i class="lar la-star"></i>';
    }
    return $result;
}

function generateAccountNumber()
{
    $accountNumber = gs('account_no_prefix');
    $uniqueId = substr(hexdec(uniqid()), -2);
    $accountNumber .= date('ydis') . rand(11, 99) . $uniqueId;
    $suffix = getNumber(gs('account_no_length') - strlen($accountNumber));
    $accountNumber .= $suffix;

    return $accountNumber;
}


function getReferees($user, $maxLevel, $data = [], $depth = 1, $layer = 0)
{
    if ($user->allReferees->count() > 0 && $maxLevel > 0) {
        foreach ($user->allReferees as $under) {
            $i = 0;
            if ($i == 0) {
                $layer++;
            }
            $i++;

            $userData['id'] = $under->id;
            $userData['fullname'] = $under->fullname;
            $userData['username'] = $under->username;
            $userData['level'] = $depth;
            $data[] = $userData;
            if ($under->allReferees->count() > 0 && $layer < $maxLevel) {
                $data = getReferees($under, $maxLevel, $data, $depth + 1, $layer);
            }
        }
    }
    return $data;
}

function getDefaultLang()
{
    return Language::where('is_default', Status::YES)->first()->code ?? 'en';
}

/**
 * Get rebate settings from general settings
 */
function getRebateSettings($key = null)
{
    $general = gs();
    $rebateSettings = json_decode($general->rebate_settings ?? '{}', true);

    $defaultSettings = [
        'system' => [
            'enabled' => true,
            'auto_approval' => false,
            'auto_approval_limit' => 50.00,
            'daily_limit_per_user' => 500.00,
            'monthly_limit_per_user' => 2000.00,
            'minimum_rebate_amount' => 1.00,
            'maximum_rebate_amount' => 100.00,
        ],
        'tiers' => [
            'enabled' => true,
            'bronze_threshold' => 0,
            'silver_threshold' => 500,
            'gold_threshold' => 2000,
            'platinum_threshold' => 5000,
            'diamond_threshold' => 15000,
            'bronze_multiplier' => 1.0,
            'silver_multiplier' => 1.2,
            'gold_multiplier' => 1.5,
            'platinum_multiplier' => 2.0,
            'diamond_multiplier' => 2.5,
        ],
        'fraud' => [
            'enabled' => true,
            'fraud_score_threshold' => 70,
            'max_daily_uploads' => 50,
            'max_rapid_uploads' => 15,
            'velocity_threshold' => 25,
            'ip_sharing_limit' => 15,
            'duplicate_detection' => true,
        ],
        'notifications' => [
            'email_on_approval' => true,
            'email_on_rejection' => true,
            'email_on_tier_upgrade' => true,
            'admin_notification_threshold' => 1000.00,
        ]
    ];

    // Merge with defaults (preserve existing values, add missing defaults)
    foreach ($defaultSettings as $section => $sectionDefaults) {
        if (!isset($rebateSettings[$section])) {
            $rebateSettings[$section] = [];
        }
        $rebateSettings[$section] = array_merge($sectionDefaults, $rebateSettings[$section]);
    }

    return $key ? ($rebateSettings[$key] ?? null) : $rebateSettings;
}

/**
 * Get rebate system settings
 */
function getRebateSystemSettings($key = null)
{
    $systemSettings = getRebateSettings('system');
    return $key ? ($systemSettings[$key] ?? null) : $systemSettings;
}

/**
 * Get rebate tier settings
 */
function getRebateTierSettings($key = null)
{
    $tierSettings = getRebateSettings('tiers');
    return $key ? ($tierSettings[$key] ?? null) : $tierSettings;
}

/**
 * Get rebate fraud settings
 */
function getRebateFraudSettings($key = null)
{
    $fraudSettings = getRebateSettings('fraud');
    return $key ? ($fraudSettings[$key] ?? null) : $fraudSettings;
}

/**
 * Get rebate notification settings
 */
function getRebateNotificationSettings($key = null)
{
    $notificationSettings = getRebateSettings('notifications');
    return $key ? ($notificationSettings[$key] ?? null) : $notificationSettings;
}

/**
 * Check if rebate system is enabled
 */
function isRebateSystemEnabled()
{
    return getRebateSystemSettings('enabled') ?? true;
}

/**
 * Get daily rebate limit for user
 */
function getRebateDailyLimit()
{
    return getRebateSystemSettings('daily_limit_per_user') ?? 500.00;
}

/**
 * Get monthly rebate limit for user
 */
function getRebateMonthlyLimit()
{
    return getRebateSystemSettings('monthly_limit_per_user') ?? 2000.00;
}

/**
 * Get user's effective currency with priority: user currency → system default
 * @param \App\Models\User|null $user
 * @return array ['text' => 'USD', 'symbol' => '$']
 */
function getUserCurrency($user = null)
{
    // If no user provided, use system default
    if (!$user) {
        return [
            'text' => gs('cur_text'),
            'symbol' => gs('cur_sym')
        ];
    }

    // If user has preferred currency, use it
    if ($user->preferred_currency) {
        // Get currency symbol based on currency text
        $symbol = getCurrencySymbol($user->preferred_currency);
        return [
            'text' => strtoupper($user->preferred_currency),
            'symbol' => $symbol
        ];
    }

    // Fall back to system default
    return [
        'text' => gs('cur_text'),
        'symbol' => gs('cur_sym')
    ];
}

/**
 * Show amount with user-specific currency
 * @param float $amount
 * @param \App\Models\User|null $user
 * @param int $decimal
 * @param bool $separate
 * @param bool $exceptZeros
 * @return string
 */
function showUserAmount($amount, $user = null, $decimal = 2, $separate = true, $exceptZeros = false)
{
    $separator = '';
    if ($separate) {
        $separator = ',';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if ($exceptZeros) {
        $exp = explode('.', $printAmount);
        if ($exp[1] * 1 == 0) {
            $printAmount = $exp[0];
        } else {
            $printAmount = rtrim($printAmount, '0');
        }
    }

    $currency = getUserCurrency($user);

    // Use system currency format setting but with user's currency
    if (gs('currency_format') == Status::CUR_BOTH) {
        return $currency['symbol'] . $printAmount . ' ' . __($currency['text']);
    } elseif (gs('currency_format') == Status::CUR_TEXT) {
        return $printAmount . ' ' . __($currency['text']);
    } else {
        return $currency['symbol'] . $printAmount;
    }
}

/**
 * Get currency symbol from currency text
 * @param string $currencyText
 * @return string
 */
function getCurrencySymbol($currencyText)
{
    $symbols = [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'JPY' => '¥',
        'NGN' => '₦',
        'AUD' => 'A$',
        'CAD' => 'C$',
        'CHF' => 'Fr',
        'CNY' => '¥',
        'SEK' => 'kr',
        'NZD' => 'NZ$',
        'MXN' => '$',
        'SGD' => 'S$',
        'HKD' => 'HK$',
        'NOK' => 'kr',
        'TRY' => '₺',
        'RUB' => '₽',
        'INR' => '₹',
        'BRL' => 'R$',
        'ZAR' => 'R',
        'KRW' => '₩',
        'PLN' => 'zł',
        'THB' => '฿',
        'IDR' => 'Rp',
        'HUF' => 'Ft',
        'ILS' => '₪',
        'CLP' => '$',
        'PHP' => '₱',
        'AED' => 'د.إ',
        'COP' => '$',
        'SAR' => '﷼',
        'MYR' => 'RM',
        'RON' => 'lei',
        'AFN' => '؋',
        'ALL' => 'Lek',
        'AOA' => 'Kz',
        'XCD' => '$',
        'ARS' => '$',
        'AWG' => 'ƒ',
        'AZN' => 'ман',
        'BSD' => '$',
        'BBD' => '$',
        'BYR' => 'p.',
        'BZD' => 'BZ$',
        'BMD' => '$',
        'BOB' => '$b',
        'BAM' => 'KM',
        'BWP' => 'P',
        'BND' => '$',
        'BGN' => 'лв',
        'KHR' => '៛',
        'XAF' => 'FCF',
        'KYD' => '$',
        'CRC' => '₡',
        'HRK' => 'kn',
        'CUP' => '₱',
        'CZK' => 'Kč',
        'DKK' => 'kr',
        'DOP' => 'RD$',
        'EGP' => '£',
        'SVC' => '$',
        'ERN' => 'Nfk',
        'EEK' => 'kr',
        'FKP' => '£',
        'FJD' => '$',
        'GMD' => 'D',
        'GHC' => '¢',
        'GIP' => '£',
        'GTQ' => 'Q',
        'GYD' => '$',
        'HTG' => 'G',
        'HNL' => 'L',
        'ISK' => 'kr',
        'IRR' => '﷼',
        'JMD' => '$',
        'KZT' => 'лв',
        'KGS' => 'лв',
        'LAK' => '₭',
        'LVL' => 'Ls',
        'LBP' => '£',
        'LSL' => 'L',
        'LRD' => '$',
        'LTL' => 'Lt',
        'MOP' => 'MOP',
        'MKD' => 'ден',
        'MWK' => 'MK',
        'MVR' => 'Rf',
        'MRO' => 'UM',
        'MUR' => '₨',
        'MNT' => '₮',
        'MZN' => 'MT',
        'MMK' => 'K',
        'NAD' => '$',
        'NPR' => '₨',
        'ANG' => 'ƒ',
        'NIO' => 'C$',
        'KPW' => '₩',
        'OMR' => '﷼',
        'PKR' => '₨',
        'PAB' => 'B/.',
        'PYG' => 'Gs',
        'PEN' => 'S/.',
        'QAR' => '﷼',
        'SHP' => '£',
        'WST' => 'WS$',
        'STD' => 'Db',
        'RSD' => 'Дин',
        'SCR' => '₨',
        'SLL' => 'Le',
        'SKK' => 'Sk',
        'SBD' => '$',
        'SOS' => 'S',
        'LKR' => '₨',
        'SRD' => '$',
        'SYP' => '£',
        'TWD' => 'NT$',
        'TOP' => 'T$',
        'TTD' => 'TT$',
        'TMM' => 'm',
        'UAH' => '₴',
        'UYU' => '$U',
        'UZS' => 'лв',
        'VUV' => 'Vt',
        'VEF' => 'Bs',
        'VND' => '₫',
        'YER' => '﷼',
        'ZMK' => 'ZK',
        'ZWD' => 'Z$',
    ];

    return $symbols[strtoupper($currencyText)] ?? strtoupper($currencyText);
}

/**
 * Get available currencies for user selection
 * @return array
 */
function getAvailableCurrencies()
{
    return [
        'USD' => 'US Dollar',
        'EUR' => 'Euro',
        'GBP' => 'British Pound',
        'JPY' => 'Japanese Yen',
        'NGN' => 'Nigerian Naira',
        'AUD' => 'Australian Dollar',
        'CAD' => 'Canadian Dollar',
        'CHF' => 'Swiss Franc',
        'CNY' => 'Chinese Yuan',
        'SEK' => 'Swedish Krona',
        'NZD' => 'New Zealand Dollar',
        'MXN' => 'Mexican Peso',
        'SGD' => 'Singapore Dollar',
        'HKD' => 'Hong Kong Dollar',
        'NOK' => 'Norwegian Krone',
        'TRY' => 'Turkish Lira',
        'RUB' => 'Russian Ruble',
        'INR' => 'Indian Rupee',
        'BRL' => 'Brazilian Real',
        'ZAR' => 'South African Rand',
        'KRW' => 'South Korean Won',
        'PLN' => 'Polish Zloty',
        'THB' => 'Thai Baht',
        'IDR' => 'Indonesian Rupiah',
        'HUF' => 'Hungarian Forint',
        'CZK' => 'Czech Koruna',
        'ILS' => 'Israeli Shekel',
        'CLP' => 'Chilean Peso',
        'PHP' => 'Philippine Peso',
        'AED' => 'UAE Dirham',
        'COP' => 'Colombian Peso',
        'SAR' => 'Saudi Riyal',
        'MYR' => 'Malaysian Ringgit',
        'RON' => 'Romanian Leu',
        'AFN' => 'Afghan Afghani',
        'ALL' => 'Albanian Lek',
        'DZD' => 'Algerian Dinar',
        'AOA' => 'Angolan Kwanza',
        'XCD' => 'East Caribbean Dollar',
        'ARS' => 'Argentine Peso',
        'AMD' => 'Armenian Dram',
        'AWG' => 'Aruban Florin',
        'AZN' => 'Azerbaijani Manat',
        'BSD' => 'Bahamian Dollar',
        'BHD' => 'Bahraini Dinar',
        'BDT' => 'Bangladeshi Taka',
        'BBD' => 'Barbadian Dollar',
        'BYR' => 'Belarusian Ruble',
        'BZD' => 'Belize Dollar',
        'XOF' => 'West African CFA franc',
        'BMD' => 'Bermudian Dollar',
        'BTN' => 'Bhutanese Ngultrum',
        'BOB' => 'Bolivian Boliviano',
        'BAM' => 'Bosnia-Herzegovina Convertible Mark',
        'BWP' => 'Botswanan Pula',
        'BND' => 'Brunei Dollar',
        'BGN' => 'Bulgarian Lev',
        'BIF' => 'Burundian Franc',
        'KHR' => 'Cambodian Riel',
        'XAF' => 'Central African CFA franc',
        'CVE' => 'Cape Verdean Escudo',
        'KYD' => 'Cayman Islands Dollar',
        'KMF' => 'Comorian Franc',
        'CRC' => 'Costa Rican Colón',
        'HRK' => 'Croatian Kuna',
        'CUP' => 'Cuban Peso',
        'CYP' => 'Cypriot Pound',
        'CDF' => 'Congolese Franc',
        'DKK' => 'Danish Krone',
        'DJF' => 'Djiboutian Franc',
        'DOP' => 'Dominican Peso',
        'EGP' => 'Egyptian Pound',
        'SVC' => 'Salvadoran Colón',
        'ERN' => 'Eritrean Nakfa',
        'EEK' => 'Estonian Kroon',
        'ETB' => 'Ethiopian Birr',
        'FKP' => 'Falkland Islands Pound',
        'FJD' => 'Fijian Dollar',
        'XPF' => 'CFP Franc',
        'GMD' => 'Gambian Dalasi',
        'GEL' => 'Georgian Lari',
        'GHC' => 'Ghanaian Cedi',
        'GIP' => 'Gibraltar Pound',
        'GTQ' => 'Guatemalan Quetzal',
        'GNF' => 'Guinean Franc',
        'GYD' => 'Guyanaese Dollar',
        'HTG' => 'Haitian Gourde',
        'HNL' => 'Honduran Lempira',
        'ISK' => 'Icelandic Króna',
        'IRR' => 'Iranian Rial',
        'IQD' => 'Iraqi Dinar',
        'JMD' => 'Jamaican Dollar',
        'JOD' => 'Jordanian Dinar',
        'KZT' => 'Kazakhstani Tenge',
        'KES' => 'Kenyan Shilling',
        'KWD' => 'Kuwaiti Dinar',
        'KGS' => 'Kyrgystani Som',
        'LAK' => 'Laotian Kip',
        'LVL' => 'Latvian Lats',
        'LBP' => 'Lebanese Pound',
        'LSL' => 'Lesotho Loti',
        'LRD' => 'Liberian Dollar',
        'LYD' => 'Libyan Dinar',
        'LTL' => 'Lithuanian Litas',
        'MOP' => 'Macanese Pataca',
        'MKD' => 'Macedonian Denar',
        'MGA' => 'Malagasy Ariary',
        'MWK' => 'Malawian Kwacha',
        'MVR' => 'Maldivian Rufiyaa',
        'MTL' => 'Maltese Lira',
        'MRO' => 'Mauritanian Ouguiya',
        'MUR' => 'Mauritian Rupee',
        'MDL' => 'Moldovan Leu',
        'MNT' => 'Mongolian Tugrik',
        'MAD' => 'Moroccan Dirham',
        'MZN' => 'Mozambican Metical',
        'MMK' => 'Myanma Kyat',
        'NAD' => 'Namibian Dollar',
        'NPR' => 'Nepalese Rupee',
        'ANG' => 'Netherlands Antillean Guilder',
        'NIO' => 'Nicaraguan Córdoba',
        'KPW' => 'North Korean Won',
        'OMR' => 'Omani Rial',
        'PKR' => 'Pakistani Rupee',
        'PAB' => 'Panamanian Balboa',
        'PGK' => 'Papua New Guinean Kina',
        'PYG' => 'Paraguayan Guarani',
        'PEN' => 'Peruvian Nuevo Sol',
        'QAR' => 'Qatari Rial',
        'RWF' => 'Rwandan Franc',
        'SHP' => 'Saint Helena Pound',
        'WST' => 'Samoan Tala',
        'STD' => 'São Tomé and Príncipe Dobra',
        'RSD' => 'Serbian Dinar',
        'SCR' => 'Seychellois Rupee',
        'SLL' => 'Sierra Leonean Leone',
        'SKK' => 'Slovak Koruna',
        'SBD' => 'Solomon Islands Dollar',
        'SOS' => 'Somali Shilling',
        'LKR' => 'Sri Lankan Rupee',
        'SDD' => 'Sudanese Dinar',
        'SRD' => 'Surinamese Dollar',
        'SZL' => 'Swazi Lilangeni',
        'SYP' => 'Syrian Pound',
        'TWD' => 'New Taiwan Dollar',
        'TJS' => 'Tajikistani Somoni',
        'TZS' => 'Tanzanian Shilling',
        'TOP' => 'Tongan Paʻanga',
        'TTD' => 'Trinidad and Tobago Dollar',
        'TND' => 'Tunisian Dinar',
        'TMM' => 'Turkmenistani Manat',
        'UGX' => 'Ugandan Shilling',
        'UAH' => 'Ukrainian Hryvnia',
        'UYU' => 'Uruguayan Peso',
        'UZS' => 'Uzbekistan Som',
        'VUV' => 'Vanuatu Vatu',
        'VEF' => 'Venezuelan Bolívar',
        'VND' => 'Vietnamese Dong',
        'YER' => 'Yemeni Rial',
        'ZMK' => 'Zambian Kwacha',
        'ZWD' => 'Zimbabwean Dollar',
    ];
}
