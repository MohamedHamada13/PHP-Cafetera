<?php
require_once __DIR__ . "/../../config/Database.php";
require_once __DIR__ . "/../../models/User.php";
require_once __DIR__ . "/../../models/Room.php";

// Check admin access
require_once __DIR__ . "/auth_check.php";

$database = new Database();
$db = $database->connect();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm = $_POST["confirm_password"] ?? "";
    $room = trim($_POST["room_no"] ?? "");

    // Validate
    if (empty($name) || strlen($name) < 3) {
        header(
            "Location: /admin/add-user?error=Name must be at least 3 characters",
        );
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: /admin/add-user?error=Invalid email format");
        exit();
    }

    // Check for duplicate email
    $userModel = new User($db);
    if ($userModel->getByEmail($email)) {
        header("Location: /admin/add-user?error=Email already exists");
        exit();
    }

    if (strlen($password) < 8) {
        header(
            "Location: /admin/add-user?error=Password must be at least 8 characters",
        );
        exit();
    }

    if ($password !== $confirm) {
        header("Location: /admin/add-user?error=Passwords do not match");
        exit();
    }

    // Check room exists
    $roomModel = new Room($db);
    $roomData = $roomModel->getByName($room);
    if (!$roomData) {
        header("Location: /admin/add-user?error=Room number does not exist");
        exit();
    }

    // Handle file upload
    $imageName = null;
    if (!empty($_FILES["profile_picture"]["name"])) {
        $allowed = ["jpg", "jpeg", "png"];
        $ext = strtolower(
            pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION),
        );

        if (!in_array($ext, $allowed)) {
            header(
                "Location: /admin/add-user?error=Only JPG, JPEG, PNG allowed",
            );
            exit();
        }

        if ($_FILES["profile_picture"]["size"] > 1048576) {
            header(
                "Location: /admin/add-user?error=Image must be less than 1MB",
            );
            exit();
        }

        $imageName = time() . "_" . uniqid() . "." . $ext;
        $uploadDir = __DIR__ . "/../../../public/uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        move_uploaded_file(
            $_FILES["profile_picture"]["tmp_name"],
            $uploadDir . $imageName,
        );
    }

    // Create user
    $user = new User($db);
    $user->name = $name;
    $user->email = $email;
    $user->password = password_hash($password, PASSWORD_DEFAULT);
    $user->room_id = $roomData["id"];
    $user->image = $imageName;
    $user->role = "user";
    $user->save();

    header("Location: /admin/users?success=User added successfully");
    exit();
}

$error = $_GET["error"] ?? null;
$success = $_GET["success"] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <?php include __DIR__ . "/../layouts/jsCDN.php"; ?>
</head>
<body>
<?php include __DIR__ . "/../layouts/navbar.php"; ?>
<div class="container mt-5" style="max-width: 600px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Add User</h2>
        <a href="/admin/users" class="btn btn-secondary">Back to Users</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" name="confirm_password" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Room No.</label>
                    <input type="text" class="form-control" name="room_no" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Profile Picture</label>
                    <input type="file" class="form-control" name="profile_picture" accept="image/*">
                    <small class="text-muted">Max 1MB - JPG, JPEG, PNG only</small>
                </div>

                <button type="submit" class="btn btn-primary w-100">Add User</button>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
