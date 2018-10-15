<?php
// If direct access
if(!file_exists('classes/DB.php')) {
    header('Location: ../index.php');
    exit();
}

require_once 'classes/DB.php';
require_once 'classes/Session.php';

// CHECK IF ADMIN IS CURRENTLY LOGGED IN
$db = DB::getInstance();
$adminMode = Session::exists('admin') && Session::exists('login') && $db->isSessionValid(Session::get('login'), Session::get('admin'));

// Admin actions
if($adminMode && isset($_GET['action']) && isset($_GET['id'])) {
    switch($_GET['action']) {
        case 'update_user' : require_once 'forms/update_user.php'; break;
        case 'update_user_status' : require_once 'app/update_user_status.php'; break;
        case 'delete_user' : require_once 'app/delete_user.php'; break;
        default: break;
    }
// END Admin actions

// Members table listing
} else if ($adminMode) {
    require_once 'includes/pagination.inc.php';
    ?>
    <table class="table table-light">
        <thead>
        <tr>
            <th scope="col"><a href="?showUsers=true&amp;sortUserLastname=<?=$sortUserLastname ?? 'ASC'?>">Nom</a></th>
            <th scope="col"><a href="?showUsers=true&amp;sortUserFirstname=<?=$sortUserFirstname ?? 'ASC'?>">Prénom</a></th>
            <th scope="col"><a href="?showUsers=true&amp;sortUserEmail=<?=$sortUserEmail ?? 'ASC'?>">Email</a></th>
            <th scope="col"><a href="?showUsers=true&amp;sortUserBirthdate=<?=$sortUserBirthdate ?? 'ASC'?>">Date de naissance</a></th>
            <th scope="col"><a href="?showUsers=true&amp;sortUserLogin=<?=$sortUserLogin ?? 'ASC'?>">Login</a></th>
            <th scope="col"><a href="?showUsers=true&amp;sortUserNickname=<?=$sortUserNickname ?? 'ASC'?>">Surnom</a></th>
            <th scope="col"><a href="?showUsers=true&amp;sortUserRegistered=<?=$sortUserRegistered ?? 'ASC'?>">Créé le</a></th>
            <th scope="col"><a href="?showUsers=true&amp;sortUserLastLogin=<?=$sortUserLastLogin ?? 'ASC'?>">Dernière Connexion</a></th>
            <th scope="col"><a href="?showUsers=true&amp;sortUserIsAdmin=<?=$sortUserIsAdmin ?? 'ASC'?>">Admin</a></th>
            <th scope="col">Editer</th>
            <th scope="col"><a href="?showUsers=true&amp;sortUserIsActive=<?=$sortUserIsActive ?? 'ASC'?>">Activer / Désactiver</th>
            <th scope="col"><a href="?showUsers=true&amp;sortUserNbArticles=<?=$sortUserNbArticles ?? 'ASC'?>">Nombre d'articles</th>
            <th scope="col">Supprimer</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user) : ?>
            <tr>
                <td><?=$user->lastName?></td>
                <td><?=$user->firstName?></td>
                <td><?=$user->email?></td>
                <td><?=$user->birthDate?></td>
                <td><?=$user->login?></td>
                <td><?=$user->nickName?></td>
                <td><?=$user->registeredDate?></td>
                <td><?=$user->lastLogin?></td>
                <td class="text-center"><?= ($user->isAdmin == '1') ? '<span class="font-weight-bold">Oui</span>' : 'Non'?></td>
                <?php
                    $pageParam = '';
                    if(isset($idPage)) {
                        $pageParam = '&amp;page=' . $idPage;
                    }
                    if(isset($urlSortParams)) {
                        $pageParam .= $urlSortParams;
                    }
                ?>
                <td class="text-center"><a href="?showUsers=true<?=$pageParam?>&amp;action=update_user&amp;id=<?=$user->idUser?>"><i class="fas fa-pencil-alt"></i></a></td>
                <td class="text-center"><a href="?showUsers=true<?=$pageParam?>&amp;action=update_user_status&amp;status=<?=$user->isActive?>&amp;id=<?=$user->idUser?>">
                <?php if($user->isActive == "1") :?>
                    <i class="fas fa-user"></i></a></td>
                <?php else : ?>
                    <i class="fas fa-user-slash"></i></a></td>
                <?php endif; ?>

                <?php
                $sql = 'SELECT COUNT(*) AS nbArticles FROM post WHERE idUser = :idUser';
                $bindings = ['idUser' => $user->idUser];
                $nbArticles = $db->query($sql, $bindings, PDO::FETCH_COLUMN, 0)[0];
                ?>

                <?php if($nbArticles == 0) : ?>
                    <td class="text-center"><?=$nbArticles?></td>
                    <td class="text-center"><a href="?showUsers=true&amp;action=delete_user&amp;id=<?=$user->idUser?>"><i class="fas fa-times"></i></a></td>
                <?php else : ?>
                    <td class="text-center"><a href="?author=<?=$user->idUser?>"><?=$nbArticles?></a></td>
                    <td class="text-center text-muted"><i class="fas fa-times"></i></td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php }
// END Members table listing