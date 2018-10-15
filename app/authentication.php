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

// Set init variables
$loginFailed = true;
$error = false;

// Retrieve submitted data from form
if(!empty($_POST)) {
    $db = DB::getInstance();

    if(isset($_POST['login']) && isset($_POST['password']) && isset($_POST['token'])) {
        $login = escape($_POST['login']);
        $pass = escape($_POST['password']);
        $token = escape($_POST['token']);

        // Check that submitted form token matches generated token (CSRF)
        if(!Token::check($token)) {
            $error = true;
            $msg = 'Erreur CSRF';
            Session::put('error', $msg);
        } else {
            // Generate a sessionID
            Token::generate();

            // Check if user is admin or member and valid
            if ($db->isLoginAdminValid($login, $pass)) {
                Session::put('admin', Session::get(Token::TOKEN_NAME));

            } else if ($db->isLoginUserValid($login, $pass)) {
                Session::put('member', Session::get(Token::TOKEN_NAME));
            } else {
                $error = true;
                $msg = 'Utilisateur ou mot de passe erroné';
            }

            // If user exists in DB and pass matches, update table with current time (last login) and sessionId
            if (!$error) {
                $loginFailed = false;
                Session::put('login', $login);
                $time = new DateTime('now', new DateTimeZone('Europe/Brussels'));
                $time = $time->format('Y-m-d H:i:s');
                $sql = 'UPDATE user SET lastLogin = :now, sessionId = :token WHERE login = :login';
                $bindings = ['now' => $time, 'token' => Session::get(Token::TOKEN_NAME), 'login' => $login];
                $db->update($sql, $bindings);
                header('Location: index.php');
            }
        }
    } else {
        $error = true;
        $msg = 'Données du formulaire incomplètes';
    }

    if($error) {
        Session::put('error', $msg);
    }
}
// Flash session message if authentication failed
if(Session::exists('error')) {
    echo '<p class="alert alert-danger">' . Session::get('error') . '</p>';
    Session::delete('error');
}