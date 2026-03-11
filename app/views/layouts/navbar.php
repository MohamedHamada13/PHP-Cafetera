<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php include 'jsCDN.php'; ?>
    <style>
      <style>
.navbar{
    background-color:#4E342E !important; /* coffee brown */
}

.navbar .navbar-brand{
    color:#FFD54F !important; /* warm yellow */
    font-weight:bold;
}

.navbar .nav-link{
    color:#FFF8E1 !important;
    font-weight:500;
}

.navbar .nav-link:hover{
    color:#FFD54F !important;
}

.navbar .btn-outline-success{
    border-color:#FFD54F;
    color:#FFD54F;
}

.navbar .btn-outline-success:hover{
    background-color:#FFD54F;
    color:#4E342E;
}
.navbar-coffee{
    background-color: #6f4e37; /* coffee brown */
}

.navbar-coffee .navbar-brand,
.navbar-coffee .nav-link{
    color: #fff;
}

.navbar-coffee .nav-link:hover{
    color: #ffd700; /* coffee shop yellow highlight */
}
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-coffee">
  <div class="container-fluid">

    <a class="navbar-brand" href="#">☕ Cafeteria</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">

      <ul class="navbar-nav me-auto">

        <li class="nav-item">
          <a class="nav-link active" href="#">Home</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="#">Products</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="#">Orders</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="#">Users</a>
        </li>

      </ul>

      <form class="d-flex">
        <input class="form-control me-2" type="search" placeholder="Search coffee...">
        <button class="btn btn-outline-success">Search</button>
      </form>

    </div>
  </div>
</nav>
    <?php include 'jsCDN.php'; ?>
</body>
</html>