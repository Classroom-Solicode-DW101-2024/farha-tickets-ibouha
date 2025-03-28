<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION["idUser"])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eventId = $_POST["eventId"];
    $userId = $_SESSION["idUser"];
    $qteNormal = intval($_POST["qteBilletsNormal"]);
    $qteReduit = intval($_POST["qteBilletsReduit"]);

    if ($qteNormal <= 0 && $qteReduit <= 0) {
        header("Location: details.php?id=$eventId&error=Veuillez sélectionner au moins un billet.");
        exit();
    }

    // Get event edition info
    $stmt = $pdo->prepare("SELECT ed.editionId, s.capSalle, 
                            (SELECT SUM(qteBilletsNormal + qteBilletsReduit) FROM Reservation WHERE editionId = ed.editionId) AS totalReserved
                           FROM Edition ed
                           JOIN Salle s ON ed.NumSalle = s.NumSalle
                           WHERE ed.eventId = ?");
    $stmt->execute([$eventId]);
    $edition = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$edition) {
        header("Location: details.php?id=$eventId&error=Édition introuvable.");
        exit();
    }

    $editionId = $edition["editionId"];
    $capacity = $edition["capSalle"];
    $reserved = $edition["totalReserved"] ?? 0;
    $remaining = $capacity - $reserved;

    if (($qteNormal + $qteReduit) > $remaining) {
        header("Location: details.php?id=$eventId&error=Plus assez de places disponibles.");
        exit();
    }

    // Insert reservation
    $stmt = $pdo->prepare("INSERT INTO Reservation (qteBilletsNormal, qteBilletsReduit, editionId, idUser) VALUES (?, ?, ?, ?)");
    $stmt->execute([$qteNormal, $qteReduit, $editionId, $userId]);
    $reservationId = $pdo->lastInsertId();

    // Generate tickets
    for ($i = 0; $i < $qteNormal; $i++) {
        $stmt = $pdo->prepare("INSERT INTO Billet (billetId, typeBillet, placeNum, idReservation) VALUES (?, 'Normal', ?, ?)");
        $stmt->execute([uniqid("B"), $reserved + $i + 1, $reservationId]);
    }
    for ($i = 0; $i < $qteReduit; $i++) {
        $stmt = $pdo->prepare("INSERT INTO Billet (billetId, typeBillet, placeNum, idReservation) VALUES (?, 'Reduit', ?, ?)");
        $stmt->execute([uniqid("B"), $reserved + $qteNormal + $i + 1, $reservationId]);
    }

    header("Location: ../profile/index.php?success=Réservation réussie !");
    exit();
}
?>
