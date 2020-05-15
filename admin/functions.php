<?php
function escape($string) {
    global $connection;
    return mysqli_real_escape_string($connection, trim(strip_tags($string)));
}
/**is add_post atsiunciamas patikrinimas
 * @param $result
 */
function confirmQuery($result): void
{
    global $connection;
    if(!$result){
        die('QUERY FAILED' . mysqli_error($connection));
    }
}

function insertCategories(): void
{
    global $connection;
    if (isset($_POST['submit'])) {
        $cat_title = $_POST['cat_title'];
        if ($cat_title == "" || empty($cat_title)) {
            echo "This field should not be empty";
        } else {
            $query = "INSERT INTO categories (cat_title) VALUE ('{$cat_title}')";

            $create_category_query = mysqli_query($connection, $query);

            if (!$create_category_query) {
                die('Query sent failed' . mysqli_error());
            }
        }
    }
}

function findAllCategories(): void
{
    global $connection;

    $query = "SELECT * FROM categories";
    $select_categories = mysqli_query($connection, $query);

    while ($row = mysqli_fetch_assoc($select_categories)) {
        $cat_id = $row['cat_id'];
        $cat_title = $row['cat_title'];
        echo "<tr>";
        echo "<td>{$cat_id}</td>";
        echo "<td>{$cat_title}</td>";
        echo "<td><a href='categories.php?delete={$cat_id}' >DELETE</a></td>";
        echo "<td><a href='categories.php?edit={$cat_id}' >EDIT</a></td>";
        echo "</tr>";
    }
}

function deleteCategories(): void
{
    global $connection;
    if (isset($_GET['delete'])) {
        $the_cat_id = $_GET['delete'];
        $query = "DELETE FROM categories WHERE cat_id = {$the_cat_id}";
        $delete_query = mysqli_query($connection, $query);
        header("Location: categories.php");
    }
}
function usersOnline(): void
{
    global $connection;
    if(isset($_GET['onlineusers'])) {


        if(!$connection) {
            session_start();
            include("../includes/db.php");
            $session = session_id();
            $time = time();
            $time_out_in_seconds = 5;
            $time_out = $time - $time_out_in_seconds;

            $query = "SELECT * FROM users_online WHERE session = '$session'";
            $send_query = mysqli_query($connection, $query);
            $count = mysqli_num_rows($send_query);

                if ($count == NULL) {
                    mysqli_query($connection, "INSERT INTO users_online(session, time) VALUES ('$session', '$time')");
                } else {
                    mysqli_query($connection, "UPDATE users_online SET time = '$time' WHERE session = '$session'");
                }
            $users_online_query = mysqli_query($connection, "SELECT * FROM users_online WHERE time > '$time_out'");
            $count_user = mysqli_num_rows($users_online_query);
            echo $count_user;
        }

    }
}
usersOnline();

function recordCount($table): int
{
    global $connection;
    $query="SELECT * FROM " . $table;
    $count_all = mysqli_query($connection, $query);
    $result = mysqli_num_rows($count_all);
    confirmQuery($result);
    return $result;
}

function  checkStatus($table, $column, $status)
{
    global $connection;
    $query="SELECT * FROM $table WHERE $column = '$status'";
    $select_to_check_status = mysqli_query($connection, $query);
    $result =  mysqli_num_rows($select_to_check_status);
    confirmQuery($result);
    return $result;
}
/*$post_published_count = checkStatus('posts', 'post_status', 'published');

$post_draft_count = checkStatus('posts', 'post_status', 'draft');

$unapproved_comment_count = checkStatus('comments', 'comment_status', 'unapproved');*/