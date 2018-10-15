<?php
// If direct access
if(!file_exists('app/update_password.php')) {
    header('Location: ../index.php');
    exit();
}

require_once 'app/update_password.php';

if(Session::exists('error')) {
    echo '<p class="alert alert-danger">' . Session::get('error') . '</p>';
    Session::delete('error');
} else if(Session::exists('success')) {
    echo '<p class="alert alert-success">' . Session::get('success') . '</p>';
    Session::delete('success');
}
?>

<div class="container">
    <div class="row">
        <div class="col">
            <h2 class="text-center">Changement du mot de passe</h2>
        </div>
    </div>
    <div class="row">
        <div class="col"></div>
        <div class="col-6">
            <form class="mb-3 mt-3" enctype="multipart/form-data" method="post" action="">

                <div class="form-group">
                    <label for="currentpass">Mot de passe actuel</label>
                    <input type="password" class="form-control" id="currentpass" name="currentpass" placeholder="Mot de passe actuel" required>
                </div>

                <div class="form-group">
                    <label for="newpass">Nouveau mot de passe</label>
                    <input type="password" class="form-control" id="newpass" name="newpass" placeholder="Nouveau mot de passe" required>
                </div>

                <div class="form-group">
                    <label for="newpasscheck">Confirmez le nouveau mot de passe</label>
                    <input type="password" class="form-control" id="newpasscheck" name="newpasscheck" placeholder="Nouveau mot de passe" required>
                </div>

                <input type="hidden" name="<?=Token::TOKEN_NAME?>" value=<?=Token::generate()?>>
                <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
            </form>
        </div>
        <div class="col"></div>
    </div>
</div>

