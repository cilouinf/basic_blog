<?php
// If direct access
if(!file_exists('classes/Image.php')) {
    header('Location: ../index.php');
    exit();
}

require_once 'classes/Image.php';
require_once 'classes/DB.php';
require_once 'classes/Session.php';
require_once 'classes/Token.php';

$db = DB::getInstance();

// Check if user is authenticated and redirect to homepage if not
$adminMode = Session::exists('admin') && Session::exists('login') && $db->isSessionValid(Session::get('login'), Session::get('admin'));
$memberMode = Session::exists('member') && Session::exists('login') && $db->isSessionValid(Session::get('login'), Session::get('member'));

if(!($memberMode || $adminMode)) {
    header('Location: index.php');
    exit();
}

$formImage = $previousImage;

if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
    $formImageDirectory = Image::DIRECTORY;
    $filename = $_FILES['file']['name'];
    $filesize = $_FILES['file']['size'];
    $filetmploc = $_FILES['file']['tmp_name'];
    //http://php.net/manual/en/function.exif-imagetype.php
    $imgType = exif_imagetype($filetmploc);
    
    if ($filesize > Image::ARTICLE_MAX_SIZE) {
        $msg = 'L\'image dépasse la taille maximum supportée de ' . Image::ARTICLE_MAX_SIZE / 1024 . ' Ko.';
        $error = true;
    } else if ($imgType != IMAGETYPE_JPEG && $imgType != IMAGETYPE_PNG) {
        $msg = 'Seuls les fichiers image de type JPEG et PNG sont supportés.';
        $error = true;
    } else {
        $finfo = new finfo();
        $fileinfo = $finfo->file($filetmploc, FILEINFO_MIME);
        if ($fileinfo != 'image/jpeg; charset=binary' && $fileinfo != 'image/png; charset=binary') {
            $msg = 'Seuls les fichiers image de type JPEG et PNG sont supportés.';
            $error = true;
        }
    }
    
    if (!$error) {

        if($_FILES['file']['error'] == UPLOAD_ERR_NO_FILE) {
            $formImage = $previousImage;
            $formFilename = $previousImage;
        } else {
            $formFilename = Image::generateFilename($genImageId, $filename);
            if (!empty($formFilename)) {
                move_uploaded_file($filetmploc, $formImageDirectory . $formFilename);
            } else {
                $formFilename = $_POST['imageHidden'];
            }

            $formImage = $formImageDirectory . $formFilename;
        }
    }

} else if ($_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE) {
    $msg = 'L\'image dépasse la taille maximum supportée par le serveur (php.ini).';
    $error = true;

} else if ($_FILES['file']['error'] != UPLOAD_ERR_OK && $_FILES['file']['error'] != UPLOAD_ERR_NO_FILE) {
    $msg = 'Une erreur s\'est produite lors de l\'upload de l\'image. Code de l\'erreur : ' . $_FILES['file']['error'];
    $error = true;
}