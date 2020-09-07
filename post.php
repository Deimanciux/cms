<?php include "includes/db.php" ?>
<?php include "includes/header.php" ?>

<?php
if(isset($_POST['liked'])) {
    //1 Fetching the right post,
    $post_id = $_POST['post_id'];
    $user_id = $_POST['user_id'];

    if(userLikedPost($post_id)) {
        exit;
    }

    $query ="SELECT * FROM posts WHERE post_id = $post_id";
    $postResult = mysqli_query($connection, $query);
    confirmQuery($postResult);
    $post = mysqli_fetch_array($postResult);
    $likes = $post['likes'];

    //2 Update the post with likes
    $postResult = mysqli_query($connection, "UPDATE posts SET likes = $likes+1 WHERE post_id = $post_id");
    confirmQuery($postResult);

    //3 Create likes for posts
    mysqli_query($connection, "INSERT INTO likes (user_id, post_id) VALUES ($user_id, $post_id)");
    exit();
}

if(isset($_POST['unliked'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_POST['user_id'];

    if(!userLikedPost($post_id)) {
        exit;
    }

    //1 fetching likes
    $query ="SELECT * FROM posts WHERE post_id = $post_id";
    $postResult = mysqli_query($connection, $query);
    confirmQuery($postResult);
    $post = mysqli_fetch_array($postResult);
    $likes = $post['likes'];

    //2 Delete(decrement) likes from post
    mysqli_query($connection, "UPDATE posts SET likes = $likes-1 WHERE post_id = $post_id");

    //3 Delete likes from likes table
    mysqli_query($connection, "DELETE FROM likes WHERE post_id = $post_id AND user_id = $user_id");
    exit();
}
?>

    <!-- Navigation -->
<?php include "includes/navigation.php" ?>

    <!-- Page Content -->
    <div class="container">

    <div class="row">

        <!-- Blog Entries Column -->
        <div class="col-md-8">

            <?php

            if(isset($_GET['p_id'])) {
                $the_post_id = $_GET['p_id'];

                $query ="UPDATE posts SET post_views_count = post_views_count + 1 WHERE post_id = $the_post_id";
                $view_query = mysqli_query($connection, $query);
                if(!$view_query){
                    die("Query failed" . mysqli_error($connection));
                }
                if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'){
                    $query = "SELECT * FROM posts WHERE post_id= $the_post_id";
                } else{
                    $query = "SELECT * FROM posts WHERE post_id= $the_post_id AND post_status='published'";
                }

            $select_all_posts_query = mysqli_query($connection, $query);

                if(mysqli_num_rows($select_all_posts_query) < 1){
                echo"<h1 class='text-center'>No posts available</h1>";
            } else {

            while($row = mysqli_fetch_assoc($select_all_posts_query)) {
                $post_title =  $row['post_title'];
                $post_user =  $row['post_user'];
                $post_date =  $row['post_date'];
                $post_image =  $row['post_image'];
                $post_content =  $row['post_content'];
                ?>

                <h2> <?php echo $post_title; ?> </h2>
                <p class="lead">
                    by <a href="../author_posts.php?user=<?php echo $post_user;?>&p_id=<?php echo $the_post_id;?>"><?php echo $post_user ?></a>
                </p>
                <p><span class="glyphicon glyphicon-time"></span><?php echo $post_date ?></p>
                <hr>
                <img class="img-responsive" src="../images/<?php echo imagePlaceholder($post_image); ?>" alt="">
                <hr>
                <p><?php echo $post_content ?></p>

                <?php
                //mysqli_free_result($select_all_posts_query);
                //mysqli_stmt_free_result($stmt);
                ?>

                <hr>
                <?php if(isLoggedIn()) : ?>
                    <div class="row">
                        <p class="pull-right">

                            <a class ="<?php echo userLikedPost($the_post_id) ? 'unlike' : 'like'; ?>" href="">
                                <span class="glyphicon glyphicon-thumbs-<?php echo userLikedPost($the_post_id) ? 'down' : 'up';?>"
                                data-toggle="tooltip"
                                data-placement='top'
                                title ="<?php echo userLikedPost($the_post_id) ? 'I liked this before' : 'Want to like it?'?> "
                                ></span>
                                <?php echo userLikedPost($the_post_id) ? 'Unlike' : 'Like'; ?>
                            </a>
                        </p>
                    </div>
                    <div class="clearfix"></div>

                <?php else: ?>

                    <div class="row">
                        <p class="pull-right">You need to <a href="/cms/login.php">Login</a> to leave a like </p>
                    </div>

                <?php endif; ?>

                <div class="row">
                    <p class="pull-right">Likes: <?php getPostLikes($the_post_id); ?></p>
                </div>
            <!-- Blog Comments -->

            <?php

            }
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




            <!-- Comments Form -->
            <div class="well">
                <h4>Leave a Comment:</h4>
                <form action="" method="post" role="form">
                    <div class="form-group">
                        <label for="Author">Author</label>
                        <input type="text" class="form-control" name="comment_author">
                    </div>
                    <div class="form-group">
                        <label for="Email">Email</label>
                        <input type="email" class="form-control" name="comment_email">
                    </div>
                    <div class="form-group">
                        <label for="comment">Your comemnt</label>
                        <textarea class="form-control" name="comment_content" rows="3"></textarea>
                    </div>
                    <button type="submit" name="create_comment" class="btn btn-primary">Submit</button>
                </form>
            </div>

            <hr>

            <!-- Posted Comments -->

            <?php
            $query = "SELECT * FROM comments WHERE comment_post_id = {$the_post_id} ";
            $query .= "AND comment_status = 'approved'";
            $query .= "ORDER BY comment_id DESC";
            $select_comment_query = mysqli_query($connection, $query);

            if(!$select_comment_query){
                die('Query Failed ' . mysqli_error($connection));
            }

            while($row = mysqli_fetch_assoc($select_comment_query)) {
                $comment_date = $row['comment_date'];
                $comment_content = $row['comment_content'];
                $comment_author = $row['comment_author'];
                ?>

                <div class="media">
                    <a class="pull-left" href="#">
                        <img class="media-object" src="http://placehold.it/64x64" alt="">
                    </a>
                    <div class="media-body">
                        <h4 class="media-heading"><?php echo $comment_author;?>
                            <small><?php echo $comment_date;?></small>
                        </h4>
                        <?php echo $comment_content;?>
                    </div>
                </div>

            <?php }
                }
            } else {
                header("Location: index.php");
            }
            ?>

            <!-- Comment -->
        </div>

        <!-- Blog Sidebar Widgets Column -->
        <?php include "includes/sidebar.php"; ?>

    </div>
    <!-- /.row -->
    <hr>
<?php include "includes/footer.php"; ?>

        <script>
            $(document).ready(function() {
                $("[data-toggle='tooltip']").tooltip();
                let post_id = <?php echo $the_post_id; ?>;
                    let user_id = <?php echo loggedInUserId(); ?>;

                    //LIKING
                $(".like").click(function() {
                    $.ajax({
                        url: "/cms/post.php?p_id=<?php echo $the_post_id; ?>",
                        type: 'post',
                        data: {
                            'liked': 1,
                            //veiktu ir be kabuciu
                            'post_id': post_id,
                            'user_id': user_id
                        }
                    })
                });


                //UNLIKING
                $(".unlike").click(function() {
                    $.ajax({
                        url: "/cms/post.php?p_id=<?php echo $the_post_id; ?>",
                        type: 'post',
                        data: {
                            'unliked': 1,
                            'post_id': post_id,
                            'user_id': user_id
                        }
                    })
                });
            });
        </script>
