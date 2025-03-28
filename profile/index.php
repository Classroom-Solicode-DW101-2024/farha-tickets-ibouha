<?php
require "../config/db.php";
include "../includes/navbar.php";


// Check if the user is logged in
if (!isset($_SESSION['idUser'])) {
    header('Location: ../auth/login.php');
    exit();
}

$userId = $_SESSION['idUser'];

// Fetch user data
$sql = "SELECT idUser, nomUser, prenomUser, mailUser, motPasse FROM utilisateur WHERE idUser = :userId";
$stmt = $pdo->prepare($sql);
$stmt->execute([':userId' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // If password is provided, hash it
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    } else {
        // If no password is provided, keep the current password
        $hashedPassword = $user['motPasse'];
    }

    // Update user information in the database
    $updateSql = "UPDATE utilisateur 
                  SET nomUser = :firstName, prenomUser = :lastName, mailUser = :email, motPasse = :password 
                  WHERE idUser = :userId";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute([
        ':firstName' => $firstName,
        ':lastName' => $lastName,
        ':email' => $email,
        ':password' => $hashedPassword,
        ':userId' => $userId
    ]);

    // Update session data
    $_SESSION['nomUser'] = $firstName;

    // Redirect to the same page to show updated details
    header('Location: profile.php');
    exit();
}

// Fetch all reservations made by the user
$reservationSql = "SELECT r.idReservation, e.dateEvent, e.timeEvent, ev.eventTitle, r.qteBilletsNormal, r.qteBilletsReduit 
                   FROM reservation r
                   JOIN edition e ON r.editionId = e.editionId
                   JOIN evenement ev ON e.eventId = ev.eventId
                   WHERE r.idUser = :userId
                   ORDER BY r.idReservation DESC";
$reservationStmt = $pdo->prepare($reservationSql);
$reservationStmt->execute([':userId' => $userId]);
$reservations = $reservationStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="css/profile.css?v=<?php echo time(); ?>">
</head>

<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-blue-600 text-white p-6">
                <h1 class="text-3xl font-bold">Profil Utilisateur</h1>
            </div>

            <!-- Personal Information Section -->
            <div class="p-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Informations Personnelles</h2>
                <form method="POST" action="profile.php" class="space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label for="firstName" class="block text-sm font-medium text-gray-700 mb-2">Prénom</label>
                            <input 
                                type="text" 
                                name="firstName" 
                                id="firstName" 
                                value="<?php echo htmlspecialchars($user['nomUser']); ?>" 
                                required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
                        <div>
                            <label for="lastName" class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                            <input 
                                type="text" 
                                name="lastName" 
                                id="lastName" 
                                value="<?php echo htmlspecialchars($user['prenomUser']); ?>" 
                                required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            value="<?php echo htmlspecialchars($user['mailUser']); ?>" 
                            required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Mot de passe (laisser vide pour garder le mot de passe actuel)
                        </label>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                    </div>

                    <div class="pt-4">
                        <button 
                            type="submit" 
                            name="update" 
                            class="w-full bg-blue-600 text-white py-3 rounded-md hover:bg-blue-700 transition duration-300"
                        >
                            Mettre à jour les informations
                        </button>
                    </div>
                </form>
            </div>

            <!-- Reservations Section -->
            <div class="bg-gray-50 p-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Vos Réservations</h2>
                
                <?php if (empty($reservations)): ?>
                    <div class="text-center text-gray-600 py-8">
                        <p class="text-lg">Aucune réservation trouvée.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full bg-white shadow rounded-lg overflow-hidden">
                            <thead class="bg-blue-600 text-white">
                                <tr>
                                    <th class="px-4 py-3 text-left">ID Réservation</th>
                                    <th class="px-4 py-3 text-left">Événement</th>
                                    <th class="px-4 py-3 text-left">Date</th>
                                    <th class="px-4 py-3 text-center">Billets Normaux</th>
                                    <th class="px-4 py-3 text-center">Billets Réduits</th>
                                    <th class="px-4 py-3 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservations as $reservation): ?>
                                    <tr class="border-b hover:bg-gray-100 transition">
                                        <td class="px-4 py-3"><?php echo htmlspecialchars($reservation['idReservation']); ?></td>
                                        <td class="px-4 py-3"><?php echo htmlspecialchars($reservation['eventTitle']); ?></td>
                                        <td class="px-4 py-3">
                                            <?php echo htmlspecialchars($reservation['dateEvent']); ?> 
                                            à <?php echo htmlspecialchars($reservation['timeEvent']); ?>
                                        </td>
                                        <td class="px-4 py-3 text-center"><?php echo htmlspecialchars($reservation['qteBilletsNormal']); ?></td>
                                        <td class="px-4 py-3 text-center"><?php echo htmlspecialchars($reservation['qteBilletsReduit']); ?></td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex justify-center space-x-2">
                                                <a 
                                                    href="ticket.php?id=<?= urlencode($reservation['idReservation']) ?>" 
                                                    class="text-blue-600 hover:text-blue-800 transition"
                                                >
                                                    Billets
                                                </a>
                                                <a 
                                                    href="facteur.php?id=<?= urlencode($reservation['idReservation']) ?>" 
                                                    class="text-green-600 hover:text-green-800 transition"
                                                >
                                                    Facture
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
