<?php
/*
 * Copyright (c) 2023 by ZiTeam. All rights reserved.
 *
 * This software product, including its source code and accompanying documentation, is the proprietary product of ZiTeam. The product is protected by copyright and other intellectual property laws. Unauthorized copying, sharing, or distribution of this software, in whole or in part, without the explicit permission of ZiTeam is strictly prohibited.
 *
 * The purchase and use of this software product must be authorized by ZiTeam through a valid license agreement. Any use of this software without a proper license agreement is considered a violation of copyright law.
 *
 * ZiTeam retains all ownership rights and intellectual property rights to this software product. No part of this software, including the source code, may be reproduced, modified, reverse-engineered, or distributed without the express written permission of ZiTeam.
 *
 * For inquiries regarding licensing and permissions, please contact ZiTeam at codezi.pro@gmail.com.
 *
 */

use Illuminate\Support\Facades\Validator;
use ZiBase\Helpers\ZiBug;
use ZiBase\Helpers\ZiView;

if (!function_exists('zi_asset_link')) {
    /**
     * @param $link
     * @param bool $return_link
     * @return string
     */
    function zi_asset_link($link): string
    {
        return asset($link) . '?v=' . ZiView::getClientVersion();
    }
}
if (!function_exists('zi_link_css')) {
    /**
     * @param $link
     * @param bool $return_link
     * @return string
     */
    function zi_link_css($link, bool $return_link = false): string
    {
        if ($return_link) {
            return asset($link) . '?v=' . ZiView::getClientVersion();
        }
        return '<link href="' . asset($link) . '?v=' . ZiView::getClientVersion() . '" rel="stylesheet">';
    }
}
if (!function_exists('zi_link_js')) {
    /**
     * @param $link
     * @param bool $return_link
     * @return string
     */
    static $lsLinkJs;
    function zi_link_js($link, bool $return_link = false): string
    {
        global $lsLinkJs;
        if (!empty($lsLinkJs[$link])) {
            //return '';
        }
        $lsLinkJs[$link] = 1;
        if ($return_link) {
            return asset($link) . '?v=' . ZiView::getClientVersion();
        }
        return '<script type="text/javascript" src="' . asset($link) . '?v=' . ZiView::getClientVersion() . '"></script>';
    }
}
if (!function_exists('zi_bug')) {
    /**
     * @param $obj
     * @param string $label
     * @param string $color
     * @return void
     */
    function zi_bug($obj, string $label = '', string $color = '#ffcebb'): void
    {
        ZiBug::show($obj, $label, $color);
    }
}
if (!function_exists('zi_model_to_string_name')) {

    function zi_model_to_string_name($model_as_name_space)
    {
        if (str_contains($model_as_name_space, 'ZiPost')) {
            return 'Post';
        }
        if (str_contains($model_as_name_space, 'ZiPage')) {
            return 'Page';
        }
        if (str_contains($model_as_name_space, 'Category')) {
            return 'Category';
        }
        if (str_contains($model_as_name_space, 'Tag')) {
            return 'Tag';
        }
        return $model_as_name_space;
    }
}
if (!function_exists('zi_html_build_options_for_select')) {
    /**
     * @param $array
     * @param $parentId
     * @param $prefix
     * @return string
     * Build select option [loop]
     */
    function zi_html_build_options_for_select($array, $parentId = 0, $prefix = '', $selected = 0): string
    {
        $html = '';

        foreach ($array as $item) {
            if ($item['parent_id'] == $parentId) {
                $_selected = '';
                if ($selected == $item['id'] || (is_array($selected) && in_array($item['id'], $selected))) {
                    $_selected = ' selected ';
                }
                $html .= '<option value="' . $item['id'] . '" ' . $_selected . '>' . $prefix . $item['name'] . '</option>';
                $html .= zi_html_build_options_for_select($array, $item['id'], $prefix . '--', $selected);
            }
        }

        return $html;
    }
}

if (!function_exists('zi_html_build_category_checklist')) {

    function zi_html_build_category_checklist($categories, $selectedIds = array(), $parentId = 0, $indentation = 0, $id = 'zi_categories'): string
    {
        $html = '';
        foreach ($categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $checkboxId = $id . 'category_' . $category['id']; // Tạo id duy nhất cho checkbox

                $html .= '<div class="form-check form-switchx d-flex mb-2">';

                // Thụt lề bằng cách sử dụng thẻ <pre>
                $html .= '<pre style="display:inline-block; width:' . $indentation * 20 . 'px"></pre>';

                $checked = in_array($category['id'], $selectedIds) ? 'checked' : '';

                $html .= '<input name="' . $id . '[]" class="form-check-input me-1" type="checkbox" id="' . $checkboxId . '" value="' . $category['id'] . '" ' . $checked . '>';
                $html .= '<label class="form-check-label" for="' . $checkboxId . '">' . $category['name'] . '</label>';
                $html .= '</div>';

                $html .= zi_html_build_category_checklist($categories, $selectedIds, $category['id'], $indentation + 1, $id);
            }
        }
        return $html;
    }
}
if (!function_exists('zi_html_build_menu_struct_setting')) {

    function zi_html_build_menu_struct_setting($menuItems, $parentId = 0, $level = 0): string
    {
        $html = '';
        foreach ($menuItems as $menuItem) {
            if ($menuItem['parent_id'] == $parentId) {
                //$checkboxId = 'category_' . $menuItem['id']; // Tạo id duy nhất cho checkbox
                $id    = $menuItem['id'];
                $label = $menuItem['name'];
                $url   = $menuItem['id'];
                $html  .= '<li class="dd-item dd3-item" data-id="' . $id . '" data-label="' . $label . '" data-url="' . $url . '">' .
                    '<div class="dd-handle dd3-handle" > Drag</div>' .
                    '<div class="dd3-content"><span>' . $label . '</span>' .
                    '<div class="item-edit">Edit</div>' .
                    '</div>' .
                    '<div class="item-settings d-none">' .
                    '<p><label for="">Navigation Label<br><input type="text" name="navigation_label" value="' . $label . '"></label></p>' .
                    '<p><label for="">Navigation Url<br><input type="text" name="navigation_url" value="' . $url . '"></label></p>' .
                    '<p><a class="item-delete" href="javascript:;">Remove</a> |' .
                    '<a class="item-close" href="javascript:;">Close</a></p>' .
                    '</div></li>';

                $html .= zi_html_build_menu_struct_setting($menuItems, $menuItem['id'], $level + 1);
            }
        }
        return $html;
    }
}


if (!function_exists('_lang')) {
    /**
     * Function hỗ trợ call biến output label multilanguage, cách dùng trong blade: {{lang('string.phone_require',"Mật khẩu khong được để trống")}}
     * @param $key
     * @param string $default
     * @param array $replace
     * @param null $locale
     * @return mixed|string|null
     */
    function _lang($key, $default = '', $replace = [], $locale = null): mixed
    {
        if (is_null($key)) {
            return null;
        }

        $string = trans($key, $replace, $locale);
        if (empty($string) || ($string == $key && !blank($default))) {
            if ($default) {
                if (!empty($replace)) {
                    foreach ($replace as $placeholder => $value) {
                        $default = str_replace(':' . $placeholder, $value, $default);
                    }
                }
                $string = $default;
            } else {
                $string = $key;
            }

        }

        ZiBug::setLangKey($key, $string);
        $q = request('show_language_key');
        if ($q && \ZiBase\Helpers\ZiSecurity::validateTokenWithSession($q, 'show_language_key')) {
            return $key;
        }
        return $string;
    }
}

function _lang_without_sync($key, $default = '', $replace = [], $locale = null)
{
    return _lang($key, $default, $replace, $locale);
}


if (!function_exists('zi_is_image')) {

    /**
     * @param $mine_type
     * @return string
     * Check mine_type is image
     */
    function zi_is_image($mine_type): string
    {
        return str_contains($mine_type, 'image/');
    }
}
if (!function_exists('zi_category_add_link')) {


    /**
     * @return string
     * Build link add for category page
     */
    function zi_category_add_link($type, $object = ''): string
    {
        return \ZiBase\Models\Content\ZiCategoryModel::link_add($type, $object);
    }
}


if (!function_exists('zi_category_get_child_ids')) {

    /**
     * @param $categories
     * @param $parentId
     * @return array
     */
    function zi_category_get_child_ids($categories, $parentId): array
    {
        $childIds = array();

        foreach ($categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $childIds[] = $category['id'];

                $childIds = array_merge($childIds, zi_category_get_child_ids($categories, $category['id']));
            }
        }

        return $childIds;
    }
}


if (!function_exists('zi_validate_listing')) {

    /**
     * Validator for listing and redirect to url without errors key
     */
    function zi_validate_listing($validateRule, $data = [], $redirect_to = null): void
    {
        $validator = Validator::make($data ?: request()->all(), $validateRule);
        if ($validator->fails()) {
            $errors    = $validator->errors();
            $keyErrors = $errors->keys();
            throw new \Illuminate\Http\Exceptions\HttpResponseException(redirect()->to($redirect_to ?: request()->fullUrlWithoutQuery($keyErrors))->withErrors($validator->errors()));
        }
    }
}
if (!function_exists('zi_redirect_error')) {

    function zi_redirect_error($url, $dataError = []): void
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(redirect()->to($url)->withErrors($dataError)->withInput());
    }
}

if (!function_exists('zi_show_date')) {

    /**
     * @throws Exception
     */
    function zi_show_date($date, $format = null, $error_string = '')
    {
        if (!$date) {
            return $error_string;
        }
        if (empty($format)) {
            $format = config('zi.website.date_format');
        }
        $_date = new DateTime($date);
        return $_date->format($format);
    }

    /**
     * @throws Exception
     */
    function zi_show_date_time($date, $format = null, $error_string = '')
    {
        if (empty($format)) {
            $format = config('zi.website.date_format') . ' ' . config('zi.website.time_format');
        }
        return zi_show_date($date, $format, $error_string);
    }
}


if (!function_exists('zi_show_increment')) {

    function zi_show_increment($key): string
    {
        return str_pad($key + 1, 2, '0', STR_PAD_LEFT);
    }

}


if (!function_exists('routex')) {

    function routex($name, $params = [], $absolute = true): string
    {
        return route($name, $params, $absolute);
    }
}


if (!function_exists('zi_is_desktop')) {

    function zi_is_desktop(): bool
    {
        $useragent = @$_SERVER['HTTP_USER_AGENT'];
        return $useragent && !preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4));
    }
}
if (!function_exists('zi_is_mobile')) {

    function zi_is_mobile(): bool
    {
        $useragent = @$_SERVER['HTTP_USER_AGENT'];
        return $useragent && preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4));
    }
}

if (!function_exists('convert_phone_to_iso')) {

    function convert_phone_to_iso($phone)
    {
        return $phone;
    }
}

if (!function_exists('zi_rand_string')) {

    function zi_rand_string($length = 16): string
    {
        return \Illuminate\Support\Str::random($length);
    }
}

if (!function_exists('zi_link_auth')) {

    function zi_link_auth($user): string
    {
        if (is_string($user)) {
            return url('auth/' . $user);
        } else {
            return url('auth/' . $user->account);
        }
    }
}

if (!function_exists('zi_add_params_to_url')) {

    function zi_add_params_to_url($url, $params): string
    {
        $query        = parse_url($url, PHP_URL_QUERY);
        $param_string = http_build_query($params);
        if ($query) {
            $url .= '&' . $param_string;
        } else {
            $url .= '?' . $param_string;
        }
        return $url;
    }
}
if (!function_exists('zi_load_menu_cms')) {

    function zi_load_menu_cms()
    {
        return \ZiBase\Helpers\ZiCmsHelper::listMainMenuItems();
    }
}

if (!function_exists('zi_load_quick_menu_cms')) {

    function zi_load_quick_menu_cms()
    {
        return \ZiBase\Helpers\ZiCmsHelper::$quickMenu;
    }
}

if (!function_exists('zi_color')) {

    function zi_color($string)
    {
        $hash = md5($string); // Calculate MD5 hash of the string

        $color = substr($hash, 0, 6);

        return "#" . $color;
    }
}


if (!function_exists('zi_time_ago')) {

    /**
     * @param $timestamp
     * @return string
     * @throws Exception
     *
     *
     * $timestamp = "2023-08-10 15:30:00"; // Replace this with your timestamp
     * $timeAgoString = zi_time_ago($timestamp);
     * echo $timeAgoString;
     */
    function zi_time_ago($timestamp): string
    {
        $time = new DateTime($timestamp);
        $now  = new DateTime();

        $interval = $now->diff($time);

        $years   = $interval->y;
        $months  = $interval->m;
        $days    = $interval->d;
        $hours   = $interval->h;
        $minutes = $interval->i;
        $seconds = $interval->s;

        if ($years > 0) {
            return $years . " year" . ($years > 1 ? "s" : "") . " ago";
        } elseif ($months > 0) {
            return $months . " month" . ($months > 1 ? "s" : "") . " ago";
        } elseif ($days > 0) {
            return $days . " day" . ($days > 1 ? "s" : "") . " ago";
        } elseif ($hours > 0) {
            return $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
        } elseif ($minutes > 0) {
            return $minutes . " minute" . ($minutes > 1 ? "s" : "") . " ago";
        } else {
            return $seconds . " second" . ($seconds > 1 ? "s" : "") . " ago";
        }
    }
}


