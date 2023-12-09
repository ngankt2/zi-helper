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

if (!function_exists('zi_media_original')) {
    /**
     * @param $path
     * @param $width
     * @param $height
     * @return string
     */
    function zi_media_original($path): string
    {
        return url('/storage/' . $path);

    }
}

if (!function_exists('zi_media_crop')) {
    /**
     * @param $path
     * @param $width
     * @param $height
     * @return string
     */
    function zi_media_crop($path, $width, $height): string
    {
        return url('/storage/crop/' . $width . 'x' . $height . '/' . $path);
    }
}
if (!function_exists('zi_media_fit')) {
    /**
     * @param $path
     * @param $width
     * @param $height
     * @return string
     */
    function zi_media_fit($path, $width, $height): string
    {
        return url('/storage/fit/' . $width . 'x' . $height . '/' . $path);
    }
}
if (!function_exists('zi_media_thumb')) {
    /**
     * @param $path
     * @param $width
     * @param $height
     * @return string
     */
    function zi_media_thumb($path, $width, $height): string
    {
        return url('/storage/resize/' . $width . 'x' . $height . '/' . $path);
    }
}
if (!function_exists('zi_media_thumb_max')) {
    /**
     * @param $path
     * @param $width
     * @param $height
     * @return string
     */
    function zi_media_thumb_max($path): string
    {
        return url('/storage/max/' . $path);
    }
}
if (!function_exists('zi_media_resize')) {
    /**
     * @param $path
     * @param $width
     * @param $height
     * @return string
     */
    function zi_media_resize($path, $width, $height): string
    {
        return url('/storage/resize/' . $width . 'x' . $height . '/' . $path);
    }
}


if (!function_exists('zi_avatar')) {
    function zi_avatar($username, $size=46): string
    {
        $initials = strtoupper(substr($username, 0, 2));
        $hash = md5($username);
        $backgroundColor = substr($hash, 0, 6);

        $red = hexdec(substr($backgroundColor, 0, 2));
        $green = hexdec(substr($backgroundColor, 2, 2));
        $blue = hexdec(substr($backgroundColor, 4, 2));
        $brightness = ($red * 299 + $green * 587 + $blue * 114) / 1000;
        $textColor =  $brightness > 128 ? '000000' : 'ffffff';

        return '<div class="rounded-circle" style="display:flex;align-items: center;
    justify-content: center;background-color: #' . $backgroundColor . '; color: #' . $textColor . '; width: ' . $size . 'px; height: ' . $size . 'px; font-size: ' . ($size / 2) . 'px;">' . $initials . '</div>';
    }
}

if (!function_exists('zi_avatar_via_gravatar_url')) {
    function zi_avatar_via_gravatar_url($account ): string
    {
        $address = strtolower( trim( $account ) );
        // Create an MD5 hash of the final string
        $hash = md5( $address );
        // Grab the actual image URL
        return 'https://www.gravatar.com/avatar/' . $hash;
    }
}

if (!function_exists('zi_avatar_url')) {
    function zi_avatar_url($user ): string
    {
        if(!empty($user->id)){
            $string = $user->name??$user->account;
        }else{
            $string =$user;
        }
        $hash = md5( $user->id??$string );
        $sources = [
            //'https://api.dicebear.com/6.x/adventurer-neutral/svg?seed='.$string,
            //'https://api.dicebear.com/6.x/initials/svg?scale=90&seed='.$string,
            'https://api.dicebear.com/6.x/pixel-art/svg?seed='.$string,
            //'https://api.dicebear.com/6.x/fun-emoji/svg?seed='.$string,
            //'https://source.boringavatars.com/beam/120/'.$hash.'?colors=264653,f4a261,e76f51&square=true',
        ];
        $pattern = '/\d+/'; // Regular expression to match one or more digits
        preg_match_all($pattern, $hash, $matches);
        $numbers = implode('',$matches[0]);

        return $sources[$numbers % count($sources)];
    }
}

