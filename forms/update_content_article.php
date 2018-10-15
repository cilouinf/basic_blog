<?php
// If direct access
if(!file_exists('app/update_content_article.php')) {
    header('Location: ../index.php');
    exit();
}

require_once 'app/update_content_article.php';

if(Session::exists('error')) {
    echo '<p class="alert alert-danger">' . Session::get('error') . '</p>';
    Session::delete('error');
}
?>

<div class="container">
    <div class="row">
        <div class="col">
            <h2 class="text-center">Mise à jour du contenu d'un article</h2>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <form class="mb-3 mt-3" enctype="multipart/form-data" method="post" action="">
                <div class="form-group">
                    <label for="title">Titre</label>
                    <input type="text" class="form-control" id="title" name="title" maxlength="128" value="<?=$post->title?>" required>
                </div>
                <div class="form-group">
                    <label for="image">Image actuelle</label>
                    <input type="text" class="form-control" id="image" name="image" value="<?=$postImageName?>" readonly required>
                    <input type="text" class="form-control" name="imageHidden" value="<?=$postImageName?>" required hidden>
                </div>
                <div class="update-art-prev-img">
                    <img src="<?=$post->image?>">
                </div>
                <div class="form-group">
                    <label for="file">Remplacer l'image actuelle</label>
                    <input type="file" class="form-control-file" id="file" name="file" accept="image/gif, image/jpeg, image/jpg,image/png">
                </div>
                <div class="form-group">
                    <label for="content">Contenu</label>
                    <textarea rows="10" cols="50" class="form-control" id="content" name="content" required><?=$post->content?></textarea>
                </div>
                <div class="form-group">
                    <label for="categories">Catégories</label>
                    <select type="text" class="form-control" id="categories" name="categories[]" multiple required>
                        <?php foreach($postCategories as $postCategory) : ?>
                            <option selected><?=$postCategory?></option>
                        <?php endforeach; ?>
                        <?php foreach($blogCategories as $blogCategory) : ?>
                            <?php if(!in_array($blogCategory->categoryName, $postCategories)) : ?>
                                <option><?=$blogCategory->categoryName?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <input type="hidden" name="<?=Token::TOKEN_NAME?>" value="<?=Token::generate()?>">
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
            </form>
        </div>
    </div>
</div>