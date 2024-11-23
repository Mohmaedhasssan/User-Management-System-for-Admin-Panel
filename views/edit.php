<?php
require '../controllers/UserAdapter.php';
session_start();

$error_fields = array();
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$userAdapter = new UserAdapter();

// Fetch existing user data if editing
$user = null;
if ($user_id > 0) {
    $user = $userAdapter->getUser($user_id);
}

// Form Submission
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (!(isset($_POST['name']) && !empty($_POST['name']))) {
        $error_fields[] = 'name';
    }
    if (!(isset($_POST['email']) && filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL))) {
        $error_fields[] = 'email';
    }
    if (!(isset($_POST['password']) && strlen(trim($_POST['password'])) > 5)) {
        $error_fields[] = 'password';
    }

    if (!$error_fields) {
        // Escape special characters to avoid SQL injection
        $name = $_POST["name"];
        $email = $_POST["email"];
        $password = !empty($_POST["password"]) ? sha1($_POST["password"]) : $user['password'];
        $admin = isset($_POST["admin"]) ? 1 : 0;

        // Prepare data for insertion or update
         $data [] = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'admin' => $admin
        ];

        // Update user data in the database
        if ($user_id > 0) {
            $result = $userAdapter->updateUser($user_id, $data);
        } else {
            $result = $userAdapter->addUser($data);
        }

        if ($result) {
            header("Location: index.php");
            exit;
        } else {
            echo "Error: Unable to save user data.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin :: <?= $user_id > 0 ? 'Edit User' : 'Add User' ?></title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1><?= $user_id > 0 ? 'Edit User' : 'Add User' ?></h1>
        <form method="post">
            <label for="Name">Name</label>
            <input type="text" name="name" id="" value="<?= isset($_POST['name']) ? $_POST['name'] : ($user['name'] ?? '') ?>">
            <?php if (in_array("name", $error_fields)) echo "*Please Enter Your Name" ?>
            <br>

            <label for="email">Email</label>
            <input type="email" name="email" id="" value="<?= isset($_POST['email']) ? $_POST['email'] : ($user['email'] ?? '') ?>">
            <?php if (in_array("email", $error_fields)) echo "*Please Enter Your Email Correctly" ?>
            <br>

            <label for="password">Password</label>
            <input type="password" name="password" id="" value="">
            <?php if (in_array("password", $error_fields)) echo "*Make sure your password is >5 letters/numbers" ?>
            <br>

            <input type="checkbox" name="admin" value="1" id="" <?= isset($_POST['admin']) || (!isset($_POST['admin']) && $user['admin']) ? 'checked' : '' ?>> Admin
            <br>

            <input type="submit" value="<?= $user_id > 0 ? 'Update User' : 'Add User' ?>">
        </form>
    </div>
</body>
</html>
