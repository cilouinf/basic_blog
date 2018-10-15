<?php

class DBConfig {
    public const    DB_TYPE = 'mysql',
                    HOSTNAME = 'localhost',
                    PORT = 3306,
                    USERNAME = '',
                    PASSWORD = '',
                    DB_NAME = '',
                    CHARSET = 'utf8',
                    OPTIONS = [
                            PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_EMULATE_PREPARES      => false,
                            PDO::ATTR_AUTOCOMMIT            => false,
                            PDO::ATTR_DEFAULT_FETCH_MODE    => PDO::FETCH_OBJ
                        ];

    private function __construct() {} // do not allow instantiation
}