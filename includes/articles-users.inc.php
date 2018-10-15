<?php
// If direct access
if(!file_exists('app/get_articles_for_table.php')) {
    header('Location: ../index.php');
    exit();
}

if(!isset($_GET['showUsers'])) {
    $usersTable = false;
    require_once 'app/get_articles_for_table.php';
} else {
    $usersTable = true;
    require_once 'app/get_users_for_table.php';
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <?php
            require_once 'includes/articles-users-menu.inc.php';

            if(!$usersTable) {
                require_once 'includes/articles-listing.inc.php';
            } else {
                require_once 'includes/users-listing.inc.php';
            }
            ?>
        </div>
    </div>
</div>
