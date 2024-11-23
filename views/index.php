<?php
require '../controllers/UserAdapter.php';
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$user = new UserAdapter();
$users = isset($_GET['search']) ? $user->searchUsers($_GET['search']) : $user->getUsers(); 

?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin :: List Users</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body>
    <div class="container">
        <div class="welcome">
            Welcome, <?php echo $_SESSION['email']; ?> | <a href='logout.php'>Logout</a>
        </div>
        <div class="search">
            <h1>List Users</h1>
            <form method="GET">
                <input type="text" name="search" placeholder="Search by name or email">
                <input type="submit" value="Search">
            </form>
        </div>
        <div class="table-section">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Admin</th>
                    <th>Avatar</th>
                    <th>Action</th>
                </tr>
                <?php
                foreach ($users as $row) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["name"] . "</td>";
                    echo "<td>" . $row["email"] . "</td>";
                    echo "<td>" . (($row["admin"]) ? "YES" : "NO") . "</td>";
                    if (!empty($row["profile_picture"])) {
                        echo " <td> <img src='../" . $row["profile_picture"] . "' alt='Profile Picture'></td>";
                    } else {
                        echo "<td> No profile picture available.</td>";
                    }
                    echo <<<HTML
                        <td>
                            <a href='edit.php?id={$row["id"]}'>Edit</a> | 
                            <a href='delete.php?id={$row["id"]}'>Delete</a>
                        </td>
                    HTML;
                    echo "</tr>";
                }
                ?>
                <tr>
                    <td colspan="2">Number of users: <?php echo $user->rowCount() ?></td>
                    <td colspan="3"><a href="add.php">Add a user</a></td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
