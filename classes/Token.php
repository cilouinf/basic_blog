<?php
////https://stackoverflow.com/questions/6287903/how-to-properly-add-csrf-token-using-php/31683058#31683058
class Token {
    public const TOKEN_NAME = 'token';

    public static function generate() : string {
        return Session::put(self::TOKEN_NAME, bin2hex(random_bytes(32)));
    }
    
    public static function check(string $tokenValue) : bool {
        if(Session::exists(self::TOKEN_NAME) && hash_equals($tokenValue, Session::get(self::TOKEN_NAME))) {
            Session::delete(self::TOKEN_NAME);
            return true;
        }
        return false;
    }
}