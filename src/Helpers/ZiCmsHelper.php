<?php
/*
 * Copyright (c) 2023  by ZiTeam. All rights reserved.
 *
 *  This software product, including its source code and accompanying documentation, is the proprietary product of ZiTeam. The product is protected by copyright and other intellectual property laws. Unauthorized copying, sharing, or distribution of this software, in whole or in part, without the explicit permission of ZiTeam is strictly prohibited.
 *
 *  The purchase and use of this software product must be authorized by ZiTeam through a valid license agreement. Any use of this software without a proper license agreement is considered a violation of copyright law.
 *
 * ZiTeam retains all ownership rights and intellectual property rights to this software product. No part of this software, including the source code, may be reproduced, modified, reverse-engineered, or distributed without the express written permission of ZiTeam.
 *
 * For inquiries regarding licensing and permissions, please contact ZiTeam at codezi.pro@gmail.com.
 *
 *
 */

namespace ZiBase\Helpers;

class ZiCmsHelper
{
    static array $menus = [];
    static array $quickMenu = [];
    static array $listSetting = [];
    static array $lsRoute = [];

    public static function getListSettings(): array
    {
        usort(self::$listSetting, function ($a, $b) {
            return $a['order'] > $b['order'];
        });
        return self::$listSetting;
    }
    public static function registerSetting($array){
        return self::$listSetting[] = $array;
    }
    public static function listMainMenuItems(): array
    {

        self::$menus = array_merge(config('zi.menu',[]), self::$menus);
        foreach (self::$menus as $key => $menu) {
            if (!empty($menu['sub_menu'])) {
                foreach ($menu['sub_menu'] as $ks => $item) {
                    if (!empty($item['permissions']) && !zi_role_can($item['permissions'])) {
                        unset(self::$menus[$key]['sub_menu'][$ks]);
                    }
                }
                usort(self::$menus[$key]['sub_menu'], function ($a, $b) {
                    return $a['order'] > $b['order'];
                });
            }
        }
        usort(self::$menus, function ($a, $b) {
            return $a['order'] > $b['order'];
        });
        return self::$menus;
    }

    public static function registerMenu($group, $title, $order = 1, $submenu = []): void
    {
        self::$menus[$group]['title'] = $title;
        self::$menus[$group]['order'] = $order;
        if (!empty($submenu)) {
            self::$menus[$group]['sub_menu'] = $submenu;
        }
    }

    public static function registerSubMenu($group, $submenu): void
    {
        self::$menus[$group]['sub_menu'][] = $submenu;
    }


    /**
     * @param $title
     * @param $icon
     * @param $route
     * @param $params
     * @return void
     * ZiCmsHelper::registerQuickMenu('admin::menu.document_create_new', 'mdi mdi-note-plus-outline', 'private.document.input');

     */
    public static function registerQuickMenu($title, $icon, $route, $params = []): void
    {
        self::$quickMenu[] = [
            'title'  => $title,
            'route'  => $route,
            'params' => $params,
            'icon'   => $icon,
        ];
    }

    public static function registerRoute($path, $order = 1): void
    {
        if (!isset(self::$lsRoute[$path])) {
            self::$lsRoute[$path] = [
                'path'  => $path,
                'order' => $order
            ];
        }
    }

    public static function getRegisterRoutes(): array
    {
        usort(self::$lsRoute, function ($a, $b) {
            return $a['order'] > $b['order'];
        });
        return self::$lsRoute;
    }

}
