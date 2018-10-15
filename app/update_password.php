<?php
// If direct access
if(!file_exists('library/sanitize.php')) {
    header('Location: ../index.php');
    exit();
}

require_once 'library/sanitize.php';
require_once 'classes/DB.php';

$db = DB::getInstance();

if((!Session::exists('admin') && !Session::exists('member')) ||
    !Session::exists('login') ||
    (Session::exists('admin') && !$db->isSessionValid(Session::get('login'), Session::get('admin'))) ||
    (Session::exists('member') && !$db->isSessionValid(Session::get('login'), Session::get('member')))) {

    header('Location: index.php');
    exit();
}

if(!empty($_POST)) {

    // CSRF Protection
    if(!isset($_POST[Token::TOKEN_NAME]) || !Token::check($_POST[Token::TOKEN_NAME])) {
        header('Location: index.php');
        exit();
    }

    $error = false;
    $currentPass = escape($_POST['currentpass']);
    $newPass = escape($_POST['newpass']);
    $newPassCheck = escape($_POST['newpasscheck']);

    if ($newPass != $newPassCheck) {
        $error = true;
        $msg = 'Les nouveaux mot de passe ne correspondent pas';
    } else if($currentPass == $newPass) {
        $error = true;
        $msg = 'Le nouveau mot de passe est identique à l\'actuel';
    }

    $login = Session::get('login');
    $sql = 'SELECT hashedPass, salt FROM user WHERE login = :login';
    $bindings = ['login' => $login];
    $userInfo = $db->query($sql, $bindings)[0];

    if ($userInfo) {
        if (!$db->isLoginUserValid($login, $currentPass) && !$db->isLoginAdminValid($login, $currentPass)) {
            $error = true;
            $msg = 'Le mot de passe actuel est invalide';
        }
    } else {
        $error = true;
        $msg = 'Utilisateur non trouvé';

    }

    if (!$error) {
        $sql = 'UPDATE user SET hashedPass = :hashedpass WHERE login = :login';
        $bindings = ['hashedpass' => Hash::make($newPass, $userInfo->salt), 'login' => $login];
        if(!$db->update($sql, $bindings)) {
            $error = true;
            $msg = 'Une erreur s\'est produite';
        }
    }

    if($error) {
        Session::put('error', $msg);
    } else {
        $msg = 'Mot de passe modifié avec succès';
        Session::put('success', $msg);
    }
}
