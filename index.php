<?php
session_start();
// Standard header
require_once 'includes/header.inc.php';

// Display messages from other pages 
require_once 'classes/Session.php';
if(Session::exists('success')) {
    echo '<p class="alert alert-success">' . Session::get('success') . '</p>';
    Session::delete('success');
} else if(Session::exists('error')) {
    echo '<p class="alert alert-danger">' . Session::get('error') . '</p>';
    Session::delete('error');
}

// Listing of the articles (table)
require_once 'includes/articles-users.inc.php';

// Standard footer
require_once 'includes/footer.inc.php';