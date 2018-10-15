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

$error = false;

$db = DB::getInstance();
$adminMode = Session::exists('admin') && Session::exists('login') && $db->isSessionValid(Session::get('login'), Session::get('admin'));

if(!$adminMode || empty($_POST) && (!isset($_GET['id']) || !$db->isUserValid(escape($_GET['id'])))) {
    header('Location: index.php');
    exit();

} else if (!empty($_POST)) {

    // CSRF protection
    if(!isset($_POST[Token::TOKEN_NAME]) || !Token::check($_POST[Token::TOKEN_NAME])) {
        header('Location: index.php');
        exit();
    }

    $lastName = escape($_POST['lastname']);
    $firstName = escape($_POST['firstname']);
    $email = escape($_POST['email']);
    $birthDate = escape($_POST['birthdate']);
    $nickName = escape($_POST['nickname']);
    $isAdmin = escape($_POST['isadmin']);

    $sql = 'UPDATE user SET lastName = :lastname,
                            firstName = :firstname,
                            email = :email,
                            birthDate = :birthdate,
                            nickName = :nickname,
                            isAdmin = :isadmin
            WHERE idUser = :id';

    $bindings = [   'lastname' => $lastName,
                    'firstname' => $firstName,
                    'email' => $email,
                    'birthdate' => $birthDate,
                    'nickname' => $nickName,
                    'isadmin' => $isAdmin,
                    'id' => escape($_GET['id'])
                ];

    $nbRecords = $db->update($sql, $bindings);

    if(!$nbRecords) {
        $error = true;
        $msg = 'Les données de l\'utilisateur n\'ont pas été mises à jour'; // happens also when no change has been made
        Session::put('error', $msg);
    } else {
        $msg = 'Les données de l\'utilisateur ont été mises à jour avec succès';
        Session::put('success', $msg);

        $location = 'Location: index.php?showUsers=true';

        if(isset($idPage)) {
            $location .= '&page=' . $idPage;
        }
        if(isset($urlSortParams)) {
            $location .= $urlSortParams;
        }

        header($location);
        exit();
    }
}

//
// retrieve user object to be modified and display current values in form fields
//
$id = escape($_GET['id']);
$sql = 'SELECT * FROM user WHERE idUser = :id';
$bindings = ['id' => $id];
$user = $db->query($sql, $bindings)[0];
