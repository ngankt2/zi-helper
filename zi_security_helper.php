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

use ZiBase\Helpers\ZiHelper;
use ZiBase\Helpers\ZiSecurity;

/**
 * @param $string
 * @return bool|string
 */
function build_encrypt_string($string): bool|string
{
    return ZiSecurity::encryptString($string);
}

function build_token_with_session($string): string
{
    return ZiSecurity::buildTokenWithSession($string);
}


function zi_build_sid($id, $table): string
{
    return ZiSecurity::buildSID($id, $table);
}

function zi_encrypt_other($string): bool|string
{
    return ZiSecurity::encryptOther($string);
}

function zi_decrypt_other($string): bool|string
{
    return ZiSecurity::decryptOther($string);
}

function hide_string($string, $start = 0, $length = 0, $re = '*'): bool|string
{
    return ZiHelper::hideStr($string, $start, $length, $re);
}

function zi_role_is_author($account = false): bool
{
    if (!$account) {
        $account = auth()->user();
    }
    return zi_role_is_content_manager($account) || (!empty($account->zi_role) && $account->zi_role == \App\Models\User::ROLE_AUTHOR);
}

function zi_role_is_content_manager($account = false): bool
{
    if (!$account) {
        $account = auth()->user();
    }
    return zi_role_is_admin($account) || (!empty($account->zi_role) && $account->zi_role == \App\Models\User::ROLE_CONTENT_MANAGER);
}

function zi_role_is_admin($account = false): bool
{
    if (!$account) {
        $account = auth()->user();
    }
    return !empty($account->zi_role) && $account->zi_role == \App\Models\User::ROLE_ADMIN;
}

if (!function_exists('zi_role_can')) {

    function zi_role_can($permission): bool
    {
        if(empty(auth()->user())){
            return false;
        }
        return auth()->user()?->zi_can($permission);
    }
}

if (!function_exists('zi_role_can_not_message')) {

    function zi_role_can_not_message($permission): string
    {
        return _lang("validation.account_cannot_access", "", [ "account" => auth()->user()->account ]) . '. #' . strip_tags(_lang_without_sync("admin::permission.".$permission));
    }
}

if (!function_exists('zi_validate_action_with_reason')) {

    function zi_validate_action_with_reason($require_reason = 'nullable', $msg_if_null = ''): void
    {
        request()->validate([
            'reason'   => $require_reason . '|string|min:2|max:225',
            'token'    => [ 'required', 'string', function ($attribute, $value, $fail) {
                if (!ZiSecurity::validateTokenWithSession($value, 'token')) {
                    return $fail(_lang('validation.checksum_invalid_with_flag', '', [ 'flag' => '@token' ]));
                }
            }, ],
            're_token' => [ 'required', 'string', function ($attribute, $value, $fail) {
                //Token băm bởi lý do
                if (!ZiSecurity::validateTokenWithSession($value, request('reason') . request('slink'))) {
                    return $fail(_lang('validation.checksum_invalid_with_flag', '', [ 'flag' => '@re_token' ]));

                }
            }, ]
        ]
        );
    }
}

if (!function_exists('zi_view_not_permission')) {

    function zi_view_not_permission($permission = '', $template = 'admin/_error/cannot-access'): \Illuminate\Http\Response
    {
        $_title = __('validation.account_cannot_access', [ 'account' => auth()->user()?->account ]);
        if ($permission) {
            $_title .= " <br/>(#" . _lang_without_sync("admin::permission." . $permission) . ") ";
        }
        $_title .= '. <br/> <i>' . __('admin::global.if_is_bug').'</i>';
        return response()->view($template, [ 'heading' => __('validation.permission_denied'),
                                             "msg"     => $_title ]);
    }
}

if (!function_exists('zi_view_not_permission_as_modal')) {

    function zi_view_not_permission_as_modal($heading = '', $title = ''): \Illuminate\Http\Response
    {
        return \ZiBase\Helpers\ZiView::outputAsModalPermissionDenied('');
        //return zi_view_not_permission($heading, $title, 'admin/_error/modal-not-permission');
    }
}

if (!function_exists('zi_role_get_list')) {

    function zi_role_get_list(): array
    {
        return \App\Models\User::getConstRoles();
    }
}



