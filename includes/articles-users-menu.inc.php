<?php
// If direct access
if(!file_exists('classes/DB.php')) {
    header('Location: ../index.php');
    exit();
}

// CHECK IF ADMIN OR MEMBER IS CURRENTLY LOGGED IN
$db = DB::getInstance();
$adminMode = Session::exists('admin') && Session::exists('login') && $db->isSessionValid(Session::get('login'), Session::get('admin'));
$memberMode = Session::exists('member') && Session::exists('login') && $db->isSessionValid(Session::get('login'), Session::get('member'));
?>

<!-- If Admin Logged In -->
<?php if($adminMode) : ?>
    <p>
        <a class="btn btn-primary mt-3" href="?">Gérer les articles</a>&nbsp;&nbsp;
        <a class="btn btn-primary mt-3" href="?showUsers=true">Gérer les membres</a>&nbsp;&nbsp;
        <a class="btn btn-info mt-3" href="change_password.php">Changer le mot de passe</a>&nbsp;&nbsp;
        <a class="btn btn-success mt-3" href="admin.php?logout=true">Se déconnecter (<?=Session::get('login')?>)</a>
    </p>
    <?php if(!$usersTable) : ?>
        <h1 class="text-center mt-3 pb-3">Liste des articles du blog (Mode administration)</h1>
        <p class="alert alert-info text-center">Total de <?=$totalNbPosts?> articles dans la DB</p>
        <p><a class="btn btn-secondary" href="?action=new_article">Créer un nouvel article</a></p>
    <?php else :?>
        <h1 class="text-center mt-3 pb-3">Liste des membres du blog (Mode administration)</h1>
        <p class="alert alert-info text-center">Total de <?=$totalNbMembers?> membres dans la DB</p>
        <p><a class="btn btn-secondary" href="register.php">Créer un nouveau compte membre</a></p>
    <?php endif; ?>
<!-- End If Admin Logged In -->

<!-- If Guest or Member Logged In -->
<?php else : ?>
    <?php if($memberMode) : ?>
        <p>
            <a class="btn btn-secondary mt-3" href="?action=new_article">Créer un nouvel article</a>&nbsp;&nbsp;
            <a class="btn btn-info mt-3" href="change_password.php">Changer le mot de passe</a>&nbsp;&nbsp;
            <a class="btn btn-success mt-3" href="admin.php?logout=true">Se déconnecter (<?=Session::get('login')?>)</a>
        </p>
    <?php endif; ?>
    <h1 class="text-center mt-3 pb-3">Liste des articles du blog </h1>
    <p class="alert alert-info text-center">Total de <?=$totalNbPosts?> articles actuellement publiés</p>
<?php endif; ?>
<!-- End If Guest or Member Logged In -->