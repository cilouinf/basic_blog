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

if(!$adminMode || !isset($_GET['id']) || !isset($_GET['status']) || !$db->isUserValid(escape($_GET['id']))) {
    header('Location: index.php?showUsers=true');
    exit();
}

$state = ($_GET['status'] == '0') ? '1' : '0';
$id = escape($_GET['id']);

$sql = 'UPDATE user SET isActive = :state WHERE idUser = :id';
$bindings = ['state' => $state, 'id' => $id];

$nbRecords = $db->update($sql, $bindings);

$location = 'Location: index.php?showUsers=true';

if(isset($idPage)) {
    $location .= '&page=' . $idPage;
}
if(isset($urlSortParams)) {
    $location .= $urlSortParams;
}

header($location);

