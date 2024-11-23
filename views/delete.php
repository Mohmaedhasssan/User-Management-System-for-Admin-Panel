<?php
require '../controllers/UserAdapter.php';
session_start();

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$userAdapter = new UserAdapter();

// Fetch existing user data if deleting
$user = null;
if ($user_id > 0) {
    $user = $userAdapter->getUser($user_id);
}

// Form Submission
if ($_SERVER['REQUEST_METHOD'] == "POST" && $user_id > 0) {
    if ($userAdapter->deleteUser($user_id)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error: Unable to delete user.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin :: Delete User</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="container">
        <?php if ($user): ?>
            <h1>Delete User</h1>
            <p>Are you sure you want to delete the user <strong><?= $user['name'] ?></strong>?</p>
            <form method="post">
                <input type="submit" value="Delete User">
            </form>
        <?php else: ?>
            <p>User not found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
