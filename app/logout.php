<?php
// If direct access
if(!file_exists('library/sanitize.php')) {
    header('Location: ../index.php');
    exit();
}

require_once 'library/sanitize.php';
require_once 'classes/Session.php';
require_once 'classes/DB.php';

$db = DB::getInstance();

if(Session::exists('admin')) {
    Session::delete('admin');
} else if(Session::exists('member')) {
    Session::delete('member');
}
if(Session::exists('login')) {
    $db->deleteSession(Session::get('login'));
    Session::delete('login');
}
