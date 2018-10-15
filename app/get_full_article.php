<?php
// If direct access
if(!file_exists('library/sanitize.php')) {
    header('Location: ../index.php');
    exit();
}

require_once 'classes/DB.php';
require 'library/sanitize.php';

// GET A DB CONNECTION
$db = DB::getInstance();

// QUERY DB
if(!isset($_GET['article']) || !$db->isArticleValid(escape($_GET['article']))) {
    header('Location: index.php');
    exit();
}

$sql = 'SELECT post.idPost, post.idUser, title, image, content, createdTime, firstName, lastName, GROUP_CONCAT(categoryName) AS categories
        FROM post
        INNER JOIN user ON post.idUser = user.idUser
        INNER JOIN postcategory ON post.idPost = postcategory.idPost
        INNER JOIN category ON postcategory.idCategory = category.idCategory
        WHERE post.idPost = :idPost';
$bindings = ['idPost' => escape($_GET['article'])];
$article = $db->query($sql, $bindings)[0];
