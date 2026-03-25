<?php
include 'config/koneksi.php';

if(isset($_POST['email'])){
    $email = $_POST['email'];

    $query = mysqli_query($conn,"SELECT * FROM users WHERE email='$email'");

    if(mysqli_num_rows($query) > 0){
        echo "email_ada";
    }else{
        echo "email_ok";
    }
}

if(isset($_POST['username'])){
    $username = $_POST['username'];

    $query = mysqli_query($conn,"SELECT * FROM users WHERE username='$username'");

    if(mysqli_num_rows($query) > 0){
        echo "username_ada";
    }else{
        echo "username_ok";
    }
}
?>