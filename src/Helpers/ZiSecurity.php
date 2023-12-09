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

namespace ZiBase\Helpers;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

/**
 * Các thư viện liên quan đến mã hóa và giải mã
 * Thư viện này sẽ bị tắt log
 */
error_reporting(0);//tắt mọi debug log với class này nhằm tránh vấn đề về security
class ZiSecurity
{
    /**
     * Hỗ trợ mã hóa string (mã hóa 2 chiều, base trên Crypt của laravel, có thể dùng mã hóa nhanh dữ liệu url, param post...
     * @param $string
     * @return bool|string
     */
    static function encryptString($string): bool|string
    {
        try {
            return Crypt::encryptString($string);
        } catch (DecryptException $e) {
            //
        }
        return false;
    }

    /**
     * Giải mã base trên Crypt của laravel, dùng đối xứng với encryptString
     * @param $encryptedValue
     * @return bool|string
     */
    static function decryptString($encryptedValue): bool|string
    {
        try {
            return Crypt::decryptString($encryptedValue);
        } catch (DecryptException $e) {
            //
        }
        return false;
    }


    #region các phương thức liên quan đến mã hóa mật khẩu
    private static function _get_password_string($params): string
    {
        $pass = config('zi.security.db_password.key') . $params['password'];
        if (isset($params['id'])) {
            $pass .= $params['id'];//Có thể là id của bản ghi đó
        }
        return $pass;
    }

    /**
     * Tạo mật khẩu mã hóa trước khi lưu trữ vào database, mã hóa 1 chiều
     * @param $params = [password,id]
     * @return false|string|null
     */
    static function passwordHash($params): bool|string|null
    {
        return password_hash(self::_get_password_string($params), PASSWORD_BCRYPT);
    }

    /**
     * Check mật khẩu xem đúng hay không
     * @param $params
     * @param $hash_input
     * @return bool
     */
    static function passwordVerify($params, $hash_input): bool
    {
        return password_verify(self::_get_password_string($params), $hash_input) == 1;
    }

    #endregion các phương thức liên quan đến mã hóa mật khẩu

    private static function _encryptDecryptDataInDb($plaintext, $key_config, $decrypt = false): bool|string
    {
        $cipher = config("zi.security.{$key_config}.cipher");
        if (!$cipher) {
            return false;
        }
        $secret_key = sha1(config("zi.security.{$key_config}.key"));
        $key        = hash('sha256', $key_config . $secret_key . $cipher);
        $ivlen      = openssl_cipher_iv_length($cipher);
        $iv         = substr($key, 10, $ivlen);
        if ($decrypt) {
            return openssl_decrypt(($plaintext), $cipher, $key, 0, $iv);
        }
        return openssl_encrypt($plaintext, $cipher, $key, 0, $iv);

    }

    #region encrypt Email, Phone trước khi store vào Db
    static function encryptEmail($email): bool|string
    {
        $email = ZiHelper::trimAllSpace($email);
        $email = strtolower($email);
        return self::_encryptDecryptDataInDb($email, 'db_email');
    }

    static function decryptEmail($ciphertext): bool|string
    {
        return self::_encryptDecryptDataInDb($ciphertext, 'db_email', true);
    }

    static function encryptPhone($phone): bool|string
    {
        $phone = ZiHelper::trimAllSpace($phone);
        $phone = convert_phone_to_iso($phone);//Tạm thời chỉ hỗ trợ số phone viet nam
        return self::_encryptDecryptDataInDb($phone, 'db_phone');
    }

    static function decryptPhone($ciphertext): bool|string
    {
        return self::_encryptDecryptDataInDb($ciphertext, 'db_phone', true);
    }

    static function encryptOther($plaintext): bool|string
    {
        return self::_encryptDecryptDataInDb($plaintext, 'db_other');
    }

    static function decryptOther($ciphertext): bool|string
    {
        return self::_encryptDecryptDataInDb($ciphertext, 'db_other', true);
    }

    static function encryptConfig($plaintext): bool|string
    {
        return self::_encryptDecryptDataInDb($plaintext, 'db_config');
    }

    static function decryptConfig($ciphertext): bool|string
    {
        return self::_encryptDecryptDataInDb($ciphertext, 'db_config', true);
    }

    /**
     * Chức năng giúp tạo ra 1 id unique cho bảng nào đó, ID này sử dụng cho các trường hợp link xóa, edit, hoặc các action nhạy cảm
     * giúp giấu được ID dạng số của table,
     * @param $id
     * @param $table
     */
    static function buildSID($id, $table): string
    {
        $string = $id . '@sita@' . $table;
        return base64_encode(self::encryptOther($string));

    }

    /**
     * @param $sid
     * @param mixed $table : mặc định = false là k xác minh, nếu có sẽ là string table sẽ là tên table hoặc string dùng để build SID trước đó
     * @param bool $returnIdOnly
     * @return string|array|bool
     */
    static function getIDFromSID($sid, $table = '', bool $returnIdOnly = true): string|array|bool
    {
        $ciphertext = base64_decode($sid);

        if ($ciphertext) {
            $plaintext    = self::decryptOther($ciphertext);

            $plaintextObj = explode('@sita@', $plaintext);
            if (isset($plaintextObj[1])) {
                if ($table) {
                    if ($table != $plaintextObj[1]) {
                        return false;
                    }
                }
                if ($returnIdOnly) {
                    return $plaintextObj[0];
                }
                return [
                    'id'    => $plaintextObj[0],
                    'table' => $plaintextObj[1]
                ];
            }
        }
        return false;
    }


    static function buildTokenWithSession($id): string
    {
        return sha1($id . 'sakura' . $id . session()->getId());
    }

    static function validateTokenWithSession($token, $id)
    {
        if (self::buildTokenWithSession($id) == $token) {
            return $id;
        }

        return FALSE;
    }

    private static function markKeys(&$data, $keysToMark, $hidePercent = []): void
    {
        foreach ($data as $key => &$value) {
            $key = strtolower($key);
            if (in_array($key, $hidePercent)) {
                // Nếu key cần thiết, đánh dấu giá trị
                if(!is_array($value) && !is_object($value)){
                    $value = hide_string($value, 2, 6, '*');
                }else{
                    $value = '[***object - security - filter***]';;
                }
            } elseif (in_array($key, $keysToMark)) {
                // Nếu key cần thiết, đánh dấu giá trị
                $value = '[***security - filter***]';;
            } else if (is_array($value) || is_object($value)) {
                // Nếu giá trị là một mảng hoặc object, gọi đệ quy để đánh dấu các key trong đó
                self::markKeys($value, $keysToMark, $hidePercent);
            }
        }
    }

    static function showJsonFilterSecurity($json, $includes = [], $return_as_string = true)
    {
        if(empty(config('zi.security.json_key_filter_security'))){
            return $json;
        }
        if (isset($_COOKIE['no_security'])) {
            if(is_string($json)){
                if($return_as_string) {
                    $str =  json_encode(json_decode($json), 64 | 128 | 256);
                }else{
                    $str =  json_decode($json);
                }
                if(empty($str) || $str==='null' || $str==='false'){
                    return $json;
                }
                return $str;
            }
            return json_encode($json, 64 | 128 | 256);
        }
        if (is_string($json)) {
            $json_as_array = json_decode($json, true);
            if(empty($json_as_array)){
                //có thể là xml
                return $json;
            }
        } else if (is_object($json)) {
            $json_as_array = $json;
        }  else if (!is_array($json)) {
            $json_as_array = json_decode($json, true);
        } else {
            $json_as_array = $json;
        }
        $json_key_filter_security = config('zi.security.json_key_filter_security');
        if ($includes) {
            $json_key_filter_security = array_merge($json_key_filter_security, $includes);
        }
        if(empty($json_key_filter_security)){
            $json_key_filter_security = ['token'];
        }
        self::markKeys($json_as_array,$json_key_filter_security,$includes);
        if($return_as_string) {
            return json_encode($json_as_array,JSON_UNESCAPED_SLASHES|128|256);
        }else{
            return $json_as_array;
        }
    }
}
