<?php

require("config.php");

$selectedPlattform = $_POST['plattform'] ?? [];
$selectedFach = $_POST['fach'] ?? [];

$separatedPlattform = explode(",", $selectedPlattform);
$separatedFach = explode(",", $selectedFach);

$arraySelectedPlattform = (array) $separatedPlattform;
$arraySelectedFach = (array) $separatedFach;

$selectedPlattformFormatted = '';
$selectedFachFormatted = '';

if (count($arraySelectedPlattform) > 1) {
    $selectedPlattformFormatted = "'".implode("', '", $separatedPlattform)."'";
} else {
    $selectedPlattformFormatted = "'".$selectedPlattform."'";
}

if (count($arraySelectedFach) > 1) {
    $selectedFachFormatted = "'".implode("', '", $separatedFach)."'";
} else {
    $selectedFachFormatted = "'".$selectedFach."'";
}


$sql = "
SELECT games.name, games.beschreibung, games.bild AS game_bild, games.linkURL,
    GROUP_CONCAT(DISTINCT fach.name SEPARATOR ', ') AS fach,   
    GROUP_CONCAT(DISTINCT plattform.name) AS plattform,
    GROUP_CONCAT(DISTINCT plattform.bild) AS plattform_bild,
    COUNT(DISTINCT games.ID) AS game_count
FROM games
JOIN fach_game ON games.ID = fach_game.game_ID
JOIN fach ON fach_game.fach_ID = fach.ID
JOIN plattform_game ON games.ID = plattform_game.game_ID
JOIN plattform ON plattform_game.plattform_ID = plattform.ID
WHERE ";

if (!empty($selectedPlattform)) {
    $sql .= "plattform.name IN ('$selectedPlattform')";
}

if (!empty($selectedFach)) {
    if (!empty($selectedPlattform)) {
        $sql .= " AND ";
    }
    $sql .= "fach.name IN ($selectedFachFormatted)";
}

$sql .= "
GROUP BY games.name, games.beschreibung, games.bild
ORDER BY games.name ASC";

$stmt = $pdo->prepare($sql);

$success = $stmt->execute();

if ($success) {
    $rows = $stmt->fetchAll();

    foreach ($rows as &$row) {
        $row['plattform_bild'] = explode(',', $row['plattform_bild']);
    }

    echo json_encode($rows);
} else {
    echo json_encode(array('error' => 'Fehler beim Abrufen der Daten.'));
}