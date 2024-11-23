<?php
require '../controllers/UserAdapter.php';
session_start();

$error_fields = array();
// Validation
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
        $userAdapter = new UserAdapter();

        // Escape special characters to avoid SQL injection
        $name = $_POST["name"];
        $email = $_POST["email"];
        $password = sha1($_POST["password"]);
        $admin = isset($_POST["admin"]) && filter_input(INPUT_POST, "admin", FILTER_VALIDATE_BOOLEAN);
        
        // Check if file upload is provided
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['profile_picture']['tmp_name'];
            $file_name = $_FILES['profile_picture']['name'];
            $file_size = $_FILES['profile_picture']['size'];
            $file_type = $_FILES['profile_picture']['type'];

            // Specify the directory to save uploaded files
            $upload_dir = "uploads/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Move the file to the server
            $file_path = $upload_dir . basename($name . $file_name);
            move_uploaded_file($file_tmp, $file_path);
        } else {
            $file_path = null; // No file uploaded
        }

        // Prepare data for insertion
        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'admin' => $admin,
            'profile_picture' => $file_path
        ];

        // Insert data into the database
        if ($userAdapter->addUser($data)) {
            header("Location: index.php");
            exit;
        } else {
            echo "Error: Unable to add user.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin :: Add User</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Add User</h1>
        <form method="post" enctype="multipart/form-data">
            <label for="Name">Name</label>
            <input type="text" name="name" id="" value="<?= (isset($_POST['name'])) ? $_POST['name'] : '' ?>">
            <?php if (in_array("name", $error_fields)) echo "*Please Enter Your Name" ?>
            <br>

            <label for="email">Email</label>
            <input type="email" name="email" id="" value="<?= (isset($_POST['email'])) ? $_POST['email'] : '' ?>">
            <?php if (in_array("email", $error_fields)) echo "*Please Enter Your Email Correctly" ?>
            <br>

            <label for="password">Password</label>
            <input type="password" name="password" id="" value="<?= (isset($_POST['password'])) ? $_POST['password'] : '' ?>">
            <?php if (in_array("password", $error_fields)) echo "*Make sure your password is >5 letters/numbers" ?>
            <br>

            <label for="profile_picture">Profile Picture</label>
            <input type="file" name="profile_picture" id="">
            <br>

            <input type="checkbox" name="admin" id="" <?= (isset($_POST['admin'])) ? 'checked' : '' ?>>Admin
            <br>

            <input type="submit" value="Add User">
        </form>
    </div>
</body>
</html>
