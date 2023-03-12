<?php

require_once('./helper/db.php'); ////import db

$errors = [];
$succses = false;

function request($field)
{
  return isset($_REQUEST[$field]) && $_REQUEST[$field] != "" ? trim($_REQUEST[$field]) : null;
} /////this is a named_function for check value///field=email and password///we should used $_REQUEST cause get all types (post & get)

function has_error($field)
{
  global $errors;
  return isset($errors[$field]);
} /////this is a function for check error ///

function get_error($field)
{
  global $errors;

  return has_error($field) ? $errors[$field] : null;
}
// /after click on submit */
if (isset($_REQUEST['submit'])) {
  $email = request('email'); /////get email
  $password = request('password'); ////get password
  // die($email . $password);

  if (empty($email)) {
    $errors['email'] = 'can not empty';
  }
  if (empty($password)) {
    $errors['password'] = 'can not empty';
  } elseif (strlen($password) < 6) { /////this is rull about password characters
    $errors['password'] = 'can not lower than six character';
  }


  /**INSERT QUERY*/
  if (!empty($email) && !empty($password) && strlen($password) >= 6) {
    $inputPass =  md5($password); ////in this line we want to hashPass
    $insertquery = $link->prepare("INSERT INTO users (email , password) values (? , ?)"); ///I want import sql query(Insert into users) 
    $insertquery->bind_param("ss", $email, $inputPass); ////bind sql query
    $result = $insertquery->execute();
  }
}

// / DELETE QUERY*/
if (isset($_REQUEST['delete-user'])) { ///after click on button delete user
  $userId = intval(request('user-id')); /////restore user id
  $deletequery = $link->prepare("DELETE FROM users WHERE id=?");
  // die(var_dump($deletequery));
  $deletequery->bind_param("d", $userId);
  $deleteResult = $deletequery->execute();
}

/**fetch users */
$selectquery = "select * from users ORDER by id DESC ";

/**UPDATE QUERY */
$operationType = '';
if (isset($_REQUEST['operaton']) && $_REQUEST['operaton']) {
  $operationType = $_REQUEST['operaton'];
  $userId = $_REQUEST['id'];
  $stmt = mysqli_prepare($link, "select * from users where id= ?");

  mysqli_stmt_bind_param($stmt, 'i', $userId);

  mysqli_stmt_execute($stmt);

  $result = mysqli_stmt_get_result($stmt);

  if ($result->num_rows == 0) {

    header("location: ./pro.php");;
  }

  $user = mysqli_fetch_assoc($result);
}
if (isset($_REQUEST['update'])) {
  $email = request('email'); /////get email
  $password = request('password'); ////get password
  $userId = request('id');
  // die($user);

  if (empty($email)) {
    $errors['email'] = 'can not empty';
  }
  if (empty($password)) {
    $errors['password'] = 'can not empty';
  } elseif (strlen($password) < 6) { /////this is rull about password characters
    $errors['password'] = 'can not lower than six character';
  }
  $stmt = mysqli_prepare($link, "update users set email= ? , password= ? where id= ?");

  mysqli_stmt_bind_param($stmt, 'ssi', $email, $password, $userId);

  mysqli_stmt_execute($stmt);

  if (mysqli_affected_rows($link)) { /////ایا در جدول ما تغغیر بوجود آمده
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
    <p>Please enter email & password :</p>
    <?php if ($operationType == '') { ?>
      <form action="pro.php" method="post">
        <div class="row">
          <div class="col">
            <input type="email" class="form-control" id="email" placeholder="Enter email" name="email">
            <?php if (has_error('email')) { ?>
              <span><?php echo get_error('email'); ?></span><br>
            <?php } ?>
          </div>
          <div class="col">
            <input type="password" class="form-control" placeholder="Enter password" name="password">
            <?php if (has_error('password')) { ?>
              <span><?php echo get_error('password'); ?></span><br>
            <?php } ?>
          </div>
        </div>
        <button name="submit" type="submit" class="btn btn-primary mt-3">register</button>
      </form>
    <?php } ?>

    <br>
    <?php if ($operationType == 'edit') { ?>

      <form action="pro.php?id=<?= $user['id']; ?>" method="post">
        <div class="row">

          <div class="col">
            <input type="email" class="form-control" id="email" placeholder="Enter email" name="email" value="<?= $user['email'] ?>">
          </div>
          <div class="col">
            <input type="password" class="form-control" placeholder="Enter password" name="password" value="<?= $user['password'] ?>">
          </div>
        </div>
        <div class="col">
          <button name="update" type="submit" class="btn btn-primary mt-3">edit data</button>
        </div>
  </div>
  </form>
<?php } ?>
</div>
<br>


<br>

<div class="container mt-3">
  <h2>data from Database</h2>
  <br>
  <?= (isset($deleteResult) && $deleteResult) ?  'deleted' : ":/";  ?>

  <?php if ($result = mysqli_query($link, $selectquery)) { ?>

    <table class="table table-bordered">
      <thead>
        <tr>
          <th>password</th>
          <th>Email</th>
          <th>Del</th>
          <th>Upd</th>

        </tr>

      </thead>
      <tbody>

        <?php while ($user = mysqli_fetch_assoc($result)) { ?>
          <tr>
            <td><?= $user['password'] ?></td>
            <td><?= $user['email'] ?></td>
            <td>
              <form method="post" action="./pro.php">
                <input type="hidden" name="user-id" value="<?= $user['id'] ?>">
                <button type="submit" class="btn btn-danger" name='delete-user'>Delete</button>
              </form>
            </td>
            <td>
              <form method="post" action="./pro.php" name="Update-user">
                <input type="hidden" name="user-id" value="<?= $user['id'] ?>">
                <!-- <button type="submit" class="btn btn-success">Update</button>                       -->
                <a href="pro.php?operaton=edit&id=<?= $user['id'] ?>" class="btn btn-success">edit</a>

              </form>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  <?php } else { ?>
    <div class="alert alert-warning">No data!!!</div>
  <?php } ?>

  </form>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.3/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>