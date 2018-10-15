<?php
// If direct access
if(!file_exists('classes/DB.php')) {
header('Location: ../index.php');
exit();
}
?>
<div class="container">
    <div class="row">
        <div class="col-4"></div>
        <div class="col-4">
            <nav class="pagination nav nav-pills nav-fill mb-1 justify-content-center">
                <?php
                    for($i = 1; $i <= $nbPages; $i++) {
                        if((isset($_GET['page']) && $_GET['page'] == $i) || (!isset($_GET['page']) && $i == 1)) {
                            $class = "nav-item nav-link active";
                        } else {
                            $class = "nav-item nav-link";
                        }
                        $linkStr = '<a href="?';
                        if($usersTable) {
                            $linkStr .= 'showUsers=true&amp;';
                        }
                        $linkStr .= 'page=' . $i . ($urlSortParams ?? null) . '" class="' . $class . '">' . $i . '</a>';
                        echo $linkStr;
                    }
                ?>
            </nav>
        </div>
        <div class="col-4"></div>
    </div>
</div>



