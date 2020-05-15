<?php include "includes/admin_header.php" ?>
<?php
$the_post_id = $_GET['id'];
mysqli_real_escape_string($connection, $the_post_id);
?>
<div id="wrapper">
    <!-- Navigation -->

    <?php include "includes/admin_navigation.php" ?>

    <div id="page-wrapper">

        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        Welcome to comments
                        <small>Author</small>
                    </h1>

                    <?php
                    if(isset($_POST['checkBoxArray'])){

                        foreach($_POST['checkBoxArray'] as $comment_value_id) {
                            $bulk_options= $_POST['bulk_options'];
                            switch ($bulk_options){
                                case 'approved':
                                    $query = "UPDATE comments SET comment_status='{$bulk_options}' WHERE comment_id={$comment_value_id}";
                                    $update_approve_status= mysqli_query($connection, $query);
                                    confirmQuery($update_approve_status);
                                    break;
                                case 'unapproved':
                                    $query = "UPDATE comments SET comment_status='{$bulk_options}' WHERE comment_id={$comment_value_id}";
                                    $update_unapprove_status= mysqli_query($connection, $query);
                                    confirmQuery($update_unapprove_status);
                                    break;
                                case 'delete':
                                    $query = "Delete FROM comments WHERE comment_id={$comment_value_id}";
                                    $delete_comment= mysqli_query($connection, $query);
                                    confirmQuery($delete_comment);
                                    break;
                                case 'clone':
                                    $query = "SELECT * FROM comments WHERE comment_id={$comment_value_id}";
                                    $select_comment_query = mysqli_query($connection, $query);
                                    confirmQuery($select_comment_query);

                                    while($row = mysqli_fetch_array($select_comment_query)) {
                                        $comment_author = $row['comment_author'];
                                        $comment_content = $row['comment_content'];
                                        $comment_email = $row['comment_email'];
                                        $comment_status = $row['comment_status'];
                                    }
                                    $query = "INSERT INTO comments (comment_post_id, comment_author, comment_content, comment_email, comment_status, comment_date) ";
                                    $query .= "VALUES ({$the_post_id}, '{$comment_author}', '{$comment_content}', '{$comment_email}', '{$comment_status}', now())";
                                    $copy_query = mysqli_query($connection, $query);
                                    confirmQuery($copy_query);
                                    break;
                            }
                        }

                    }

                    ?>
<form action="" method="post">
<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <div id="bulkOptionContainer" class="col-xs-4">
            <select class="form-control" name="bulk_options" id="">
                <option value="">Select Options</option>
                <option value="approved">Approve</option>
                <option value="unapproved">Unapprove</option>
                <option value="delete">Delete</option>
                <option value="clone">Clone</option>
            </select>
        </div class='col-xs-4'>
        <input type='submit' name="submit" class="btn btn-success" value="Apply">

        <a class="btn btn-primary" href="../post.php?p_id=<?php echo $the_post_id;?>">Add New</a>
       </form>
        <thead>
        <tr>
            <th><input id="selectAllBoxes" type="checkbox"></th>
            <th>Id</th>
            <th>Author</th>
            <th>Comments</th>
            <th>Email</th>
            <th>Status</th>
            <th>In Response</th>
            <th>Date</th>
            <th>Approve</th>
            <th>Unapprove</th>
            <th>Delete</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $query = "SELECT * FROM comments WHERE comment_post_id = $the_post_id";
        $select_comments = mysqli_query($connection, $query);

        while ($row = mysqli_fetch_assoc($select_comments)){
            $comment_id = $row['comment_id'];
            $comment_post_id = $row['comment_post_id'];
            $comment_author = $row['comment_author'];
            $comment_content = $row['comment_content'];
            $comment_email = $row['comment_email'];
            $comment_status = $row['comment_status'];
            $comment_date = $row['comment_date'];

            echo "<tr>";
            echo "<td><input class='checkBoxes' type='checkbox' name='checkBoxArray[]' value='$comment_id'></td>";
            echo "<td> $comment_id</td>";
            echo "<td>$comment_author</td>";
            echo "<td>$comment_content</td>";

            /* $query = "SELECT * FROM categories WHERE cat_id = {$post_category_id}";
             $select_categories_id = mysqli_query($connection, $query);

             while ($row = mysqli_fetch_assoc($select_categories_id)) {
                 $cat_id = $row['cat_id'];
                 $cat_title = $row['cat_title'];

                 echo "<td>{$cat_title}</td>";
             }
 */
            echo "<td>$comment_email</td>";
            echo "<td>$comment_status</td>";

            $query ="SELECT * FROM posts WHERE post_id = $comment_post_id";
            $select_post_id_query = mysqli_query($connection, $query);

            confirmQuery($select_post_id_query);

            while($row = mysqli_fetch_assoc($select_post_id_query)){
                $post_id = $row['post_id'];
                $post_title = $row['post_title'];

                echo "<td><a href='../post.php?p_id=$post_id'>$post_title</a></td>";
            }


            echo "<td>$comment_date</td>";
            echo "<td><a href='post_comment.php?approve=$comment_id&id=$the_post_id'>Approve</a></td>";
            echo "<td><a href='post_comment.php?unapprove=$comment_id&id=$the_post_id'>Unapprove</a></td>";
            echo "<td><a href='post_comment.php?delete=$comment_id&id=$the_post_id'>Delete</a></td>";
            echo "</tr>";

        }
        ?>
        </tbody>
    </table>
</div>
<?php
if(isset($_GET['approve'])){
    $the_comment_id = $_GET['approve'];

    $query = "UPDATE comments SET comment_status='approved' WHERE comment_id = {$the_comment_id}";
    $approve_comment_query = mysqli_query($connection, $query);
    header("post_comment.php?id=$the_post_id");
}
?>

<?php
if(isset($_GET['unapprove'])){
    $the_comment_id = $_GET['unapprove'];

    $query = "UPDATE comments SET comment_status='unapproved' WHERE comment_id = {$the_comment_id}";
    $unapprove_comment_query = mysqli_query($connection, $query);
    header("post_comment.php?id=$the_post_id");
}
?>

<?php
if(isset($_GET['delete'])){
    $the_comment_id = $_GET['delete'];

    $query = "DELETE FROM comments WHERE comment_id = {$the_comment_id}";
    $delete_query = mysqli_query($connection, $query);
    header("Location: post_comment.php?id=$the_post_id");
}
?>
</div>
</div>
<!-- /.row -->
</div>
<!-- /.container-fluid -->

</div>
<!-- /#page-wrapper -->

<?php include "includes/admin_footer.php"; ?>

