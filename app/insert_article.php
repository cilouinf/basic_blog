<?php
// If direct access
if(!file_exists('library/sanitize.php')) {
    header('Location: ../index.php');
    exit();
}

require_once 'library/sanitize.php';
require_once 'classes/DB.php';
require_once 'classes/Session.php';
require_once 'classes/Token.php';

$db = DB::getInstance();

// Check if user is authenticated and redirect to homepage if not
$adminMode = Session::exists('admin') && Session::exists('login') && $db->isSessionValid(Session::get('login'), Session::get('admin'));
$memberMode = Session::exists('member') && Session::exists('login') && $db->isSessionValid(Session::get('login'), Session::get('member'));

if(!($memberMode || $adminMode)) {
    header('Location: index.php');
    exit();
}

//
// INIT VARIABLES
//
$postImageName = 'Aucune image sélectionnée';
$error = false;
$msg = '';

//
// GET ALL EXISTING CATEGORIES FOR COMBOX BOX
//
$sql = 'SELECT idCategory, categoryName FROM category ORDER BY categoryName ASC';
$allCategories = $db->query($sql, []);

//
// RETRIEVE DATA FROM FORM AND INSERT NEW ARTICLE
//
if(!empty($_POST)) {

    // CSRF protection
    if(!isset($_POST[Token::TOKEN_NAME]) || !Token::check($_POST[Token::TOKEN_NAME])) {
        header('Location: index.php');
        exit();
    }

    $formTitle = $_POST['title'];
    $formContent = trim(escape($_POST['content']));
    $formCategories = $_POST['categories'];
    $formIsPublished = ($adminMode) ? $_POST['ispublished'] : 0;
    $formImage = '';
    $previousImage = '';

    $sql = 'SELECT idUser FROM user WHERE login = :login';
    $bindings = ['login' => Session::get('login')];
    $idUser = $db->query($sql, $bindings, PDO::FETCH_COLUMN, 0)[0];
    $genImageId = $idUser;

    require_once 'app/image_upload.php';

    if (!$error) {

        $formCreatedTime = new DateTime('now', new DateTimeZone('Europe/Brussels'));
        $formCreatedTime = $formCreatedTime->format('Y-m-d H:i:s');
        $sql = 'INSERT INTO post (title, image, content, createdTime, idUser, isPublished) VALUES
                (:title, :image, :content, :createdTime, :idUser, :isPublished)';
        $bindings = [
            'title' => $formTitle,
            'image' => $formImage,
            'content' => $formContent,
            'createdTime' => $formCreatedTime,
            'idUser' => $idUser,
            'isPublished' => $formIsPublished
        ];
        $insertedId = $db->insert($sql, $bindings);
        if ($insertedId) {
            foreach ($formCategories as $category) {
                $sql = 'SELECT idCategory FROM category WHERE categoryName = :category';
                $bindings = ['category' => $category];
                $categoryId = $db->query($sql, $bindings, PDO::FETCH_COLUMN, 0)[0];
                $sql = 'INSERT INTO postcategory (idPost, idCategory) VALUES (:idPost, :idCategory)';
                $bindings = ['idPost' => $insertedId, 'idCategory' => $categoryId];
                $db->insert($sql, $bindings);
            }
        } else {
            $error = true;
            $msg = 'Error inserting article';
        }
    } else {
        Session::put('error', $msg);
    }
}

//
// FEEDBACK IF SUCCESS
//
if(!empty($_POST) && !$error) {
    $msg = 'Article créé avec succès';
    if(!$adminMode) {
        $msg .= ' et en attente de publication après validation par un administrateur.';
    }
    Session::put('success', $msg);
    header('Location: index.php');
}