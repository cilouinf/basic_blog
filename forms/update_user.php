<?php
// If direct access
if(!file_exists('app/update_user.php')) {
    header('Location: ../index.php');
    exit();
}

require_once 'app/update_user.php';

if(Session::exists('error')) {
    echo '<p class="alert alert-danger">' . Session::get('error') . '</p>';
    Session::delete('error');
}
?>

<div class="container">
    <div class="row">
        <div class="col">
            <h2 class="text-center">Mise à jour des données d'un membre</h2>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <form class="mb-3 mt-3" enctype="multipart/form-data" method="post" action="">
                <div class="form-group">
                    <label for="lastname">Nom</label>
                    <input type="text" class="form-control" id="lastname" name="lastname"  maxlength="64" value="<?=$user->lastName?>" required>
                </div>

                <div class="form-group">
                    <label for="firstname">Prénom</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" maxlength="64" value="<?=$user->firstName?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?=$user->email?>" maxlength="128" required>
                </div>

                <div class="form-group">
                    <label for="birthdate">Date de naissance</label>
                    <input type="date" class="form-control" id="birthdate" name="birthdate" value="<?=$user->birthDate?>" min="1920-01-01" max="2018-05-29" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" required>
                </div>

                <div class="form-group">
                    <label for="nickname">Surnom</label>
                    <input type="text" class="form-control" id="nickname" name="nickname" maxlength="64" value="<?=$user->nickName?>" required>
                </div>

                <div class="form-group">
                    <label for="isadmin">Privilèges administrateur</label>
                    <input type="number" min="0" max="1" class="form-control" id="isadmin" name="isadmin" value="<?=$user->isAdmin?>" required>
                </div>

                <input type="hidden" name="<?=Token::TOKEN_NAME?>" value="<?=Token::generate()?>">
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
            </form>
        </div>
    </div>
</div>
