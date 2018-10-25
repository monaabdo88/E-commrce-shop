<?php
session_start();
if(isset($_SESSION['username'])){
    header('Location: dashboard.php');
}
$noNavbar = '';
$pageTitle = 'Login';
include "init.php";
?>
    <div class="container login-page">
        <h1 class="text-center login-head">
            <span class="selected" data-class="login">Login</span> |
            <span data-class="signup">Signup</span>
        </h1>
        <?php
        // Check if user coming from http request
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $username = $_POST['user'];
            $password = $_POST['pass'];
            $hashpass = sha1($password);

            //check if user exist in database
            $stmt = $con->prepare('SELECT UserID,Username,Password FROM users WHERE Username = ? AND Password = ? AND GroupID = 1 LIMIT 1');
            $stmt->execute(array($username,$hashpass));
            $row = $stmt->fetch();
            $count = $stmt->rowCount();
            //check if count great than 0 start the session
            if($count > 0){
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $row['UserID'];
                header('Location: index.php');
                exit();
            }else{
                echo '<div class="alert alert-danger col-md-offset-3 col-md-6"><button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                      </button><p class="text-center">Wrong Username Or Password Please Try Again</p></div>';
            }
        }
        ?>
        <!------ Login Form ------>
        <form action="<?=$_SERVER['PHP_SELF']?>" method="post" class="login" id="form_validate">
            <input type="text" name="user" id="username" placeholder="Username" autocomplete="off" class="form-control">
            <input type="password" name="pass" id="password" placeholder="Password" autocomplete="off" class="form-control">
            <input type="submit" class="btn btn-primary btn-block" value="Login">
        </form>
    </div>
<?php include $tpl.'footer.php'; ?>