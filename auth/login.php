<?php
require_once "../config/db.php"; // Include database connection
 require "../includes/navbar.php" ;


$errors = [
    "mailUser" => "",
    "motPasse" => ""
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["mailUser"]);
    $password = $_POST["motPasse"];

    if (empty($email)) {
        $errors["mailUser"] = "L'email est obligatoire.";
    }
    if (empty($password)) {
        $errors["motPasse"] = "Le mot de passe est obligatoire.";
    }

    if (!array_filter($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM Utilisateur WHERE mailUser = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $password === $user["motPasse"]) {
            // Store user data in session
            $_SESSION["idUser"] = $user["idUser"];
            $_SESSION["nomUser"] = $user["nomUser"];
            $_SESSION["prenomUser"] = $user["prenomUser"];
            $_SESSION["mailUser"] = $user["mailUser"];

            // Redirect to profile or homepage
            header("Location: ../");
            exit();
        } else {
            $errors["motPasse"] = "Email ou mot de passe incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="h-screen bg-gray-100">
    <div class="flex items-center justify-center mt-16">
        <div class="w-[30%]  max-sm:max-w-lg mx-auto p-6 mt-6 bg-white rounded-lg shadow-md">
            <div class="text-center mb-12 sm:mb-16">
                <h2 class="text-xl font-semibold text-gray-700">Se connecter</h2>
            </div>

            <form method="POST" action="">
                <!-- Email -->
                <div>
                    <label class="text-slate-800 text-sm font-medium mb-2 block">Adresse Email</label>
                    <input name="mailUser" type="text" class="bg-slate-100 w-full text-slate-800 text-sm px-4 py-3 rounded focus:bg-transparent outline-blue-500 transition-all"
                        placeholder="Entrez votre email" value="<?= htmlspecialchars($_POST['mailUser'] ?? '') ?>" />
                    <p class="text-red-500 text-xs mt-1"><?= $errors["mailUser"] ?></p>
                </div>

                <!-- Mot de passe -->
                <div class="mt-4">
                    <label class="text-slate-800 text-sm font-medium mb-2 block">Mot de passe</label>
                    <input name="motPasse" type="password" class="bg-slate-100 w-full text-slate-800 text-sm px-4 py-3 rounded focus:bg-transparent outline-blue-500 transition-all"
                        placeholder="Entrez votre mot de passe" />
                    <p class="text-red-500 text-xs mt-1"><?= $errors["motPasse"] ?></p>
                </div>

                <div class="mt-12">
                    <button type="submit" class="mx-auto block py-3 px-6 text-sm font-medium tracking-wider rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
                        Se connecter
                    </button>
                </div>
            </form>

            <p class="text-sm text-center text-gray-600 mt-4">Pas encore de compte ? <a href="register.php" class="text-blue-500">S'inscrire</a></p>
        </div>

    </div>

    <footer class="w-full absolute bottom-0 ">
        <?php include "../includes/footer.php";?>
    </footer>



</body>

</html>