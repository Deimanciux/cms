<?php
if(isset($_POST['create_user'])){
    $user_firstname =$_POST['user_firstname'];
    $user_lastname =$_POST['user_lastname'];
    $user_role =$_POST['user_role'];

    /*$username = $_FILES['image']['name'];
    $username =  $_FILES['image']['tmp_name'];*/

    $username =$_POST['username'];
    $user_email =$_POST['user_email'];
    $user_password =$_POST['user_password'];

    $user_password = password_hash($user_password, PASSWORD_BCRYPT, array('cost' => 10));

    $user_firstname = mysqli_real_escape_string($connection, $user_firstname);
    $user_lastname = mysqli_real_escape_string($connection, $user_lastname);
    $user_role = mysqli_real_escape_string($connection, $user_role);
    $username = mysqli_real_escape_string($connection, $username);
    $user_email = mysqli_real_escape_string($connection, $user_email);
    $user_password = mysqli_real_escape_string($connection, $user_password);

   /* $post_date = date('d, m, y');*/
   /* $query = "SELECT userSalt FROM users";
    $select_usersalt_query = mysqli_query($connection, $query);

    if (!$select_usersalt_query) {
        die("Query Failed" . mysqli_error($connection));
    }
    $row = mysqli_fetch_array($select_usersalt_query);
    $salt = $row['userSalt'];
    $user_password = crypt($user_password, $salt);*/

    /*move_uploaded_file($post_image_temp, "../images/$post_image");*/
    $query="INSERT INTO users(user_firstname, user_lastname, user_role, username, user_email, user_password) ";
    $query.="VALUES('{$user_firstname}', '{$user_lastname}', '{$user_role}', '{$username}', '{$user_email}', '{$user_password}')";
    $create_user_query = mysqli_query($connection, $query);

    confirmQuery($create_user_query);

    echo "User Created: " . " " . "<a href='users.php'>View Users</a> ";
}
?>
<form action="" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label for="firstname">Firstname</label>
        <input type="text" class="form-control" name="user_firstname">
    </div>
    <div class="form-group">
        <label for="lastname">Lastname</label>
        <input type="text" class="form-control" name="user_lastname">
    </div>
    <div class="form-group">
        <label for="user_role">Role</label>
        <select name="user_role" id="user_role">
            <option value='subscriber'>Select options</option>";
            <option value='admin'>admin</option>";
            <option value='subscriber'>subscriber</option>";
        </select>
    </div>
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" class="form-control" name="username">
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" name="user_email">
    </div>
   <!--<div class="form-group">
        <label for="post_image">Post Image</label>
        <input type="file" name="image">
    </div>-->
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control" name="user_password">
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary"  name="create_user"  value="Add User">
    </div>
</form>