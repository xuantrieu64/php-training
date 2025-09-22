<?php
ini_set('session.save_handler', 'redis');
ini_set('session.save_path', 'tcp://redis:6379');
session_start();

// Server-side check login
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

require_once 'models/UserModel.php';
$userModel = new UserModel();

// Lấy params search nếu có
$params = [];
if (!empty($_GET['keyword'])) {
    $params['keyword'] = $_GET['keyword'];
}

$users = $userModel->getUsers($params);
?>
<!DOCTYPE html>
<html>

<head>
    <title>List Users</title>
    <?php include 'views/meta.php' ?>
</head>

<body>
<?php include 'views/header.php' ?>

<div class="container">

    <!-- Client-side token check -->
    <script>
        const token = localStorage.getItem('userToken');
        const username = localStorage.getItem('username');

        if (!token) {
            window.location.href = 'login.php';
        }
    </script>

    <div id="welcome" class="alert alert-info" role="alert" style="margin-top:15px;">
        Xin chào <span id="usernameDisplay"></span>!
    </div>

    <button id="logoutBtn" class="btn btn-danger" style="margin-bottom:15px;">Logout</button>

    <?php if (!empty($users)) { ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Username</th>
                    <th scope="col">Fullname</th>
                    <th scope="col">Type</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) { ?>
                    <tr>
                        <th scope="row"><?php echo htmlspecialchars($user['id']) ?></th>
                        <td><?php echo htmlspecialchars($user['name']) ?></td>
                        <td><?php echo htmlspecialchars($user['fullname']) ?></td>
                        <td><?php echo htmlspecialchars($user['type']) ?></td>
                        <td>
                            <a href="form_user.php?id=<?php echo urlencode($user['id']) ?>" title="Update">
                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                            </a>
                            <a href="view_user.php?id=<?php echo urlencode($user['id']) ?>" title="View">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </a>
                            <a href="delete_user.php?id=<?php echo urlencode($user['id']) ?>" title="Delete">
                                <i class="fa fa-eraser" aria-hidden="true"></i>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <div class="alert alert-dark" role="alert">
            Không có user nào!
        </div>
    <?php } ?>
</div>

<script>
    // Hiển thị username từ LocalStorage
    const usernameDisplay = document.getElementById('usernameDisplay');
    usernameDisplay.innerText = localStorage.getItem('username') || '';

    // Logout: xóa token + username, redirect về login
    document.getElementById('logoutBtn').addEventListener('click', () => {
        localStorage.removeItem('userToken');
        localStorage.removeItem('username');
        window.location.href = 'login.php';
    });
</script>

</body>
</html>
