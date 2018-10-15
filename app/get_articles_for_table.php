<?php
// If direct access
if(!file_exists('library/sanitize.php')) {
    header('Location: ../index.php');
    exit();
}

require_once 'classes/DB.php';
require_once 'classes/Session.php';
require_once 'classes/Token.php';
require 'library/sanitize.php';

//
// GET A DB CONNECTION
//
$db = DB::getInstance();

//
// CHECK IF ADMIN OR MEMBER IS CURRENTLY LOGGED IN
//
$adminMode = Session::exists('admin') && Session::exists('login') && $db->isSessionValid(Session::get('login'), Session::get('admin'));
$memberMode = Session::exists('member') && Session::exists('login') && $db->isSessionValid(Session::get('login'), Session::get('member'));

//
// GET THE TOTAL NUMBER OF RECORDS (POSTS)
//
if($adminMode) {
    $totalNbPosts = $db->query('SELECT COUNT(*) AS total FROM post')[0]->total;
} else {
    $totalNbPosts = $db->query('SELECT COUNT(*) AS total FROM post WHERE isPublished = "1"')[0]->total;
}

//
// SELECT ALL POSTS
//
$sql = 'SELECT post.idPost, post.idUser, title, createdTime, firstName, lastName, GROUP_CONCAT(categoryName) AS categories, isPublished
        FROM post
        INNER JOIN user ON post.idUser = user.idUser
        INNER JOIN postcategory ON post.idPost = postcategory.idPost
        INNER JOIN category ON postcategory.idCategory = category.idCategory'
;

//
// SHOW ONLY PUBLISHED POSTS TO NORMAL MEMBERS AND GUESTS
//
if(!$adminMode) {
    $sql .= ' WHERE isPublished = "1"';
}

//
// SET VARIABLES TO THEIR DEFAULT VALUE
//
$bindings = [];
$articleFiltered = false;

//
// RESET ALL FILTERS (COOKIES)
//
if(isset($_GET['reset']) && $_GET['reset'] == 'true') {
    setcookie('category', '', time() - 3600, '/', $_SERVER['SERVER_NAME'],0,1);
    unset($_COOKIE['category']);
    setcookie('search', '', time() - 3600, '/', $_SERVER['SERVER_NAME'],0,1);
    unset($_COOKIE['search']);
    setcookie('author', '', time() - 3600, '/', $_SERVER['SERVER_NAME'],0,1);
    unset($_COOKIE['author']);
}

//
// FILTER POSTS BY CATEGORY NAME
//
if(isset($_COOKIE['category']) && !isset($_GET['category'])) {
    $_GET['category'] = $_COOKIE['category'];
}

if(isset($_GET['category']) && in_array($_GET['category'], $db->getCategoryNames())) {
    if(!$adminMode) {
        $sql .= ' AND ';
    } else {
        $sql .= ' WHERE ';
    }

    $sql .= 'post.idPost IN 
                (SELECT idPost FROM postcategory WHERE idCategory = 
                    (SELECT idCategory FROM category WHERE categoryName = :catName))';
    $bindings = ['catName' => $_GET['category']];
    $articleFiltered = true;
    setcookie('category', $_GET['category'], time() + 3600, '/', $_SERVER['SERVER_NAME'],0,1);
}

//
// FILTER POSTS BY SEARCH PARAMETER
//
if(isset($_COOKIE['search']) && !isset($_GET['search'])) {
    $_GET['search'] = $_COOKIE['search'];
}

if(isset($_GET['search'])) {
    $search = escape($_GET['search']);
    $sqlSearch = '(title LIKE :searchTitle OR 
                   content LIKE :searchContent OR
                   categoryName LIKE :searchCategoryName)';

    $sql .= ($articleFiltered || !$adminMode) ? ' AND ' : ' WHERE ';
    $articleFiltered = true;
    $sql .= $sqlSearch;
    $bindings += ['searchTitle' => '%' . $search . '%'];
    $bindings += ['searchContent' => '%' . $search . '%'];
    $bindings += ['searchCategoryName' => '%' . $search . '%'];
    setcookie('search', escape($_GET['search']), time() + 3600, '/', $_SERVER['SERVER_NAME'],0,1);
}

//
// FILTER POSTS BY AUTHOR
//
if(isset($_COOKIE['author']) && !isset($_GET['author'])) {
    $_GET['author'] = $_COOKIE['author'];
}

if(isset($_GET['author']) && $db->isAuthorIdValid($_GET['author'])) {
    $author = $_GET['author'];
    $sql .= ($articleFiltered || !$adminMode) ? ' AND ' : ' WHERE ';
    $sql .= 'post.idUser = :author';
    $articleFiltered = true;
    $bindings += ['author' => $author];
    setcookie('author', $_GET['author'], time() + 3600, '/', $_SERVER['SERVER_NAME'],0,1);
}

//
// GROUP_CONCAT requires GROUP BY (aggregation function)
//
$sql .= ' GROUP BY post.idPost';

//
// SORT POSTS FROM DB
//
$sortTitle = null;
$sortCreatedTime = null;
$sortAuthor = null;
$sortCategories = null;
$sortIsPublished = null;


if(isset($_GET['sortTitle']) && in_array($_GET['sortTitle'], ['ASC', 'DESC'])) {
    $sql .= ' ORDER BY title ' . $_GET['sortTitle'];
    $sortTitle = ($_GET['sortTitle'] == 'ASC') ? 'DESC' : 'ASC';
    $urlSortParams = '&sortTitle=' . $_GET['sortTitle']; // so the order is kept between pages (pagination)

} else if(isset($_GET['sortCreatedTime']) && in_array($_GET['sortCreatedTime'], ['ASC', 'DESC'])) {
    $sql .= ' ORDER BY createdTime ' . $_GET['sortCreatedTime'];
    $sortCreatedTime = ($_GET['sortCreatedTime'] == 'ASC') ? 'DESC' : 'ASC';
    $urlSortParams = '&sortCreatedTime=' . $_GET['sortCreatedTime'];

} else if(isset($_GET['sortAuthor']) && in_array($_GET['sortAuthor'], ['ASC', 'DESC'])) {
    $sql .= ' ORDER BY lastName ' . $_GET['sortAuthor'] . ', firstName ' . $_GET['sortAuthor'];
    $sortAuthor = ($_GET['sortAuthor'] == 'ASC') ? 'DESC' : 'ASC';
    $urlSortParams = '&sortAuthor=' . $_GET['sortAuthor'];

} else if(isset($_GET['sortCategory']) && in_array($_GET['sortCategory'], ['ASC', 'DESC'])) {
    $sql .= ' ORDER BY categories ' . $_GET['sortCategory'];
    $sortCategories = ($_GET['sortCategory'] == 'ASC') ? 'DESC' : 'ASC';
    $urlSortParams = '&sortCategory=' . $_GET['sortCategory'];

} else if(isset($_GET['sortIsPublished']) && in_array($_GET['sortIsPublished'], ['ASC', 'DESC'])) {
    $sql .= ' ORDER BY isPublished ' . $_GET['sortIsPublished'];
    $sortIsPublished = ($_GET['sortIsPublished'] == 'ASC') ? 'DESC' : 'ASC';
    $urlSortParams = '&sortIsPublished=' . $_GET['sortIsPublished'];

} else {
    $sql .= ' ORDER BY createdTime DESC';
}

//
// GET THE NUMBER OF PAGES IN FUNCTION OF THE NUMBER OF RESULTS FOR PAGINATION
//
$nbArticlesPerPage = 5;
$posts = $db->query($sql, $bindings); // query without limit to get the number of posts (filters)
$nbPages = ceil(count($posts) / $nbArticlesPerPage);
$totalNbPostsPagination = count($posts);
$idPage = 1;
if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1 && $_GET['page'] <= $nbPages) {
    $idPage = $_GET['page'];
}

//
// RETRIEVE POSTS FROM DB WITH LIMIT
//
$sql .= ' LIMIT ' . ($idPage - 1)*$nbArticlesPerPage . ',' . $nbArticlesPerPage;
$posts = $db->query($sql, $bindings);
