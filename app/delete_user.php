<?php
// If direct access
if(!file_exists('library/sanitize.php')) {
    header('Location: ../index.php');
    exit();
}

require_once 'library/sanitize.php';
require_once 'classes/DB.php';
require_once 'classes/Session.php';

$db = DB::getInstance();

$adminMode = Session::exists('admin') && Session::exists('login') && $db->isSessionValid(Session::get('login'), Session::get('admin'));

if(!$adminMode || !isset($_GET['id']) || !$db->isUserValid(escape($_GET['id']))) {
    header('Location: index.php?showUsers=true');
    exit();
}

$id = escape($_GET['id']);
$sql = 'DELETE FROM user WHERE idUser = :id';
$bindings = ['id' => $id];

// If user is owner of any post, integrity constraint will not permit the suppression
$nbRecordsUsers = $db->delete($sql, $bindings);

header('Location: index.php?showUsers=true');