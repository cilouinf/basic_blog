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
require_once 'classes/Hash.php';


$db = DB::getInstance();

$adminMode = Session::exists('admin') && Session::exists('login') && $db->isSessionValid(Session::get('login'), Session::get('admin'));

if(!empty($_POST)) {

    // CSRF protection
    if(!isset($_POST[Token::TOKEN_NAME]) || !Token::check($_POST[Token::TOKEN_NAME])) {
        header('Location: index.php');
        exit();
    }

    $error = false;

    $lastName = escape($_POST['lastname']);
    $firstName = escape($_POST['firstname']);
    $nickName = escape($_POST['nickname']);
    $email = escape($_POST['email']);
    $birthDate = escape($_POST['birthdate']);
    $login = escape($_POST['login']);
    if(escape($_POST['password']) == escape($_POST['passwordcheck'])) {
        $password = escape($_POST['password']);
    } else {
        $error = true;
        $msg = 'Les mots de passe ne correspondent pas';
    }

    $isAdmin = $adminMode ? $_POST['isadmin'] : '0';
    $isActive = $adminMode ? $_POST['isactive'] : '0';

    //
    // Check if login already exists in DB
    //
    $sql = 'SELECT idUser FROM user WHERE login = :login';
    $bindings = ['login' => $login];
    $userId = $db->query($sql, $bindings, PDO::FETCH_COLUMN, 0);
    if(count($userId)) {
        $error = true;
        $msg = 'Le login choisi est déjà utilisé';
    }

    //
    // Check if email already exists in DB
    //
    $sql = 'SELECT idUser FROM user WHERE email = :email';
    $bindings = ['email' => $email];
    $userId = $db->query($sql, $bindings, PDO::FETCH_COLUMN, 0);
    if(count($userId)) {
        $error = true;
        $msg = 'L\'email choisi est déjà utilisé';
    }
    
    //
    // Create new user
    //
    if(!$error) {
        $sql = 'INSERT INTO user (firstName, lastName, email, birthDate, login, nickName, hashedPass, salt, registeredDate, isAdmin, isActive) VALUES
                (:firstname, :lastname, :email, :birthdate, :login, :nickname, :hashedpass, :salt, :registereddate, :isadmin, :isactive)';

        $registeredDate = new DateTime('now', new DateTimeZone('Europe/Brussels'));
        $registeredDate = $registeredDate->format('Y-m-d H:i:s');
        $salt = Hash::salt();
        $hashedPass = Hash::make($password, $salt);
        
        $bindings = [
            'firstname' => $firstName,
            'lastname' => $lastName,
            'email' => $email,
            'birthdate' => $birthDate,
            'login' => $login,
            'nickname' => $nickName,
            'hashedpass' => $hashedPass,
            'salt' => $salt,
            'registereddate' => $registeredDate,
            'isadmin' => $isAdmin,
            'isactive' => $isActive
        ];

        if($db->insert($sql, $bindings)) {
            $msg = 'Le compte a été créé avec succès';
            $location = 'Location: index.php?showUsers=true';
            if(!$adminMode) {
                $msg .= ' et est en attente de validation par un administrateur';
                $location = 'Location: index.php';
            }
            Session::put('success', $msg);
            header($location);
        }
    } else {
        Session::put('error', $msg);
    }
}
