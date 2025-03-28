<?php
require_once "../config/db.php";
include "../includes/navbar.php";

// Get event ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<p class='text-center text-red-500'>Événement introuvable.</p>";
    exit();
}

$eventId = $_GET['id'];

// Fetch event details
$stmt = $pdo->prepare("SELECT e.*, ed.dateEvent, ed.NumSalle ,ed.image, s.DescSalle, s.capSalle
                       FROM Evenement e
                       JOIN Edition ed ON e.eventId = ed.eventId
                       JOIN Salle s ON ed.NumSalle = s.NumSalle
                       WHERE e.eventId = ?");
$stmt->execute([$eventId]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    echo "<p class='text-center text-red-500'>Événement introuvable.</p>";
    exit();
}
?>

<div class="max-w-4xl mx-auto p-6 bg-white shadow-md rounded-lg mt-6">
    <img src="<?= htmlspecialchars($event['image']) ?>" 
         alt="<?= htmlspecialchars($event['eventTitle']) ?>" class="w-full h-64 object-cover rounded">
    
    <h1 class="text-2xl font-semibold text-gray-800 mt-4"><?= htmlspecialchars($event['eventTitle']) ?></h1>
    <p class="text-sm text-gray-600"><?= htmlspecialchars($event['eventType']) ?> - <?= htmlspecialchars($event['dateEvent']) ?></p>

    <p class="mt-4 text-gray-700"><?= nl2br(htmlspecialchars($event['eventDescription'])) ?></p>

    <p class="mt-4 text-gray-600"><strong>Salle:</strong> <?= htmlspecialchars($event['DescSalle']) ?> (Capacité: <?= htmlspecialchars($event['capSalle']) ?>)</p>

    <p class="mt-4"><strong>Tarif Normal:</strong> <span class="text-blue-600"><?= number_format($event['TariffNormal'], 2) ?> MAD</span></p>
    <p><strong>Tarif Réduit:</strong> <span class="text-blue-600"><?= number_format($event['TariffReduit'], 2) ?> MAD</span></p>

    <!-- Purchase Form -->
    <div class="mt-6">
        <?php if (!isset($_SESSION["idUser"])) : ?>
            <p class="text-red-500 text-center">Vous devez être connecté pour acheter un billet.</p>
            <div class="text-center mt-4">
                <a href="../auth/login.php" class="py-2 px-4 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Connexion
                </a>
            </div>
        <?php else : ?>
            <form action="reserve.php" method="POST" class="space-y-4">
                <input type="hidden" name="eventId" value="<?= $eventId ?>">
                <label class="block text-sm font-medium text-gray-700">Billets Normaux</label>
                <input type="number" name="qteBilletsNormal" min="0" max="10" value="0" class="w-full p-2 border rounded">
                
                <label class="block text-sm font-medium text-gray-700">Billets Réduits</label>
                <input type="number" name="qteBilletsReduit" min="0" max="10" value="0" class="w-full p-2 border rounded">
                
                <button type="submit" class="w-full py-2 px-4 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Acheter maintenant
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<footer class="w-full relative bottom-0 ">
        <?php include "../includes/footer.php";?>
    </footer>