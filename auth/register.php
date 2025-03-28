<?php
require_once "../config/db.php"; // Include database connection

$errors = [
    "nomUser" => "",
    "prenomUser" => "",
    "mailUser" => "",
    "motPasse" => "",
    "confirmMotPasse" => ""
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idUser = uniqid("U"); // Generate a unique user ID
    $nom = trim($_POST["nomUser"]);
    $prenom = trim($_POST["prenomUser"]);
    $email = trim($_POST["mailUser"]);
    $password = $_POST["motPasse"];
    $confirm_password = $_POST["confirmMotPasse"];

    // Validate inputs
    if (empty($nom)) {
        $errors["nomUser"] = "Le prénom est obligatoire.";
    }
    if (empty($prenom)) {
        $errors["prenomUser"] = "Le nom est obligatoire.";
    }
    if (empty($email)) {
        $errors["mailUser"] = "L'email est obligatoire.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["mailUser"] = "Format d'email invalide.";
    }
    if (empty($password)) {
        $errors["motPasse"] = "Le mot de passe est obligatoire.";
    }
    if (empty($confirm_password)) {
        $errors["confirmMotPasse"] = "Veuillez confirmer votre mot de passe.";
    } elseif ($password !== $confirm_password) {
        $errors["confirmMotPasse"] = "Les mots de passe ne correspondent pas.";
    }

    // If no errors, insert into database
    if (!array_filter($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO Utilisateur (idUser, nomUser, prenomUser, mailUser, motPasse) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$idUser, $nom, $prenom, $email, $password]); // Password NOT hashed

            // Redirect to login page after successful registration
            header("Location: login.php?success=1");
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Email already used
                $errors["mailUser"] = "Cet email est déjà utilisé.";
            } else {
                echo "Erreur lors de l'inscription.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">

<div class="w-[70%] max-sm:max-w-lg mx-auto p-6 mt-6 bg-white rounded-lg shadow-md">
    <div class="text-center mb-12 sm:mb-16">
        <h2 class="text-xl font-semibold text-gray-700">Créer un compte</h2>
    </div>

    <form method="POST" action="">
        <div class="grid sm:grid-cols-2 gap-8">
            <!-- Prénom -->
            <div>
                <label class="text-slate-800 text-sm font-medium mb-2 block">Prénom</label>
                <input name="nomUser" type="text" class="bg-slate-100 w-full text-slate-800 text-sm px-4 py-3 rounded focus:bg-transparent outline-blue-500 transition-all"
                       placeholder="Entrez votre prénom" value="<?= htmlspecialchars($_POST['nomUser'] ?? '') ?>" />
                <p class="text-red-500 text-xs mt-1"><?= $errors["nomUser"] ?></p>
            </div>

            <!-- Nom -->
            <div>
                <label class="text-slate-800 text-sm font-medium mb-2 block">Nom</label>
                <input name="prenomUser" type="text" class="bg-slate-100 w-full text-slate-800 text-sm px-4 py-3 rounded focus:bg-transparent outline-blue-500 transition-all"
                       placeholder="Entrez votre nom" value="<?= htmlspecialchars($_POST['prenomUser'] ?? '') ?>" />
                <p class="text-red-500 text-xs mt-1"><?= $errors["prenomUser"] ?></p>
            </div>

            <!-- Email -->
            <div>
                <label class="text-slate-800 text-sm font-medium mb-2 block">Adresse Email</label>
                <input name="mailUser" type="text" class="bg-slate-100 w-full text-slate-800 text-sm px-4 py-3 rounded focus:bg-transparent outline-blue-500 transition-all"
                       placeholder="Entrez votre email" value="<?= htmlspecialchars($_POST['mailUser'] ?? '') ?>" />
                <p class="text-red-500 text-xs mt-1"><?= $errors["mailUser"] ?></p>
            </div>

            <!-- Mot de passe -->
            <div>
                <label class="text-slate-800 text-sm font-medium mb-2 block">Mot de passe</label>
                <input name="motPasse" type="password" class="bg-slate-100 w-full text-slate-800 text-sm px-4 py-3 rounded focus:bg-transparent outline-blue-500 transition-all"
                       placeholder="Entrez votre mot de passe" />
                <p class="text-red-500 text-xs mt-1"><?= $errors["motPasse"] ?></p>
            </div>

            <!-- Confirmation du mot de passe -->
            <div>
                <label class="text-slate-800 text-sm font-medium mb-2 block">Confirmer le mot de passe</label>
                <input name="confirmMotPasse" type="password" class="bg-slate-100 w-full text-slate-800 text-sm px-4 py-3 rounded focus:bg-transparent outline-blue-500 transition-all"
                       placeholder="Confirmez votre mot de passe" />
                <p class="text-red-500 text-xs mt-1"><?= $errors["confirmMotPasse"] ?></p>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="mx-auto block py-3 px-6 text-sm font-medium tracking-wider rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
                S'inscrire
            </button>
        </div>
    </form>

    <p class="text-sm text-center text-gray-600 mt-4">Déjà un compte ? <a href="login.php" class="text-blue-500">Se connecter</a></p>
</div>

</body>
</html>
