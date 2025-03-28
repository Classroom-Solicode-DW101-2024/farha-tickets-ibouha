<?php
require_once "../config/db.php";
include "../includes/navbar.php";

// Get filter values from GET parameters
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$date = $_GET['date'] ?? '';

// Build SQL query with filters
$query = "SELECT e.eventId, e.eventTitle, ed.image, e.eventType, ed.dateEvent 
          FROM Evenement e 
          JOIN Edition ed ON e.eventId = ed.eventId 
          WHERE ed.dateEvent >= CURDATE()";

// Apply search filter
if (!empty($search)) {
    $query .= " AND e.eventTitle LIKE :search";
}

// Apply category filter
if (!empty($category)) {
    $query .= " AND e.eventType = :category";
}

// Apply date filter
if (!empty($date)) {
    $query .= " AND ed.dateEvent = :date";
}

$query .= " ORDER BY ed.dateEvent ASC";
$stmt = $pdo->prepare($query);

// Bind parameters
if (!empty($search)) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
if (!empty($category)) {
    $stmt->bindValue(':category', $category, PDO::PARAM_STR);
}
if (!empty($date)) {
    $stmt->bindValue(':date', $date, PDO::PARAM_STR);
}

$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="max-w-7xl mx-auto p-6">
    <h1 class="text-3xl font-semibold text-gray-800 text-center">Événements à venir</h1>
    
    <!-- Search & Filters -->
    <form method="GET" class="mt-6 flex flex-col sm:flex-row sm:justify-between space-y-4 sm:space-y-0">
        <input type="text" name="search" placeholder="Rechercher un événement..." 
               value="<?= htmlspecialchars($search) ?>"
               class="w-full sm:w-1/3 p-2 border rounded shadow-sm focus:outline-none focus:ring focus:ring-blue-300">

        <select name="category" class="w-full sm:w-1/4 p-2 border rounded shadow-sm focus:outline-none">
            <option value="">Toutes catégories</option>
            <option value="Cinéma" <?= $category == 'Cinéma' ? 'selected' : '' ?>>Cinéma</option>
            <option value="Musique" <?= $category == 'Musique' ? 'selected' : '' ?>>Musique</option>
            <option value="Théatre" <?= $category == 'Théatre' ? 'selected' : '' ?>>Théâtre</option>
            <option value="Rencontres" <?= $category == 'Rencontres' ? 'selected' : '' ?>>Rencontres</option>
        </select>

        <input type="date" name="date" value="<?= htmlspecialchars($date) ?>" 
               class="w-full sm:w-1/4 p-2 border rounded shadow-sm focus:outline-none">

        <button type="submit" class="py-2 px-4 bg-blue-500 text-white rounded hover:bg-blue-600">
            Filtrer
        </button>
    </form>

    <!-- Events Grid -->
    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6" id="eventList">
        <?php foreach ($events as $event) : ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <img src="<?= htmlspecialchars($event['image']) ?>" alt="<?= htmlspecialchars($event['eventTitle']) ?>" 
                     class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($event['eventTitle']) ?></h3>
                    <p class="text-sm text-gray-600"><?= htmlspecialchars($event['eventType']) ?> - <?= htmlspecialchars($event['dateEvent']) ?></p>
                    
                    <div class="mt-4">
                        <a href="/farhaevents/events/details.php?id=<?= $event['eventId'] ?>"
                           class="block text-center py-2 px-4 rounded font-medium 
                                  <?= (strtotime($event['dateEvent']) >= time()) ? 'bg-blue-500 text-white hover:bg-blue-600' : 'bg-gray-400 text-gray-700 cursor-not-allowed' ?>">
                            <?= (strtotime($event['dateEvent']) >= time()) ? "J’achète" : "Guichet fermé" ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<footer class="w-full relative bottom-0 ">
        <?php include "../includes/footer.php";?>
</footer>
