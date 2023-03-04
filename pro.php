<?php
$selectquery = "select * from users ORDER by id DESC ";

function request($field) {
    return isset($_REQUEST[$field]) && $_REQUEST[$field] != "" ? trim($_REQUEST[$field]) : null;
}

function has_error($field) {
    global $errors;

    return isset($errors[$field]);
}

function get_error($field) {
    global $errors;

    return has_error($field) ? $errors[$field] : null;
}

$errors = [];
$succses=false;


if(isset($_REQUEST['submit'])){
    $email=request('email');
    $password=request('password');
    // die($email . $password);


    if(empty($email)){
        $errors['email']='can not empty';
    }

    if(empty($password)){
     $errors['password']='can not empty';

    }elseif(strlen($password) < 6){/////this is rull about characters
    $errors['password']='can not lower than six character';
    }
    }

    if(!empty($email) && !empty($password) && strlen($password)>=6){
        $link=mysqli_connect('localhost:3306','root','');

            if(! $link){
                echo 'could not connect: ' . mysqli_connect_error();
                exit;
            }

            mysqli_select_db($link ,'zarindb');

            // $selectquery = "select * from users ORDER by id DESC ";

            $insertquery = $link->prepare("INSERT INTO users (email , password) values (? , ?)");
            $insertquery ->bind_param("ss", $email,$password);////bind sql query

            $result=$insertquery->execute();

            if( $result = mysqli_query($link , $selectquery) ) {
            } else {
                echo 'error : ' . mysqli_error($link);
                exit;
            }


}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <title> Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="./static/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

<div class="container">
  <h2>Example</h2>
  <p>Please enter email & password :</p>
  <form action="pro.php" method="post">
    <div class="row">
      <div class="col">
        <input type="email" class="form-control" id="email" placeholder="Enter email" name="email" >
        <?php if(has_error('email')) { ?>
            <span><?php echo get_error('email'); ?></span><br>
        <?php } ?>
      </div>
      <div class="col">
        <input type="password" class="form-control" placeholder="Enter password" name="password">
        <?php if(has_error('password')) { ?>
            <span><?php echo get_error('password'); ?></span><br>
        <?php } ?>
      </div>
    </div>
    <button name="submit" type="submit" class="btn btn-primary mt-3">register</button>
  </form>
</div>

<br>

<div class="container mt-3">
  <h2>data from Database</h2>
  <br>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>password</th>
        <th>Email</th>
      </tr>
    </thead>
    <tbody>

    <?php while ($user = mysqli_fetch_assoc($result) ) { ?>
                    <tr>
                    <td><?= $user['password'] ?></td>
                        <td><?= $user['email'] ?></td>
                        
                    </tr>
                    <?php } ?>
    </tbody>
  </table>
</div>
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.3/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>