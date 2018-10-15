<?php
class Hash {
    private function __construct() {}

    /**
     * @param string $str
     * @param string $salt
     * @return string
     */
    public static function make(string $str, string $salt = '') {
        return hash('sha256', $str . $salt);
    }

    /**
     * @param int $length
     * @return null|string
     */
    public static function salt(int $length = 32) {
        $bytes = null;
        try {
            $bytes = random_bytes($length / 2); // because 1hex = 4bits
        } catch(Exception $e) {
            return null;
        }

        return bin2hex($bytes);
    }
}