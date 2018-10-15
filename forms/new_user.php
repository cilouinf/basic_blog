<?php
// If direct access
if(!file_exists('app/insert_user.php')) {
    header('Location: ../index.php');
    exit();
}

require_once 'app/insert_user.php';

if(Session::exists('error')) {
    echo '<p class="alert alert-danger">' . Session::get('error') . '</p>';
    Session::delete('error');
}
?>

<div class="container">
    <div class="row">
        <div class="col">
            <h2 class="text-center">Création d'un compte membre</h2>
        </div>
    </div>
    <div class="row">
        <div class="col"></div>
        <div class="col-6">
            <form class="mb-3 mt-3" enctype="multipart/form-data" method="post" action="">
                <div class="form-group">
                    <label for="lastname">Nom</label>
                    <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Votre nom" maxlength="64" value="<?=$lastName ?? ''?>" required>
                </div>

                <div class="form-group">
                    <label for="firstname">Prénom</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Votre prénom" maxlength="64" value="<?=$firstName ?? ''?>" required>
                </div>

                <div class="form-group">
                    <label for="nickname">Surnom</label>
                    <input type="text" class="form-control" id="nickname" name="nickname" placeholder="Votre surnom" maxlength="64" value="<?=$nickName ?? ''?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Votre email" maxlength="128" value="<?=$email ?? ''?>" required>
                </div>

                <div class="form-group">
                    <label for="birthdate">Date de naissance</label>
                    <input type="date" class="form-control" id="birthdate" name="birthdate" placeholder="Votre date de naissance" min="1920-01-01" max="2018-05-29" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" value="<?=$birthDate ?? ''?>" required>
                </div>

                <div class="form-group">
                    <label for="login">Login</label>
                    <input type="text" class="form-control" id="login" name="login" placeholder="Votre login" maxlength="128" value="<?=$login ?? ''?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Votre mot de passe" maxlength="128" required>
                </div>

                <div class="form-group">
                    <label for="passwordcheck">Confirmation du mot de passe</label>
                    <input type="password" class="form-control" id="passwordcheck" name="passwordcheck" maxlength="128" placeholder="Confirmez le mot de passe" required>
                </div>

                <?php if($adminMode) :?>

                    <div class="form-group">
                        <label for="isactive">Est actif</label>
                        <input type="number" min="0" max="1" class="form-control-file" id="isactive" name="isactive" value="<?=$isActive ?? 1?>" required>
                    </div>

                    <div class="form-group">
                        <label for="isadmin">Est administrateur</label>
                        <input type="number" min="0" max="1" class="form-control-file" id="isadmin" name="isadmin" value="<?=$isAdmin ?? 0?>" required>
                    </div>

                <?php endif; ?>

                <input type="hidden" name="<?= Token::TOKEN_NAME ?>" value="<?= Token::generate(); ?>">
                <button type="submit" class="btn btn-primary">Créer</button>
            </form>
        </div>
        <div class="col"></div>
    </div>
</div>
