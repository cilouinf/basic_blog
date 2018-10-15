<?php
// If direct access
if(!file_exists('classes/DB.php')) {
    header('Location: ../index.php');
    exit();
}
/**
 * @param $string
 * @return string
 */
function escape($string) : string {
    return htmlentities($string, ENT_QUOTES, 'UTF-8');
}