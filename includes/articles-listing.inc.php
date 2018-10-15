<?php
// If direct access
if(!file_exists('classes/DB.php')) {
    header('Location: ../index.php');
    exit();
}

require_once 'classes/DB.php';
require_once 'classes/Session.php';

// CHECK IF ADMIN OR MEMBER IS CURRENTLY LOGGED IN
$db = DB::getInstance();
$adminMode = Session::exists('admin') && Session::exists('login') && $db->isSessionValid(Session::get('login'), Session::get('admin'));
$memberMode = Session::exists('member') && Session::exists('login') && $db->isSessionValid(Session::get('login'), Session::get('member'));

// ADMIN Actions
if ($adminMode && isset($_GET['action']) && isset($_GET['id'])) {
    switch ($_GET['action']) {
        case 'update_content' :
            require_once 'forms/update_content_article.php';
            break;
        case 'update_status' :
            require_once 'app/update_status_article.php';
            break;
        case 'delete' :
            require_once 'app/delete_article.php';
            break;
        default:
            break;
    }
}
// END ADMIN Actions

// ADMIN and MEMBER Actions
else if (($adminMode || $memberMode) && isset($_GET['action']) && $_GET['action'] == 'new_article') {
    require_once 'forms/new_article.php';
}
// END ADMIN and MEMBER Actions

// START DISPLAY ARTICLES TABLE
else {
    ?>
    <?php if ($articleFiltered && $totalNbPostsPagination < $totalNbPosts) : ?>
        <a href="?reset=true" class="btn btn-primary mb-3">Voir tous les articles</a>
    <?php endif; ?>

    <?php if (count($posts)) : ?>

        <?php require_once 'includes/pagination.inc.php'; ?>

        <table class="articles table table-light">
            <thead>
                <tr>
                    <th class="align-middle" scope="col"><a href="?sortTitle=<?= $sortTitle ?? 'ASC' ?>">Titre</a></th>
                    <th class="align-middle" scope="col"><a href="?sortCreatedTime=<?= $sortCreatedTime ?? 'ASC' ?>">Date de publication</a></th>
                    <th class="align-middle" scope="col"><a href="?sortAuthor=<?= $sortAuthor ?? 'ASC' ?>">Auteur</a></th>
                    <th class="align-middle" scope="col"><a href="?sortCategory=<?= $sortCategories ?? 'ASC' ?>">Catégorie(s)</a></th>
                    <?php if ($adminMode) : ?>
                        <th class="align-middle text-center" scope="col">Editer</th>
                        <th class="align-middle text-center" scope="col"><a href="?sortIsPublished=<?= $sortIsPublished ?? 'ASC' ?>">Publier / Cacher</th>
                        <th class="align-middle text-center" scope="col">Supprimer</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($posts as $post) : ?>
                <?php $postCategories = explode(',', $post->categories); ?>
                <?php $nbCategories = count($postCategories); ?>

                <tr>
                    <td class="align-middle"><a href="article.php?article=<?=$post->idPost?>" target="_blank"><?=$post->title?></a></td>
                    <td class="align-middle"><?=$post->createdTime?></td>
                    <td class="align-middle"><a href="?author=<?=$post->idUser?>"><?=$post->firstName . ' ' . $post->lastName?></a></td>
                    <td class="align-middle">
                    <?php $indexCategory = 1; ?>
                    <?php foreach ($postCategories as $postCategory) : ?>
                        <a href="?category=<?=$postCategory?>"><?=$postCategory?></a>
                        <?=($indexCategory < $nbCategories) ? ', &nbsp;' : '';?>
                        <?php $indexCategory++?>
                    <?php endforeach ?>
                    </td>

                    <?php if ($adminMode) : ?>
                        <?php
                        $pageParam = '';
                        if(isset($idPage)) {
                            $pageParam = '&amp;page=' . $idPage;
                        }
                        if(isset($urlSortParams)) {
                            $pageParam .= $urlSortParams;
                        }
                        ?>
                        <td class="text-center align-middle"><a href="?action=update_content<?=$pageParam?>&amp;id=<?=$post->idPost?>"><i class="fas fa-pencil-alt"></i></a></td>
                        <td class="text-center align-middle"><a href="?action=update_status<?=$pageParam?>&amp;status=<?=$post->isPublished?>&amp;id=<?=$post->idPost?>">
                        <?php if ($post->isPublished == "1") : ?>
                            <i class="fas fa-eye"></i></a></td>
                        <?php else :?>
                            <i class="far fa-eye-slash"></i></a></td>
                        <?php endif; ?>
                        <td class="text-center align-middle"><a href="?action=delete&amp;id=<?=$post->idPost?>"><i class="fas fa-times"></i></a></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif (isset($search)): ?>
        <p>Pas de résultats pour la recherche "<?= $search ?>".</p>
    <?php elseif (isset($author)): ?>
        <p>Aucun article n'existe pour cet auteur.</p>
    <?php else: ?>
        <p>Pas d'articles pour le moment.</p>
    <?php endif; ?>
<?php
}
// END DISPLAY ARTICLES TABLE
