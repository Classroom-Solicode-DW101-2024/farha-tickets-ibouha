<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FarhaEvents</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<!-- Navbar -->
<nav class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <a href="/farhaevents/index.php" class="flex items-center ">
                    <span class="text-lg font-semibold text-blue-600 first-letter:text-4xl">Farha</span> <span class="font-bold">Association</span>
                </a>
            </div>

            <div class="hidden sm:flex sm:items-center sm:space-x-6">
                <a href="/farhaevents/events/index.php" class="text-gray-700 hover:text-blue-500">Événements</a>
                <a href="/farhaevents/about.php" class="text-gray-700 hover:text-blue-500">À propos</a>

                <?php if (isset($_SESSION["idUser"])) : ?>
                    <a href="/farhaevents/profile/index.php" class="text-gray-700 hover:text-blue-500">
                        Mon Profil (<?= $_SESSION["prenomUser"] ?>)
                    </a>
                    <a href="/farhaevents/auth/logout.php" class="py-2 px-4 bg-red-500 text-white rounded hover:bg-red-600">
                        Déconnexion
                    </a>
                <?php else : ?>
                    <a href="/farhaevents/auth/login.php" class="py-2 px-4 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Connexion
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
