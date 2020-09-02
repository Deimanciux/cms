<div class="table-responsive">
<?php include "delete_modal.php"; ?>
    <?php
    if(isset($_POST['checkBoxArray'])){

        foreach($_POST['checkBoxArray'] as $post_value_id) {
           $bulk_options= $_POST['bulk_options'];
           switch ($bulk_options){
               case 'published':
                   $query = "UPDATE posts SET post_status='{$bulk_options}' WHERE post_id={$post_value_id}";
                   $update_published_status= mysqli_query($connection, $query);
                   confirmQuery($update_published_status);
                   break;
               case 'draft':
                   $query = "UPDATE posts SET post_status='{$bulk_options}' WHERE post_id={$post_value_id}";
                   $update_draft_status= mysqli_query($connection, $query);
                   confirmQuery($update_draft_status);
                   break;
               case 'delete':
                   $query = "Delete FROM posts WHERE post_id={$post_value_id}";
                   $delete_post= mysqli_query($connection, $query);
                   confirmQuery($delete_post);
                   $query = "DELETE FROM comments WHERE comment_post_id = {$post_value_id}";
                   $delete_comments_query = mysqli_query($connection, $query);
                   confirmQuery($delete_comments_query);
                   break;
               case 'clone':
                   $query = "SELECT * FROM posts WHERE post_id={$post_value_id}";
                   $select_post_query = mysqli_query($connection, $query);
                   confirmQuery($select_post_query);

                   while($row = mysqli_fetch_array($select_post_query)) {
                       $post_title = $row['post_title'];
                       $post_category_id = $row['post_category_id'];
                       $post_date = $row['post_date'];
                       $post_user = $row['post_user'];
                       $post_status = $row['post_status'];
                       $post_image = $row['post_image'];
                       $post_tags = $row['post_tags'];
                       $post_content = $row['post_content'];
                   }
                   $query = "INSERT INTO posts (post_category_id, post_title, post_user, post_date, post_image, post_content, post_tags, post_status) ";
                   $query .= "VALUES ({$post_category_id}, '{$post_title}', '{$post_user}', now(), '{$post_image}', '{$post_content}', '{$post_tags}', '{$post_status}') ";
                   $copy_query = mysqli_query($connection, $query);
                   confirmQuery($copy_query);
                   break;
           }
        }

    }

    ?>

    <form action="" method="post">
<table class="table table-bordered table-hover">
    <div id="bulkOptionContainer" class="col-xs-4">
        <select class="form-control" name="bulk_options" id="">
            <option value="">Select Options</option>
            <option value="published">Publish</option>
            <option value="draft">Draft</option>
            <option value="delete">Delete</option>
            <option value="clone">Clone</option>
        </select>
    </div class='col-xs-4'>
    <input type='submit' name="submit" class="btn btn-success" value="Apply">
    <a class="btn btn-primary" href="posts.php?source=add_post">Add New</a>

    <thead>
    <tr>
        <th><input id="selectAllBoxes" type="checkbox"></th>
        <th>Id</th>
        <th>Users</th>
        <th>Title</th>
        <th>Category</th>
        <th>Status</th>
        <th>Image</th>
        <th>Tags</th>
        <th>Comments</th>
        <th>Date</th>
        <th>View Post</th>
        <th>Published</th>
        <th>Draft</th>
        <th>Edit</th>
        <th>Dalete</th>
        <th>Views</th>
    </tr>
    </thead>
    <tbody>

    <?php
   // $query = "SELECT * FROM posts ORDER BY post_date DESC";
    $query = "SELECT posts.post_id, posts.post_user, posts.post_title, posts.post_category_id, posts.post_status, ";
    $query .= "posts.post_image, posts.post_tags, posts.post_date, posts.post_views_count, categories.cat_id, categories.cat_title ";
    $query .= "FROM posts LEFT JOIN categories ON posts.post_category_id = categories.cat_id ORDER BY posts.post_id DESC";
    $select_posts = mysqli_query($connection, $query);
    confirmQuery($select_posts);

    while ($row = mysqli_fetch_assoc($select_posts)) {
        $post_id = $row['post_id'];
        $post_user = $row['post_user'];
        $post_title = $row['post_title'];
        $post_category_id = $row['post_category_id'];
        $post_status = $row['post_status'];
        $post_image = $row['post_image'];
        $post_tags = $row['post_tags'];
        $post_date = $row['post_date'];
        $post_views_count = $row['post_views_count'];
        $category_title = $row['cat_title'];
        $category_id = $row['cat_id'];
        echo "<tr>";
        ?>

        <td><input class='checkBoxes' type='checkbox' name="checkBoxArray[]" value="<?php echo $post_id; ?>"></td>

        <?php
        echo "<td>$post_id</td>";

        echo "<td>$post_user</td>";

        echo "<td>$post_title</td>";

        /* $query = "SELECT * FROM categories WHERE cat_id = {$post_category_id}";
         $select_categories_id = mysqli_query($connection, $query);

            while ($row = mysqli_fetch_assoc($select_categories_id)) {
                $cat_id = $row['cat_id'];
                $cat_title = $row['cat_title'];*/

                echo "<td>{$category_title}</td>";
           // }*/

        echo "<td>$post_status</td>";
        echo "<td><img  width='100' src='../images/$post_image' alt='image'></td>";
        echo "<td>$post_tags</td>";

        $query = "SELECT * FROM comments WHERE comment_post_id = $post_id";
        $send_comment_query = mysqli_query($connection, $query);
        $row = mysqli_fetch_array($send_comment_query);
        $count_comments = mysqli_num_rows($send_comment_query);

        echo "<td><a href='post_comment.php?id=$post_id'>$count_comments</a></td>";

        echo "<td>$post_date</td>";
        echo "<td><a class='btn btn-primary' href='../post/$post_id'>View Post</a></td>";
        echo "<td><a href='posts.php?publish={$post_id}'>Publish</a></td>";
        echo "<td><a href='posts.php?draft={$post_id}'>Draft</a></td>";
        echo "<td><a class='btn btn-info' href='posts.php?source=edit_post&p_id={$post_id}'>Edit</a></td>";
        //echo "<td><a onClick=\"javascript: return confirm('Are you sure you want to delete this post?');\"href='posts.php?delete={$post_id}'>Delete</a></td>";
        ?>

        <form method="post">
            <input type="hidden" name="post_id" value="<?php echo $post_id?>">
            <?php
           echo '<td><input class="btn btn-danger" type="submit" value="Delete" name="delete"></td>';
            ?>
        </form>

        <?php
       // echo "<td><a  rel='$post_id' href='javascript:void(0)' class='delete_link'>Delete</a></td>";
        echo "<td><a href='posts.php?reset={$post_id}'>{$post_views_count}</a></td>";
        echo "</tr>";

    }
    ?>
    </tbody>
</table>
    </form>
</div>
<?php
if(isset($_POST['delete'])){
    $the_post_id = escape($_POST['post_id']);

    $query = "DELETE FROM posts WHERE post_id = {$the_post_id}";
    $delete_query = mysqli_query($connection, $query);

    $query = "DELETE FROM comments WHERE comment_post_id = {$the_post_id}";
    $delete_comments_query = mysqli_query($connection, $query);
    header("Location: posts.php");
}
if(isset($_GET['reset'])){
    $the_post_id = escape($_GET['reset']);

    $query = "UPDATE posts SET post_views_count = 0 WHERE post_id = {$the_post_id}";
    $reset_query = mysqli_query($connection, $query);
    redirect('Location: posts.php');
}
if(isset($_GET['publish'])){
    $the_post_id = $_GET['publish'];
    if($post_status!=='published'){
        $query = "UPDATE posts SET post_status = published WHERE post_id = {$the_post_id} ";
        $publish_query = mysqli_query($connection, $query);
        header("Location: posts.php");
    }
    else{
        header("Location: posts.php");
    }
}
if(isset($_GET['draft'])){
    $the_post_id = $_GET['draft'];
    if($post_status!=='draft'){
        $query = "UPDATE posts SET post_status = draft WHERE post_id = {$the_post_id} ";
        $draft_query = mysqli_query($connection, $query);
        header("Location: posts.php");
    }
    else{
        header("Location: posts.php");
    }
}
?>
<script>
    $(document).ready(function () {
        $(".delete_link").on('click', function(){
            var id = $(this).attr("rel");
            var delete_url ='posts.php?delete=' + id + "";
            $(".modal_delete_link").attr("href", delete_url);
            $("#myModal").modal('show');
    });
    });
</script>
