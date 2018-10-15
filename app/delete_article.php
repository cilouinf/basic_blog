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

if(!$adminMode || !isset($_GET['id']) || !$db->isArticleValid(escape($_GET['id']))) {
    header('Location: index.php');
    exit();
}

$id = escape($_GET['id']);

$sql = 'SELECT image FROM post WHERE idPost = :id';
$bindings = ['id' => $id];
$previousImage = $db->query($sql, $bindings, PDO::FETCH_COLUMN, 0)[0];

$sqlPostCategory = 'DELETE FROM postcategory WHERE idPost = :id';
$sqlPost = 'DELETE FROM post WHERE idPost = :id';
$bindings = ['id' => $id];

$nbRecordsCat = $db->delete($sqlPostCategory, $bindings);
$nbRecordsPost = $db->delete($sqlPost, $bindings);

if(!($nbRecordsPost && $nbRecordsCat && (($previousImage && unlink($previousImage)) || !$previousImage))) {
    $error = true;
    $msg = 'Une erreur s\'est produite lors de la suppression de l\'article';
    Session::put('error', 'Article non supprimé : ' . $msg);
}

header('Location: index.php');
