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
// CHECK IF ADMIN MEMBER IS CURRENTLY LOGGED IN
//
$adminMode = Session::exists('admin') && Session::exists('login') && $db->isSessionValid(Session::get('login'), Session::get('admin'));

if(!$adminMode) {
    header('Location: index.php');
    exit();
}

//
// GET TOTAL NUMBER OF MEMBERS
//
$totalNbMembers = $db->query('SELECT COUNT(*) AS total FROM user')[0]->total;

//
// SELECT ALL USERS
//
$sql = 'SELECT  user.idUser, 
                firstName, 
                lastName, 
                email, 
                birthDate, 
                login, 
                nickName, 
                hashedPass, 
                salt, 
                registeredDate, 
                lastLogin, 
                isAdmin, 
                isActive, 
                COUNT(idPost) AS nbArticles
      FROM user 
        LEFT JOIN post ON user.idUser = post.idUser
      GROUP BY user.idUser';

//
// SORT POSTS FROM DB
//
$sortUserLastname = null;
$sortUserFirstname = null;
$sortUserEmail = null;
$sortUserBirthdate = null;
$sortUserLogin = null;
$sortUserNickname = null;
$sortUserRegistered = null;
$sortUserLastLogin = null;
$sortUserIsAdmin = null;
$sortUserIsActive = null;
$sortUserNbArticles = null;

if(isset($_GET['sortUserLastname']) && in_array($_GET['sortUserLastname'], ['ASC', 'DESC'])) {
    $sql .= ' ORDER BY lastName ' . $_GET['sortUserLastname'];
    $sortUserLastname = ($_GET['sortUserLastname'] == 'ASC') ? 'DESC' : 'ASC';
    $urlSortParams = '&sortUserLastname=' . $_GET['sortUserLastname']; // keep sorting order between pages (pagination)

} else if(isset($_GET['sortUserFirstname']) && in_array($_GET['sortUserFirstname'], ['ASC', 'DESC'])) {
    $sql .= ' ORDER BY firstName ' . $_GET['sortUserFirstname'];
    $sortUserFirstname = ($_GET['sortUserFirstname'] == 'ASC') ? 'DESC' : 'ASC';
    $urlSortParams = '&sortUserFirstname=' . $_GET['sortUserFirstname'];

} else if(isset($_GET['sortUserEmail']) && in_array($_GET['sortUserEmail'], ['ASC', 'DESC'])) {
    $sql .= ' ORDER BY email ' . $_GET['sortUserEmail'];
    $sortUserEmail = ($_GET['sortUserEmail'] == 'ASC') ? 'DESC' : 'ASC';
    $urlSortParams = '&sortUserEmail=' . $_GET['sortUserEmail'];

} else if(isset($_GET['sortUserBirthdate']) && in_array($_GET['sortUserBirthdate'], ['ASC', 'DESC'])) {
    $sql .= ' ORDER BY birthDate ' . $_GET['sortUserBirthdate'];
    $sortUserBirthdate = ($_GET['sortUserBirthdate'] == 'ASC') ? 'DESC' : 'ASC';
    $urlSortParams = '&sortUserBirthdate=' . $_GET['sortUserBirthdate'];

} else if(isset($_GET['sortUserLogin']) && in_array($_GET['sortUserLogin'], ['ASC', 'DESC'])) {
    $sql .= ' ORDER BY login ' . $_GET['sortUserLogin'];
    $sortUserLogin = ($_GET['sortUserLogin'] == 'ASC') ? 'DESC' : 'ASC';
    $urlSortParams = '&sortUserLogin=' . $_GET['sortUserLogin'];

} else if(isset($_GET['sortUserNickname']) && in_array($_GET['sortUserNickname'], ['ASC', 'DESC'])) {
    $sql .= ' ORDER BY nickName ' . $_GET['sortUserNickname'];
    $sortUserNickname = ($_GET['sortUserNickname'] == 'ASC') ? 'DESC' : 'ASC';
    $urlSortParams = '&sortUserNickname=' . $_GET['sortUserNickname'];

} else if(isset($_GET['sortUserRegistered']) && in_array($_GET['sortUserRegistered'], ['ASC', 'DESC'])) {
    $sql .= ' ORDER BY registeredDate ' . $_GET['sortUserRegistered'];
    $sortUserRegistered = ($_GET['sortUserRegistered'] == 'ASC') ? 'DESC' : 'ASC';
    $urlSortParams = '&sortUserRegistered=' . $_GET['sortUserRegistered'];

} else if(isset($_GET['sortUserLastLogin']) && in_array($_GET['sortUserLastLogin'], ['ASC', 'DESC'])) {
    $sql .= ' ORDER BY lastLogin ' . $_GET['sortUserLastLogin'];
    $sortUserLastLogin = ($_GET['sortUserLastLogin'] == 'ASC') ? 'DESC' : 'ASC';
    $urlSortParams = '&sortUserLastLogin=' . $_GET['sortUserLastLogin'];

} else if(isset($_GET['sortUserIsAdmin']) && in_array($_GET['sortUserIsAdmin'], ['ASC', 'DESC'])) {
    $sql .= ' ORDER BY isAdmin ' . $_GET['sortUserIsAdmin'];
    $sortUserIsAdmin = ($_GET['sortUserIsAdmin'] == 'ASC') ? 'DESC' : 'ASC';
    $urlSortParams = '&sortUserIsAdmin=' . $_GET['sortUserIsAdmin'];

} else if(isset($_GET['sortUserIsActive']) && in_array($_GET['sortUserIsActive'], ['ASC', 'DESC'])) {
    $sql .= ' ORDER BY isActive ' . $_GET['sortUserIsActive'];
    $sortUserIsActive = ($_GET['sortUserIsActive'] == 'ASC') ? 'DESC' : 'ASC';
    $urlSortParams = '&sortUserIsActive=' . $_GET['sortUserIsActive'];

} else if(isset($_GET['sortUserNbArticles']) && in_array($_GET['sortUserNbArticles'], ['ASC', 'DESC'])) {
    $sql .= ' ORDER BY nbArticles ' . $_GET['sortUserNbArticles'];
    $sortUserNbArticles = ($_GET['sortUserNbArticles'] == 'ASC') ? 'DESC' : 'ASC';
    $urlSortParams = '&sortUserNbArticles=' . $_GET['sortUserNbArticles'];
} else {
    $sql .= ' ORDER BY registeredDate DESC';
}

//
// GET THE NUMBER OF PAGES IN FUNCTION OF THE NUMBER OF RESULTS FOR PAGINATION
//
$nbArticlesPerPage = 5;
$nbPages = ceil($totalNbMembers / $nbArticlesPerPage);
$idPage = 1;
if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1 && $_GET['page'] <= $nbPages) {
    $idPage = $_GET['page'];
}

//
// RETRIEVE POSTS FROM DB WITH LIMIT
//
$sql .= ' LIMIT ' . ($idPage - 1)*$nbArticlesPerPage . ',' . $nbArticlesPerPage;
$users = $db->query($sql);
