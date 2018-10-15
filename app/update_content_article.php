<?php
// If direct access
if(!file_exists('library/sanitize.php')) {
    header('Location: ../index.php');
    exit();
}

require_once 'library/sanitize.php';
require_once 'classes/DB.php';
require_once 'classes/Session.php';
require_once 'classes/Token.php';

$error = false;

$adminMode = Session::exists('admin') && Session::exists('login') && $db->isSessionValid(Session::get('login'), Session::get('admin'));

if(!$adminMode || (empty($_POST) && (!isset($_GET['id']) || !$db->isArticleValid(escape($_GET['id']))))) {
    header('Location: index.php');
    exit();
} else if(!empty($_POST)) {

    // CSRF protection
    if(!isset($_POST[Token::TOKEN_NAME]) || !Token::check($_POST[Token::TOKEN_NAME])) {
        header('Location: index.php');
        exit();
    }

    $idPost = escape($_GET['id']);
    $formTitle = escape($_POST['title']);
    $formContent = trim(escape($_POST['content']));
    $formCategories = $_POST['categories'];
    $genImageId = $idPost;

    // Retrieve previous image full path if any
    $sql = 'SELECT image FROM post WHERE idPost = :idPost';
    $bindings = ['idPost' => $idPost];
    $previousImage = $db->query($sql, $bindings, PDO::FETCH_COLUMN, 0)[0];
    if(!$previousImage) {
        $previousImage = '';
    }

    require_once 'app/image_upload.php';

    if (!$error) {

        //If not modif on article title, image and content, do not update those fields
        $sql = 'SELECT title, image, content FROM post WHERE idPost = :idPost';
        $bindings = ['idPost' => $idPost];
        $currentPost = $db->query($sql, $bindings)[0];
        $postUpdate =   !($currentPost->title == $formTitle &&
                        $currentPost->content == $formContent &&
                        $formImage == $previousImage);

        if($postUpdate) {
            $sql = 'UPDATE post 
                    SET title = :title, image = :image, content = :content
                    WHERE idPost = :idPost';
            $bindings = [   'title' => $formTitle,
                            'image' => $formImage ?? $previousImage,
                            'content' => $formContent,
                            'idPost' => $idPost];

            if (!$db->update($sql, $bindings)) {
                $error = true;
                $msg = 'Une erreur s\'est produite lors de la mise à jour de l\'article';
            } else {
                if (isset($formImage) && $formImage != $previousImage && !empty($previousImage)) {
                    unlink($previousImage);
                }
            }
        }

        // Check if categories need to be added or suppressed
        $sql = 'SELECT idCategory FROM postcategory WHERE idPost = :idPost';
        $bindings = ['idPost' => $idPost];
        $idPostCategories = $db->query($sql, $bindings, PDO::FETCH_COLUMN, 0);

        $sql = 'SELECT idCategory, categoryName FROM category';
        $allCategories = $db->query($sql, []);

        // Add ID of categories selected in form into an array
        $newPostCategoriesId = [];

        foreach ($formCategories as $formCategory) {
            foreach ($allCategories as $category) {
                if ($formCategory == $category->categoryName) {
                    array_push($newPostCategoriesId, $category->idCategory);
                }
            }
        }

        // Check if post has the categories selected
        foreach ($idPostCategories as $idPostCategory) {
            // if the current post contains a category not selected
            if (!in_array($idPostCategory, $newPostCategoriesId)) {
                $sql = 'DELETE FROM postcategory WHERE idPost = :idPost AND idCategory = :idCategory';
                $bindings = ['idPost' => $idPost, 'idCategory' => $idPostCategory];
                $db->delete($sql, $bindings);
            }
        }

        // Check if we need to add new categories to the post
        foreach ($newPostCategoriesId as $newCatId) {
            // If a new category has been selected
            if (!in_array($newCatId, $idPostCategories)) {
                $sql = 'INSERT INTO postcategory (idPost, idCategory) VALUES (:idPost, :idCategory)';
                $bindings = ['idPost' => $idPost, 'idCategory' => $newCatId];
                $db->insert($sql, $bindings);
            }
        }
        //}
    }
}

$sql = 'SELECT post.idPost, post.idUser, title, image, content, createdTime, firstName, lastName, GROUP_CONCAT(categoryName) AS categories, isPublished
        FROM post
        INNER JOIN user ON post.idUser = user.idUser
        INNER JOIN postcategory ON post.idPost = postcategory.idPost
        INNER JOIN category ON postcategory.idCategory = category.idCategory
        WHERE post.idPost = :idPost';
;

$bindings = ['idPost' => escape($_GET['id'])];
$post = $db->query($sql, $bindings)[0];
$postCategories = explode(',', $post->categories);

$sql = 'SELECT idCategory, categoryName FROM category ORDER BY categoryName';
$blogCategories = $db->query($sql);

$postImageName = explode('/', $post->image);
$postImageName = $postImageName[count($postImageName) - 1];
if(empty($postImageName)) $postImageName = 'Pas d\'image sélectionnée';

if(!empty($_POST)) {
    if(!$error) {
        Session::put('success', 'Article mis à jour avec succès');

        $location = 'Location: index.php';

        if(isset($idPage)) {
            $location .= '?page=' . $idPage;
        } else {
            $location .= '?page=1'; // in case page is not set in order to not have '&' immediately after the page name
        }
        if(isset($urlSortParams)) {
            $location .= $urlSortParams;
        }

        header($location);
        exit();

    } else {
        Session::put('error', 'Article non mis à jour : ' . $msg);
    }
}
