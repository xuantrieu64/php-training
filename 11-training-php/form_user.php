<?php
ini_set('session.save_handler', 'redis');
ini_set('session.save_path', 'tcp://redis:6379');
session_start();

require_once 'models/UserModel.php';
$userModel = new UserModel();

// === Server-side check: phải login mới truy cập ===
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

// Lấy user theo id nếu có
$user = null;
$_id = null;
if (!empty($_GET['id'])) {
    $_id = $_GET['id'];
    $user = $userModel->findUserById($_id); // mảng user
}

// Xử lý submit form
if (!empty($_POST['submit'])) {
    if (!empty($_id)) {
        $userModel->updateUser($_POST);
    } else {
        $userModel->insertUser($_POST);
    }
    header('location: list_users.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Form</title>
    <?php include 'views/meta.php' ?>
</head>
<body>
<?php include 'views/header.php' ?>

<div class="container">

    <!-- Client-side check token LocalStorage -->
    <script>
        const token = localStorage.getItem('userToken');
        if (!token) {
            alert('Bạn cần login trước!');
            window.location.href = 'login.php';
        }
    </script>

    <?php if ($user || !isset($_id)) { ?>
        <div class="alert alert-warning" role="alert">
            User form
        </div>

        <form method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($_id) ?>">

            <div class="form-group">
                <label for="name">Name</label>
                <input class="form-control" name="name" placeholder="Name"
                       value="<?php echo !empty($user[0]['name']) ? htmlspecialchars($user[0]['name']) : '' ?>">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Password">
            </div>

            <button type="submit" name="submit" value="submit" class="btn btn-primary">Submit</button>
        </form>

    <?php } else { ?>
        <div class="alert alert-danger" role="alert">
            User not found!
        </div>
    <?php } ?>

</div>
</body>
</html>
