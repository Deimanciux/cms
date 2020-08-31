<?php include "includes/db.php" ?>
<?php include "includes/header.php" ?>

    <!-- Navigation -->
<?php include "includes/navigation.php" ?>

    <!-- Page Content -->
    <div class="container">

    <div class="row">

        <!-- Blog Entries Column -->
        <div class="col-md-8">

            <?php

            if(isset($_GET['p_id'])){
                $the_post_id=$_GET['p_id'];
                $the_post_user = $_GET['user'];
            }

            $query = "SELECT * FROM posts WHERE post_user = '{$the_post_user}' ORDER BY post_date DESC";
            $select_all_posts_query = mysqli_query($connection, $query); ?>

            <h1 class="page-header">All posts by <?php echo $the_post_user ?></h1>

            <?php
            while($row = mysqli_fetch_assoc($select_all_posts_query)){
                $post_title =  $row['post_title'];
                $post_user =  $row['post_user'];
                $post_date =  $row['post_date'];
                $post_image =  $row['post_image'];
                $post_content =  $row['post_content'];
                ?>

                <h2>
                    <a href="#"><?php echo $post_title; ?></a>
                </h2>

                <p><span class="glyphicon glyphicon-time"></span><?php echo $post_date ?></p>
                <hr>
                <img class="img-responsive" src="images/<?php echo $post_image ?>" alt="">
                <hr>
                <p><?php echo $post_content ?></p>


                <hr>
            <?php } ?>
            <!-- Blog Comments -->

            <?php
            if(isset($_POST['create_comment'])) {
                $the_post_id = $_GET['p_id'];
                $comment_author = $_POST['comment_author'];
                $comment_email = $_POST['comment_email'];
                $comment_content = $_POST['comment_content'];
                if(!empty($comment_author) && !empty($comment_email) && !empty($comment_content)){
                    $query ="INSERT INTO comments (comment_post_id, comment_author, comment_email, comment_content, comment_status, comment_date) ";
                    $query .="VALUES ($the_post_id, '{$comment_author}', '{$comment_email}', '{$comment_content}', 'unapproved', now())";

                    $create_comment_query = mysqli_query($connection, $query);
                    if(!$create_comment_query){
                        die('QUERY FAILED ' . mysqli_error($connection));
                    }
                } else {
                    //echo "<script>alert('Fields can not be empty')</script>";
                    echo "<p class='bg-danger'>Fields can not be empty. </p>";
                }
            }
            ?>
        </div>

        <!-- Blog Sidebar Widgets Column -->
        <?php include "includes/sidebar.php"; ?>

    </div>
    <!-- /.row -->
    <hr>
<?php include "includes/footer.php"; ?>