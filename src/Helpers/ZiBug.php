<?php

namespace ZiBase\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class ZiBug
{
    const exclude_messages
                               = [
            'Unauthenticated.', 'The given data was invalid.', 'CSRF token mismatch.'
        ];
    const exclude_status_codes = [404, 405, 401];
    const exclude_agents       = ['TelegramBot (like TwitterBot)'];

    /**
     * Dump
     * @param $obj
     * @param string $label
     * @param string $color
     */
    static function show($obj, $label = '', $color = '#ffcebb')
    {
        echo "<pre style='border: 1px solid red;margin:3px;padding:3px;background-color:$color !important;max-height: 800px;overflow: auto'>";
        $debug = debug_backtrace();
        if ($label) {
            echo "<h2>$label</h2>";
        }
        echo ($debug[0]['file'] . ':' . $debug[0]['line']) . '<br/>';
        print_r($obj);
        echo "</pre>";
    }

    static function dump($obj, $label = '', $color = '#ffcebb')
    {
        $debug = debug_backtrace();
        $dump  = [
            'line' => $debug[0]['file'] . ':' . $debug[0]['line'],
            'obj'  => $obj
        ];
        dump($dump);
    }


    /**
     * Start timeline với Debugbar của laravel, dùng bắt đầu của một đoạn code mà bạn muốn đo thời gian thực thi
     * @param $string
     */
    static function startDebugTime($string)
    {
        if (\config('debugbar.enabled')) {
            start_measure($string);
        }
    }

    /**
     * End timeline với Debugbar của laravel, dùng đóng  một đoạn code mà bạn muốn đo thời gian thực thi trước đó đã sử dụng startDebugTime
     * @param $string
     */
    static function endDebugTime($string)
    {
        if (\config('debugbar.enabled')) {
            stop_measure($string);
        }
    }

    static function setDebugInfo($object, $label = '')
    {
        if (\config('debugbar.enabled')) {
            //stop_measure($string);
            \Debugbar::addMessage($object, $label);

        }
    }

    static function pushNotification($message): bool|string
    {
        if (4096 <= mb_strlen($message, 'UTF-8')) {
            $message = substr($message, 0, 4000);
        }

        $bot_token = \config('zi.debug.telegram_bot_token');
        $chat_id   = \config('zi.debug.telegram_chat_id');
        if (!$bot_token || !$chat_id) {
            return '';
        }

        $url  = "https://api.telegram.org/bot{$bot_token}/sendMessage";
        $data = array(
            'chat_id' => $chat_id,
            'text'    => $message,
        );

        $options = array(
            CURLOPT_URL            => $url,
            CURLOPT_POST           => 1,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HEADER         => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT        => 1,
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_HTTPHEADER     => array(
                'Content-Type: application/json',
                'Accept: application/json'
            )
        );
        $ch      = curl_init();
        curl_setopt_array($ch, $options);
        $re = curl_exec($ch);
        curl_close($ch);
        return $re;
    }

    static function trackingJsError()
    {

        $msg = \request('c');
        if ($msg) {
            $msg = base64_decode($msg);
        }
        $content  = base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==');
        $response = Response::make($content, 200);
        $response->header('Content-Type', 'image/gif');

        if (!$msg) {
            return $response;
        }

        $msg .= "\n";
        $msg .= self::_getDebugInfo();
        self::pushNotification($msg, \config('app.telegram_jsdebug_log_channel'));
        return $response;

    }

    static function _getDebugInfo()
    {
        $msg = "\n";
        $msg .= str_repeat('-', 30);
        $msg .= "\nREMOTE_ADDR: " . @$_SERVER['REMOTE_ADDR'];
        $msg .= "\nHTTP_USER_AGENT: " . @$_SERVER['HTTP_USER_AGENT'];
        $msg .= "\nHTTP_REFERER: " . @$_SERVER['HTTP_REFERER'];
        $msg .= "\nFULL_URL: " . request()->fullUrl();
        $msg .= "\nREQUEST_METHOD: " . @$_SERVER['REQUEST_METHOD'];
        $msg .= "\nSERVER_NAME: " . @$_SERVER['SERVER_NAME'];
        $msg .= "\nHTTP_HOST: " . @$_SERVER['HTTP_HOST'];
        //$msg .= "\nREQUEST_URI: " . @$_SERVER['REQUEST_URI'];
        $msg .= "\n" . str_repeat('-', 30);
        return $msg;
    }

    static function trackingPhpError($exception, $link_issue = '', $statusCode = '')
    {
        if ($exception instanceof ValidationException) {
            // Your code here
            return false;
        }

        if (!$statusCode) {
            if (method_exists($exception, 'getStatusCode')) {
                $statusCode = $exception->getStatusCode();
            }
        }
        if (in_array($statusCode, self::exclude_status_codes)
            || in_array(@$_SERVER['HTTP_USER_AGENT'], self::exclude_agents)
            || in_array($exception->getMessage(), self::exclude_messages)
            || $exception->getLine() == 1
        ) {
            return false;
        }

        if (app()->bound('sentry')) {

            $sentry_id  = app('sentry')->captureException($exception);
            $link_issue = config('zi.debug.sentry_debug_query_url') . $sentry_id;
        }

        $msg = "Link issues: " . $link_issue;
        $msg .= "\nMessage: " . $exception->getMessage();
        $msg .= "\nStatusCode: " . $statusCode;
        $msg .= "\nFile: " . $exception->getFile() . ':' . $exception->getLine();

        $msg .= self::_getDebugInfo();

        self::pushNotification($msg);
    }

    static function showLineSingle($string)
    {
        echo $string . "\n<br/>";
    }

    static function showSql($queryBuilder, $return = false)
    {
        $query = str_replace(array('?'), array('\'%s\''), $queryBuilder->toSql());
        $query = vsprintf($query, $queryBuilder->getBindings());
        if ($return) {
            return $query;
        }
        self::show($query);
    }

    static function getSql($queryBuilder)
    {
        if (\config('app.debug')) {
            return self::showSql($queryBuilder, true);
        }
    }

    static array $allLangKey = [];

    static function setLangKey($key, $value = ''): void
    {
        if (\config('debugbar.enabled')) {
            self::$allLangKey[$key] = $value;
        }
    }

    static function syncLang(): void
    {

        if (isset($_GET['dev_lang']) || isset($_COOKIE['dev_lang'])) {
            $content = @file_get_contents(storage_path('lang/lang.json'));
            if ($content) {
                $content = json_decode($content, true);
            } else {
                $content = [];
            }
            foreach (self::$allLangKey as $key => $value) {
                $content[$key] = "_lang('$key')";
            }

            try {
                file_put_contents(storage_path('lang/lang.json'), json_encode($content));
            } catch (\Exception $exception) {
                self::setDebugInfo($exception->getTraceAsString(), 'Exception in ' . __METHOD__);
            }
        }
    }
}
//v3
