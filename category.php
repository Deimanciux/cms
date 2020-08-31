<?php include "includes/db.php" ?>
<?php include "includes/header.php" ?>
<?php include "admin/functions.php" ?>

<!-- Navigation -->
<?php include "includes/navigation.php" ?>
<!-- Page Content -->
<div class="container">

    <div class="row">

        <!-- Blog Entries Column -->
        <div class="col-md-8">

            <?php
            if(isset($_GET['category'])){
                $post_category_id = $_GET['category'];

                if(isset($_SESSION['username']) && is_admin($_SESSION['username'])){

                    $stmt1 = mysqli_prepare($connection, "SELECT post_id, post_title, post_user, post_date, post_image, post_content FROM posts WHERE post_category_id = ?/*ORDER BY post_id DESC*/");

                } else {
                    $stmt2 = mysqli_prepare($connection, "SELECT post_id, post_title, post_user, post_date, post_image, post_content FROM posts WHERE post_category_id = ? AND post_status= ?/*ORDER BY post_id DESC*/");
                    $published = 'published';
                }

                if(isset($stmt1)){
                    //rasom i, nes integer. Jei string tada turi but s. Vietoj klaustuku auksciau
                    mysqli_stmt_bind_param($stmt1, "i", $post_category_id);
                    mysqli_stmt_execute($stmt1);
                    mysqli_stmt_bind_result($stmt1,$post_id,$post_title, $post_user, $post_date, $post_image, $post_content);
                    $stmt = $stmt1;
                } else{
                    //cia butinai skliausteliuose turi buti variables
                    mysqli_stmt_bind_param($stmt2, 'is', $post_category_id, $published);
                    mysqli_stmt_execute($stmt2);
                    mysqli_stmt_bind_result($stmt2,$post_id, $post_title, $post_user, $post_date, $post_image, $post_content);
                    $stmt = $stmt2;
                }
                mysqli_stmt_store_result($stmt);

            if(mysqli_stmt_num_rows($stmt) != 0){

            while($row = mysqli_stmt_fetch($stmt)):

                ?>

                <h2>
                    <a href="../post/<?php echo $post_id;?>"><?php echo $post_title ?></a>
                </h2>
                <p class="lead">
                    by <a href="index.php"><?php echo $post_user; ?></a>
                </p>
                <p><span class="glyphicon glyphicon-time"></span><?php echo $post_date ?></p>
                <hr>
                <a href="../post/<?php echo $post_id;?>">
                    <img class="img-responsive" src="../images/<?php echo $post_image; ?>" alt="">
                </a>

                <hr>

                <?php if(strlen($post_content) >= 100) {
                    $post_content = substr($post_content, 0, 100) . "...";
                } ?>

                <p><?php echo $post_content ?></p>
                <a class="btn btn-primary" href="../post/<?php echo $post_id;?>">Read More <span class="glyphicon glyphicon-chevron-right"></span></a>
                <hr>
           <!--nebutinas dalykas mysqli_stmt_close, nes pats php ta padaro-->
            <?php endwhile; mysqli_stmt_close($stmt);

            } else {
                echo"<h1 class='text-center'>No posts available</h1>";
            }} else{
               //header("Location: index.php");
            } ?>

        </div>

        <!-- Blog Sidebar Widgets Column -->
        <?php include "includes/sidebar.php" ?>

    </div>
    <!-- /.row -->

    <hr>

    <?php include "includes/footer.php" ?>
