<?php

namespace ZiBase\Helpers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use ZiBase\Models\Menu\ZiMenuModel;
use ZiBase\Models\ZiConfig;

/**
 * Customer view resource folder
 */
class ZiView
{

    static mixed $clientVersion = '';

    static function getClientVersion(): string
    {
        if (config('app.debug')) {
            self::$clientVersion = (string)time();
        }
        if (isset($_GET['cache'])) {
            Cache::put('clientVersion', (string)time(), 99999999);
        }
        if (self::$clientVersion === '') {
            self::$clientVersion = Cache::get('clientVersion');
            if (!self::$clientVersion) {
                Cache::put('clientVersion', (string)time(), 99999999);
                self::$clientVersion = (string)time();
            }
        }
        return self::$clientVersion;
    }

    /****
     * @param $dir
     * @param $template
     * @param $var
     * @return Application|Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    static function setView($dir, $template, $var = [])
    {
        if ($dir) {
            if ($dir == 'themes') {
                View::addLocation(base_path('themes'));
                $folder = '/';
            } else {
                View::addLocation($dir);
                $folder = '/views/';
            }
        } else {
            $folder = '';
        }
        return view($folder . $template, $var);
    }


    static function getView($dir, $template, $var = []): string
    {
        if ($dir) {
            View::addLocation($dir);
            $folder = '/views/';
        } else {
            $folder = '';
        }
        return view($folder . $template, $var)->render();

    }


    /**
     * Ouput data ra dạng json (thường sử dụng cho output api hoặc ajax reponse...)
     * @param $json
     */
    static function outputJson($json, $statuscode = 200)
    {
        if (config('app.debug')) {
            $json['debug']['sql']    = DB::getQueryLog();
            $json['debug']['post']   = $_POST;
            $json['debug']['get']    = $_GET;
            $json['debug']['cookie'] = $_COOKIE;
            //$json['debug']['raw'] = file_get_contents('php://input');
        }
        @app('debugbar')->disable();
        return response()->json($json, $statuscode);
        /* header('Content-Type: application/json; charset=utf-8');
         die(json_encode($json));*/
    }


    static function outputJsonError($msg, $keyerror = '')
    {
        $json['status'] = 0;
        $json['msg']    = $msg;
        $json['key']    = $keyerror;

        return self::outputJson($json);
    }

    static function outputJsonSuccess($msg, $data)
    {
        $json['status'] = 1;
        $json['msg']    = $msg;
        $json['data']   = $data;

        return self::outputJson($json);
    }

    static function outputJsonPermissionDenied($permission): \Illuminate\Http\JsonResponse
    {
        return self::outputJsonErrorNotif(_lang("validation.account_cannot_access","",["account"=>auth()->user()->account]) . '. #Permission: ' . strip_tags(_lang_without_sync("admin::permission." . $permission)));
    }

    static function outputJsonErrorNotif($msg, $key = '', $data = [])
    {
        $json['status']       = 0;
        $json['notification'] = 'error';
        $json['msg']          = $msg;
        $json['data']         = $data;
        return self::outputJson($json);
    }

    static function outputJsonSuccessNotif($msg, $data = [])
    {
        $json['status']       = 1;
        $json['notification'] = 'success';
        $json['msg']          = $msg;
        $json['data']         = $data;
        return self::outputJson($json);
    }

    static function outputJsonInfoNotif($msg, $data = [])
    {
        $json['status']       = 1;
        $json['notification'] = 'info';
        $json['msg']          = $msg;
        $json['data']         = $data;
        return self::outputJson($json);
    }

    static function outputJsonWarningNotif($msg, $data = [])
    {
        $json['status']       = 1;
        $json['notification'] = 'warning';
        $json['msg']          = $msg;
        $json['data']         = $data;
        return self::outputJson($json);
    }

    static function outputJsonException($msg, $key = '', $status_code = 442, $data = [])
    {
        $json['status']       = 0;
        $json['errors'][$key] = $msg;
        $json['data']         = $data;
        return self::outputJson($json, $status_code);
    }

    public static function outputAsModal($content=null): \Illuminate\Http\Response
    {
        return response()->view('admin/_error/modal-error', [ 'content' => $content ]);
    }
    public static function outputAsModalPermissionDenied($permission): \Illuminate\Http\Response
    {
        return response()->view('admin/_error/modal-error', [ 'content' => _lang("validation.account_cannot_access","",["account"=>auth()->user()->account]) . '. #Permission: ' . strip_tags(_lang_without_sync("admin::permission." . $permission)) ]);
    }
    public static function outputFrontEnd404()
    {
        return response()->view("errors/404");
    }


}
