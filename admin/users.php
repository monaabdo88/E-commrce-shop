<?php
ob_start();
session_start();
if(!isset($_SESSION['username'])){
    header('Location: index.php');
    exit();
}
$pageTitle = "Users";
include "init.php";
$do = (isset($_GET['do'])) ? $_GET['do'] : 'Manage';
if($do == 'Manage') {
    $query = '';
    if(isset($_GET['page']) && $_GET['page'] == 'Pending'){
        $query = 'AND RegStatus = 0';
    }
    // Select All Users Except Admin
    $stmt = $con->prepare("SELECT * FROM users WHERE GroupID != 1 $query ORDER BY UserID DESC");
    $stmt->execute();
    $data = $stmt->fetchAll();
    if(!empty($data)) {
        ?>
        <h1 class="text-center">Manage users</h1>
        <div class="container">
            <div class="table-responsive">
                <table class="main-table manage-users text-center table table-bordered">
                    <tr>
                        <td>#ID</td>
                        <td>Avatar</td>
                        <td>Username</td>
                        <td>Email</td>
                        <td>Full Name</td>
                        <td>Registered Date</td>
                        <td>Control</td>
                    </tr>
                    <?php
                    foreach ($data as $row){
                        echo "<tr>";
                        echo "<td>" . $row['UserID'] . "</td>";
                        echo "<td>";
                        if (empty($row['avatar'])) {
                            echo 'No Image';
                        } else {
                            echo "<img src='uploads/avatar/" . $row['avatar'] . "' alt='' width='150'/>";
                        }
                        echo "</td>";

                        echo "<td>" . $row['Username'] . "</td>";
                        echo "<td>" . $row['Email'] . "</td>";
                        echo "<td>" . $row['FullName'] . "</td>";
                        echo "<td>" . $row['Date'] ."</td>";
                        echo "<td>
										<a href='users.php?do=Edit&userid=" . $row['UserID'] . "' class='btn btn-success'><i class='fa fa-edit'></i> Edit</a>
										<a href='users.php?do=Delete&userid=" . $row['UserID'] . "' class='btn btn-danger confirm'><i class='fa fa-close'></i> Delete </a>";
                        if ($row['RegStatus'] == 0) {
                            echo "<a 
													href='users.php?do=Activate&userid=" . $row['UserID'] . "' 
													class='btn btn-info activate'>
													<i class='fa fa-check'></i> Activate</a>";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
            </div>
            <a href="users.php?do=Add" class="btn btn-primary">
                <i class="fa fa-plus"></i> New Member
            </a>
        </div>
        <?php
    }else{
        echo '<div class="container">';
        echo '<div class="nice-message">There\'s No users To Show</div>';
        echo '<a href="users.php?do=Add" class="btn btn-primary">
							<i class="fa fa-plus"></i> New Member
						</a>';
        echo '</div>';
    }
}
// Edit Section
elseif($do == 'Edit'){
    $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;
    $stmt = $con->prepare("SELECT * FROM users WHERE UserID = ? LIMIT 1");
        $stmt->execute(array($userid));
        $row = $stmt->fetch();
        $userCount = $stmt->rowCount();
if($userCount > 0) {
    ?>
    <h1 class="text-center">Edit Member</h1>
    <div class="container">
        <form class="form-horizontal" action="?do=Update" method="POST">
            <input type="hidden" name="userid" value="<?php echo $userid ?>"/>
            <!-- Start Username Field -->
            <div class="form-group form-group-lg">
                <label class="col-sm-2 control-label">Username</label>
                <div class="col-sm-10 col-md-6">
                    <input type="text" name="username" class="form-control" value="<?php echo $row['Username'] ?>"
                           autocomplete="off" required="required"/>
                </div>
            </div>
            <!-- End Username Field -->
            <!-- Start Password Field -->
            <div class="form-group form-group-lg">
                <label class="col-sm-2 control-label">Password</label>
                <div class="col-sm-10 col-md-6">
                    <input type="hidden" name="oldpassword" value="<?php echo $row['Password'] ?>"/>
                    <input type="password" name="newpassword" class="form-control" autocomplete="new-password"
                           placeholder="Leave Blank If You Dont Want To Change"/>
                </div>
            </div>
            <!-- End Password Field -->
            <!-- Start Email Field -->
            <div class="form-group form-group-lg">
                <label class="col-sm-2 control-label">Email</label>
                <div class="col-sm-10 col-md-6">
                    <input type="email" name="email" value="<?php echo $row['Email'] ?>" class="form-control"
                           required="required"/>
                </div>
            </div>
            <!-- End Email Field -->
            <!-- Start Full Name Field -->
            <div class="form-group form-group-lg">
                <label class="col-sm-2 control-label">Full Name</label>
                <div class="col-sm-10 col-md-6">
                    <input type="text" name="full" value="<?php echo $row['FullName'] ?>" class="form-control"
                           required="required"/>
                </div>
            </div>
            <!-- End Full Name Field -->
            <!-- Start Submit Field -->
            <div class="form-group form-group-lg">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="submit" value="Save" class="btn btn-primary btn-lg"/>
                </div>
            </div>
            <!-- End Submit Field -->
        </form>
    </div>
    <?php
            }else{
                echo "<div class='container'>";

                $theMsg = '<div class="alert alert-danger">Theres No Such ID</div>';

                redirectHome($theMsg);

                echo "</div>";
            }
        }
        // Update Section Code
        elseif($do == 'Update'){
            echo "<h1 class='text-center'>Update Member</h1>";
            echo "<div class='container'>";
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                // Get Variables From The Form

                $id 	= $_POST['userid'];
                $user 	= $_POST['username'];
                $email 	= $_POST['email'];
                $name 	= $_POST['full'];
                $password = (empty('newpassword')) ? $_POST['oldpassword'] : sha1($_POST['newpassword']);
                //validate the form
                $formErrors = [];

                if (strlen($user) < 4) {
                    $formErrors[] = 'Username Cant Be Less Than <strong>4 Characters</strong>';
                }

                if (strlen($user) > 20) {
                    $formErrors[] = 'Username Cant Be More Than <strong>20 Characters</strong>';
                }

                if (empty($user)) {
                    $formErrors[] = 'Username Cant Be <strong>Empty</strong>';
                }
                if (empty($name)) {
                    $formErrors[] = 'Full Name Cant Be <strong>Empty</strong>';
                }

                if (empty($email)) {
                    $formErrors[] = 'Email Cant Be <strong>Empty</strong>';
                }

                // Loop Into Errors Array And Echo It
                foreach ($formErrors as $error){
                    echo'<div class="alert alert-danger col-md-offset-3 col-md-6"><button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                      </button><p class="text-center">'.$error.'</p></div>';
                }
                if(empty($formErrors)){
                    $stmt2 = $con->prepare('SELECT * FROM users WHERE Username = ? AND UserID != ?');
                    $stmt2->execute(array($user,$id));
                    $count2 = $stmt2->rowCount();
                    if($count2 > 0){
                        $theMsg = '<div class="alert alert-danger col-md-offset-3 col-md-6"><button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                      </button><p class="text-center">Sorry This User Is Exist</p></div>';
                        redirectHome($theMsg,'back');
                    }else{
                        // Update The Database With This Info
                        $stmt = $con->prepare("UPDATE users SET Username = ?, Email = ?, FullName = ?, Password = ? WHERE UserID = ?");

                        $stmt->execute(array($user, $email, $name, $pass, $id));

                        // Echo Success Message

                        $theMsg = "<div class='alert alert-success'><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
                      <span aria-hidden=\"true\">&times;</span>
                      </button><p class=\"text-center\">" . $stmt->rowCount() . ' Record Updated</p></div>';

                        redirectHome($theMsg, 'back');
                    }
                }
            }else{

            }
            echo "</div>";
        }
        //Add Section Form
elseif ($do == 'Add'){
    ?>
    <h1 class="text-center">Add New Member</h1>
    <div class="container">
        <form class="form-horizontal" action="?do=Insert" method="POST" enctype="multipart/form-data">
            <!-- Start Username Field -->
            <div class="form-group form-group-lg">
                <label class="col-sm-2 control-label">Username</label>
                <div class="col-sm-10 col-md-6">
                    <input type="text" name="username" class="form-control" autocomplete="off" required="required" placeholder="Username To Login Into Shop" />
                </div>
            </div>
            <!-- End Username Field -->
            <!-- Start Password Field -->
            <div class="form-group form-group-lg">
                <label class="col-sm-2 control-label">Password</label>
                <div class="col-sm-10 col-md-6">
                    <input type="password" name="password" class="password form-control" required="required" autocomplete="new-password" placeholder="Password Must Be Hard & Complex" />
                    <i class="show-pass fa fa-eye fa-2x"></i>
                </div>
            </div>
            <!-- End Password Field -->
            <!-- Start Email Field -->
            <div class="form-group form-group-lg">
                <label class="col-sm-2 control-label">Email</label>
                <div class="col-sm-10 col-md-6">
                    <input type="email" name="email" class="form-control" required="required" placeholder="Email Must Be Valid" />
                </div>
            </div>
            <!-- End Email Field -->
            <!-- Start Full Name Field -->
            <div class="form-group form-group-lg">
                <label class="col-sm-2 control-label">Full Name</label>
                <div class="col-sm-10 col-md-6">
                    <input type="text" name="full" class="form-control" required="required" placeholder="Full Name Appear In Your Profile Page" />
                </div>
            </div>
            <!-- End Full Name Field -->
            <!-- Start Avatar Field -->
            <div class="form-group form-group-lg">
                <label class="col-sm-2 control-label">User Avatar</label>
                <div class="col-sm-10 col-md-6">
                    <input type="file" name="avatar" class="form-control" required="required" />
                </div>
            </div>
            <!-- End Avatar Field -->
            <!-- Start Submit Field -->
            <div class="form-group form-group-lg">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="submit" value="Add Member" class="btn btn-primary btn-lg" />
                </div>
            </div>
            <!-- End Submit Field -->
        </form>
    </div>

    <?php
}
// Insert code section
elseif($do == 'Insert')
{
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        echo "<h1 class='text-center'>Insert Member</h1>";
        echo "<div class='container'>";
        // Upload Variables
        $imgName = $_FILES['avatar']['name'];
        $imgSize = $_FILES['avatar']['size'];
        $imgTmp = $_FILES['avatar']['tmp_name'];
        $imgType = $_FILES['avatar']['type'];
        // List Of Allowed File Typed To Upload
        $imgAllowType = array("jpeg","jpg","png","gif");
        // get image Extension
        $ext = explode('.', $imgName);
        $imgExtension = strtolower(end($ext));
        // get variables from form
        $user 	= $_POST['username'];
        $pass 	= $_POST['password'];
        $email 	= $_POST['email'];
        $name 	= $_POST['full'];
        $hashPass = sha1($_POST['password']);
        //validate the form
        $formErrors = [];

            if (strlen($user) < 4) {
                $formErrors[] = 'Username Cant Be Less Than <strong>4 Characters</strong>';
            }

            if (strlen($user) > 20) {
                $formErrors[] = 'Username Cant Be More Than <strong>20 Characters</strong>';
            }

            if (empty($user)) {
                $formErrors[] = 'Username Cant Be <strong>Empty</strong>';
            }

            if (empty($pass)) {
                $formErrors[] = 'Password Cant Be <strong>Empty</strong>';
            }

            if (empty($name)) {
                $formErrors[] = 'Full Name Cant Be <strong>Empty</strong>';
            }

            if (empty($email)) {
                $formErrors[] = 'Email Cant Be <strong>Empty</strong>';
            }

            if (! empty($imgName) && ! in_array($imgExtension, $imgAllowType)) {
                $formErrors[] = 'This Extension Is Not <strong>Allowed</strong>';
            }

            if (empty($imgName)) {
                $formErrors[] = 'Avatar Is <strong>Required</strong>';
            }

            if ($imgSize > 4194304) {
                $formErrors[] = 'Avatar Cant Be Larger Than <strong>4MB</strong>';
            }

            // Loop Into Errors Array And Echo It
            foreach ($formErrors as $error){
                echo'<div class="alert alert-danger col-md-offset-3 col-md-6"><button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                      </button><p class="text-center">'.$error.'</p></div>';
            }
        // Check If There's No Error Proceed The Update Operation
        if(empty($formErrors)){
                $img = rand(0,9999).'-'.$imgName;
                move_uploaded_file($imgTmp,'uploads\avatar\\'.$img);
            // Check If User Exist in Database
            $checkuser = checkItem('Username','users',$user);
            if($checkuser == 1){
                $theMsg = '<div class="alert alert-danger">Sorry This User Is Exist</div>';

                redirectHome($theMsg, 'back');
            }else{
                //insert new user in database
                $stmt = $con->prepare('INSERT INTO users (Username, Password, Email, FullName, RegStatus, Date, avatar) 
                                      VALUES (:user,:pass,:email,:fullname,1,now(),:img)');
                $stmt->execute(array(
                        'user'=> $user,
                        'pass' => $hashPass,
                        'email' => $email,
                        'fullname'=> $name,
                        'img' => $img
                ));
                // Echo Success Message

                $theMsg = "<div class='alert alert-success'><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
                      <span aria-hidden=\"true\">&times;</span>
                      </button><p class='text-center'>" . $stmt->rowCount() . ' Record Inserted</p></div>';

                redirectHome($theMsg, 'back');
            }

        }
        echo "</div>";
    }else{
        echo "<div class='container'>";

        $theMsg = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                      </button><p class=\'text-center\'>Sorry You Cant Browse This Page Directly</p></div>';

        redirectHome($theMsg);

        echo "</div>";

    }
}elseif($do == 'Delete'){
    echo "<h1 class='text-center'>Delete Member</h1>";
    echo "<div class='container'>";

    // Check If Get Request userid Is Numeric & Get The Integer Value Of It

    $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;

    // Select All Data Depend On This ID

    $check = checkItem('userid', 'users', $userid);

    // If There's Such ID Show The Form

    if ($check > 0) {

        $stmt = $con->prepare("DELETE FROM users WHERE UserID = :zuser");

        $stmt->bindParam(":zuser", $userid);

        $stmt->execute();

        $theMsg = "<div class='alert alert-success'><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
                      <span aria-hidden=\"true\">&times;</span>
                      </button><p class='text-center'>" . $stmt->rowCount() . ' Record Deleted</p></div>';

        redirectHome($theMsg, 'back');

    } else {

        $theMsg = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                      </button><p class=\'text-center\'>This ID is Not Exist</p></div>';

        redirectHome($theMsg);

    }

    echo '</div>';
}elseif($do == 'Activate'){

    echo "<h1 class='text-center'>Activate Member</h1>";
    echo "<div class='container'>";

    // Check If Get Request userid Is Numeric & Get The Integer Value Of It

    $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;

    // Select All Data Depend On This ID

    $check = checkItem('userid', 'users', $userid);

    // If There's Such ID Show The Form

    if ($check > 0) {

        $stmt = $con->prepare("UPDATE users SET RegStatus = 1 WHERE UserID = ?");

        $stmt->execute(array($userid));

        $theMsg = "<div class='alert alert-success'><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
                      <span aria-hidden=\"true\">&times;</span>
                      </button><p class='text-center'>" . $stmt->rowCount() . ' Record Updated</p></div>';

        redirectHome($theMsg);

    } else {

        $theMsg = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                      </button><p class=\'text-center\'>This ID is Not Exist</p></div>';

        redirectHome($theMsg);

    }

    echo '</div>';
}
?>
<?php include $tpl."footer.php"?>
