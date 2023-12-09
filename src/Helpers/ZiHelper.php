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

use Carbon\Carbon;

class ZiHelper
{

    static function getIp(): mixed
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * @param $file_name
     * @return false|string
     */
    public static function getFileExtension($file_name)
    {
        return pathinfo($file_name, PATHINFO_EXTENSION);
    }

    /**
     * Format số 12345678 => 12.345.678
     * @param $stringNumber
     * @param string $sep
     * @return mixed|string|null
     */
    public static function numberFormat($stringNumber, $sep = '.')
    {
        if (null == gettype($stringNumber)) {
            return $stringNumber;
        }
        if (!$stringNumber) {
            return $stringNumber;
        }
        return number_format($stringNumber, 0, '', $sep);
    }

    /**
     * Convert số dạng format sang số thường ví dụ: 1.000 => 1000 hoặc 1,000 =>1000
     * @param $number
     * @param string[] $point
     * @return array|string|string[]
     */
    public static function convertStringToNumber($number, $point = [',', '.', ' '])
    {
        //todo: có thể bổ sung thêm việc xóa các ký tự khác "Không phải số"
        return str_replace($point, '', $number);
    }

    /**
     * Random String ngẫu nhiên theo độ dài nhất định
     * @param int $length
     * @param string $characters
     * @return string
     */
    public static function randomString($length = 8, $characters = '0123456789abcdefghilkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {

        $charactersLength = strlen($characters);
        $randomString     = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * Random string ngẫu nhiên nhưng không bao gồm số
     * @param int $length
     * @return string
     */
    public static function randomStringWithoutNumber($length = 8)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return self::randomString($length, $characters);
    }

    /**
     * Xóa toàn bộ dấu cách trong chuỗi bất kỳ
     * @param $string
     * @return string|null
     */
    public static function trimAllSpace($string)
    {
        $string = str_replace('&nbsp;', '', $string);
        $string = str_replace(' ', '', $string);
        return preg_replace('/\s+/', '', $string);
    }

    /**
     * Validate isEmail
     * @param $email
     * @return bool
     */
    static function isEmail($email)
    {
        return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email)) ? FALSE : TRUE;
    }

    /**
     * Validate số điện thoại => cái này tương đối
     * @param $number
     * @return false|int
     */
    static function isMobileNumber($number)
    {
        return preg_match("/^\+?\d{9,12}$/i", trim($number));
        //return preg_match("/^(01([0-9]{2})|09[0-9]|08[0-9]|05[0-9]|03[0-9]|)(\d{10})$/i", $number);
    }

    /**
     * Validate chuỗi ngày tháng có đúng định dạng hay ko
     * @param $str
     * @param string $format
     * @return bool
     */
    public static function isDatetime($str, $format = 'd/m/Y')
    {
        try {
            $d = Carbon::createFromFormat($format, $str);
        } catch (\InvalidArgumentException $e) {
            return FALSE;
        }

        return $d && $d->format($format) == $str;
    }

    /**
     * Tạo mã màu từ 1 chuỗi => có thể dùng cho các kịch bản như gen avatar với màu theo name, hoặc màu text theo name....
     * @param $string
     * @return string
     */
    static function genCorlorRand($string)
    {
        return '#' . substr(md5($string), 0, 6);
    }

    static function isDesktop()
    {
        $useragent = @$_SERVER['HTTP_USER_AGENT'];
        return !preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4));
    }

    /**
     * Check xem có phải url chuẩn hay không?
     * @param $link
     * @return mixed
     */
    static function isLink($link)
    {
        return filter_var($link, FILTER_VALIDATE_URL);
    }


    static function validateDateTime($date, $format = 'd/m/Y H:i')
    {
        $d = \DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }

    /**
     * @param $date_time_string ví dụ : 24/05/2018 08:20
     * @param string $format 'd/m/Y H:i'
     * @return \MongoDB\BSON\UTCDateTime
     *
     */
    static function getMongoDateTime($date_time_string = FALSE, $format = 'd/m/Y H:i')
    {
        if (!$date_time_string) {
            return new \MongoDB\BSON\UTCDateTime(strtotime('now') * 1000);
        }
        $date = strtotime(Carbon::createFromFormat($format, $date_time_string)->toDateTimeString());
        return new \MongoDB\BSON\UTCDateTime($date * 1000);
    }

    static function randMongoDateTime($from = '1 January 2018', $to = '30 May 2018')
    {
        $date_start = strtotime($from);
        $date_end   = strtotime($to);
        $rand_date  = rand($date_start, $date_end);
        return new \MongoDB\BSON\UTCDateTime($rand_date * 1000);
    }

    static function getMongoDate($date = NULL, $dimiter = '/', $start = TRUE)
    {
        if ($date) {
            $time = explode($dimiter, $date);
            if (!isset($time[1])) {
                return new \MongoDB\BSON\UTCDateTime(strtotime($date) * 1000);
            }
            if ($start) {
                $time = mktime(0, 0, 0, (int)$time[1], (int)$time[0], (int)$time[2]);
            } else {
                $time = mktime(23, 59, 59, (int)$time[1], (int)$time[0], (int)$time[2]);
            }

            return new \MongoDB\BSON\UTCDateTime($time * 1000);
        }

        return new \MongoDB\BSON\UTCDateTime(strtotime('now') * 1000);
    }

    public static function hideStr($string, $start = 0, $length = 0, $re = '*')
    {
        if (empty($string)) return false;
        $strarr    = array();
        $mb_strlen = mb_strlen($string);
        while ($mb_strlen) {//Cycling strings into arrays
            $strarr[]  = mb_substr($string, 0, 1, 'utf8');
            $string    = mb_substr($string, 1, $mb_strlen, 'utf8');
            $mb_strlen = mb_strlen($string);
        }
        $strlen = count($strarr);
        $begin  = $start >= 0 ? $start : ($strlen - abs($start));
        $end    = $last = $strlen - 1;
        if ($length > 0) {
            $end = $begin + $length - 1;
        } elseif ($length < 0) {
            $end -= abs($length);
        }
        for ($i = $begin; $i <= $end; $i++) {
            $strarr[$i] = $re;
        }
        if ($begin >= $end || $begin >= $last || $end > $last) return str_repeat($re, 5);
        return implode('', $strarr);
    }

    /***
     * @param        $content
     * @param string $string_split
     *
     * @return mixed
     * @note: split các dòng của text area ra thành nhiều dòng
     */
    static public function splitAreaContent($content, $string_split = ',')
    {
        return preg_split('/\r\n|[\r\n]/', $content);
    }

    /**
     * @param $str
     * @return string
     */
    static function getNumberOnlyInString($str)
    {
        preg_match_all('!\d+!', $str, $matches);

        return implode('', $matches[0]);
        //return filter_var($str, FILTER_SANITIZE_NUMBER_INT);// cái này nó trả về cả số âm
    }

    static function buildFolderFromString($filename, $reset = false)
    {
        $md5 = md5($filename . $filename . $reset);
        return $md5[0] . $md5[1] . '/' . $md5[2] . $md5[3] . '/' . $md5[4] . $md5[5];
    }


    static function getUrlContent($url, $options = [],$method='GET')
    {
        $agent = [
            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36",
            "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0",
            'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)',
            "Mozilla/5.0 (Macintosh; Intel Mac OS X x.y; rv:42.0) Gecko/20100101 Firefox/42.0",
            "Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_1 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/10.0 Mobile/14E304 Safari/602.1",
            'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
            'Googlebot-News',
            'Googlebot/2.1 (+http://www.google.com/bot.html)',
            'Mozilla/5.0 (Linux; Android 4.2.1; en-us; Nexus 5 Build/JOP40D) AppleWebKit/535.19 (KHTML, like Gecko; googleweblight) Chrome/38.0.1025.166 Mobile Safari/535.19',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36 (compatible; Google-Read-Aloud; +https://developers.google.com/search/docs/advanced/crawling/overview-google-crawlers)',
            'Googlebot/2.1 (+http://www.google.com/bot.html)',
            'Mozilla/5.0 (Linux; Android 7.0; SM-G930V Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.125 Mobile Safari/537.36 (compatible; Google-Read-Aloud; +https://developers.google.com/search/docs/advanced/crawling/overview-google-crawlers)',
            'Mozilla/5.0 (Linux; Android 8.0; Pixel 2 Build/OPD3.170816.012; DuplexWeb-Google/1.0)',
            'AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.131 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 4.2.1; en-us; Nexus 5 Build/JOP40D) AppleWebKit/535.19 (KHTML, like Gecko; googleweblight) Chrome/38.0.1025.166 Mobile Safari/535.19',
        ];
        shuffle($agent);
        $ch = curl_init();
        curl_setopt_array($url, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        if (isset($options['CURLOPT_REFERER'])) {
            curl_setopt($ch, CURLOPT_REFERER, $options['CURLOPT_REFERER']);
        }

        $data     = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);


        return ($httpcode >= 200 && $httpcode < 300) ? $data : FALSE;
    }


}
