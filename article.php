<?php

require_once 'app/get_full_article.php';
$windowTitle = $article->title;
require_once 'includes/header.inc.php';

?>

<!-- Start Article -->
<div class="container">
    <p><a class="btn btn-primary mt-3 mb-1" href="index.php">Retourner à la liste des articles</a></p>
    <div class="row">
        <div class="col">
            <div class="card mb-3 mt-3">
                <img class="card-img-top" src="<?=$article->image?>" alt="<?=$article->title?>">
                <div class="card-body">
                    <h1 class="card-title text-center pb-3"><?=$article->title?></h1>
                    <p class="card-text"><small class="text-muted">Publié le <?=$article->createdTime?> par <a href="index.php?author=<?=$article->idUser?>"><?=$article->firstName?> <?=$article->lastName?></a> dans les catégories <span class="font-weight-bold"><?=$article->categories?></span></small></p>
                    <p class="card-text"><small class="text-muted"></small></p>
                    <p class="card-text text-justify"><?=$article->content?></p>
                </div>
            </div>
        </div>
    </div>
    <p><a class="btn btn-primary mt-3 mb-1" href="index.php">Retourner à la liste des articles</a></p>
</div>

<!-- End Article -->

<?php
require_once 'includes/footer.inc.php';








