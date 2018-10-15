<?php
class Session {
    private function __construct() {}
    /**
     * @param string $name
     * @param string $value
     * @return string
     */
    public static function put(string $name, string $value) : string {
        return $_SESSION[$name] = $value;
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function exists(string $name) : bool {
        return isset($_SESSION[$name]);
    }

    /**
     * @param string $name
     * @return string
     */
    public static function get(string $name) : string {
        if(self::exists($name)) {
            return $_SESSION[$name];
        }
        return '';
    }

    /**
     * @param string $name
     */
    public static function delete(string $name) {
        if(self::exists($name)) {
            unset($_SESSION[$name]);
        }
    }
}