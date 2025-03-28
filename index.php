<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body class="flex flex-col min-h-screen">
    <header>
        <?php
        require_once "./config/db.php";
        include "./includes/navbar.php";
        ?>
    </header>
    <main></main>
    <footer class="w-full absolute bottom-0 ">
        <?php include "./includes/footer.php";?>
    </footer>

</body>

</html>



