<?php
// If direct access
if(!file_exists('app/insert_article.php')) {
    header('Location: ../index.php');
    exit();
}

// else
require_once 'app/insert_article.php';

if(Session::exists('error')) {
    echo '<p class="alert alert-danger">' . Session::get('error') . '</p>';
    Session::delete('error');
}
?>

<div class="container">
    <div class="row">
        <div class="col">
            <h2 class="text-center">Création d'un article</h2>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <form class="mb-3 mt-3" enctype="multipart/form-data" method="post" action="">
                <div class="form-group">
                    <label for="title">Titre</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="Titre" value="<?=($formTitle ?? '')?>" required>
                </div>
                <div class="form-group">
                    <label for="image">Image actuelle</label>
                    <input type="text" class="form-control" id="image" name="image" value="<?=$postImageName?>" readonly required>
                    <input type="text" class="form-control" name="imageHidden" value="<?=$postImageName?>" required hidden>
                </div>
                <div class="form-group">
                    <label for="file">Remplacer l'image actuelle</label>
                    <input type="file" class="form-control-file" id="file" name="file" accept="image/gif, image/jpeg, image/jpg,image/png">
                </div>
                <div class="form-group">
                    <label for="content">Contenu</label>
                    <textarea rows="10" cols="50" class="form-control" id="content" name="content" placeholder="Contenu de l'article"  required><?=($formContent ?? '')?></textarea>
                </div>
                <div class="form-group">
                    <label for="categories">Catégories</label>
                    <select type="text" class="form-control" id="categories" name="categories[]" multiple required>
                        <?php foreach($allCategories as $category) : ?>
                            <?php if(isset($formCategories) && in_array($category->categoryName, $formCategories)) : ?>
                                <option selected><?=$category->categoryName?></option>
                            <?php else : ?>
                                <option><?=$category->categoryName?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if($adminMode) :?>

                    <div class="form-group">
                        <label for="ispublished">Est publié</label>
                        <input type="number" min="0" max="1" class="form-control-file" id="ispublished" name="ispublished" value=<?= $formIsPublished ?? 0 ?>>
                    </div>

                <?php endif; ?>

                <input type="hidden" name="<?=Token::TOKEN_NAME?>" value="<?=Token::generate()?>">
                <button type="submit" class="btn btn-primary">Créer</button>
            </form>
        </div>
    </div>
</div>