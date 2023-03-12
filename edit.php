<?php 
require_once('./helper/db.php');////import db

if( ! isset($_GET['id'])){
    // header("location: ./edit.php");
    return;
}
$id=(int)$_GET['id'];

$stmt =mysqli_prepare($link ,"select * from users where id= ?");

mysqli_stmt_bind_param($stmt , 'i' , $id);

mysqli_stmt_execute($stmt);

$result=mysqli_stmt_get_result($stmt);

if($result -> num_rows==0){

    header("location: ./edit.php");;
}

/////get user
$user=mysqli_fetch_assoc($result);



if($_SERVER['REQUEST_METHOD']=="POST" && !is_null($user)){
  
    
    $stmt =mysqli_prepare($link ,"update users set email= ? , password= ? where id= ?");
    
    mysqli_stmt_bind_param($stmt , 'ssi' , $_POST['email'] , $_POST['password'], $user['id']);
    
    mysqli_stmt_execute($stmt);
    
    if(mysqli_affected_rows($link)){/////ایا در جدول ما تغغیر بوجود آمده
        header("location: ./pro.php");
        return;
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
  <p>Please change email & password :</p>


<form action="edit.php?id=<?=$user['id']?>" method="post">
    <div class="row">

    <div class="col">
    <input type="email" class="form-control" id="email" placeholder="Enter email" name="email" value="<?=$user['email']?>" > 
    </div>
    <div class="col">
        <input type="password" class="form-control" placeholder="Enter password" name="password" value="<?=$user['password']?>">
    </div>    
    </div>
    <div class="col">
        <button name="submit"  type="submit" class="btn btn-primary mt-3" >edit data</button>
    </div>
    </div>
    
</form>
</div>
</body>
</html>



