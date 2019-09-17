<?php
declare(strict_types=1);
namespace iWriter\Common
{
    class Validate {
        public static function regex(string $expression, string $value): bool {
            return (bool)preg_match($expression, $value);
        }
        public static function email(string $email): bool {
            return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
        }
        public static function url(string $url): bool {
            return (bool) filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED);
        }

        public static function ip(string $ip): bool {
            return (bool)filter_var($ip, FILTER_VALIDATE_IP);//不支持ip2long出来的int型
        }
        public static function username(string $username): bool {
            return (bool)preg_match('/[\w-]{4,15}/', $username);
        }
        public static function pwd(string $pwd): bool {
            return (bool)preg_match('/.{6,20}/',$pwd);
        }
        public static function mobilephone(string $mobilephone): bool {
            return (bool)preg_match('/^1\d{10}$/', $mobilephone);
        }
        public static function date(string $date): bool {
            return (strtotime($date) !== FALSE) && preg_match('&^\d{4}[/-]?\d{1,2}[/-]?\d{1,2}( ?\d{1,2}:?\d{1,2}:\d{1,2})?$&', $date);
        }
        public static function timestamp(string $timestamp): bool {
            return is_numeric($timestamp) && strtotime(date('Y-m-d H:i:s', (int)$timestamp)) == $timestamp;
        }
        public static function sqlParam(string $param): bool{
            return stristr($param, 'insert') === false 
                && 
                stristr($param, 'update') === false
                && 
                stristr($param, 'delete') === false
                && 
                stristr($param, 'select') === false
                && 
                stristr($param, 'grant') === false
                && 
                stristr($param, 'revoke') === false;
        }
    }
}
