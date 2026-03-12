<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <?php include __DIR__ . "/../layouts/jsCDN.php"; ?>
</head>
<body>
    <?php include __DIR__ . "/../layouts/navbar.php"; ?>
    <div class="container mt-5">
        <h1>Welcome, <?php echo $_SESSION["user_name"]; ?>!</h1>
    </div>
</body>
</html>
