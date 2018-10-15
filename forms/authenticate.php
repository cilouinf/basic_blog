<?php
// If direct access
if(!file_exists('app/authentication.php')) {
    header('Location: ../index.php');
    exit();
}

require_once 'app/authentication.php';
?>

<div class="container">
    <div class="row">
        <div class="col"></div>
        <div class="col-6">
            <form class="mb-3 mt-3" method="post" action="admin.php">
                <div class="form-group">
                    <label for="login">Login</label>
                    <input type="text" class="form-control" id="login" name="login" placeholder="Entrez votre login" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Entrez votre mot de passe" required>
                </div>
                <input type="hidden" name="<?=Token::TOKEN_NAME?>" value="<?= Token::generate(); ?>">
                <button type="submit" class="btn btn-primary">Envoyer</button>
            </form>
        </div>
        <div class="col"></div>
    </div>
</div>
