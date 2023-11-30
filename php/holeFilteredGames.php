<?php

require("config.php");


// Filterwerte aus der Anfrage erhalten
$selectedPlattform = $_POST['plattform'];
$selectedFach = $_POST['fach'];

$placeholderPlattform = str_repeat('?,', count($selectedPlattform) - 1) . '?';
$placeholderFach = str_repeat('?,', count($selectedFach) - 1) . '?';


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
JOIN plattform ON plattform_game.plattform_ID = plattform.ID";

if (!empty($selectedPlattform)) {
    $sql .= "
    WHERE plattform.ID IN ($placeholderPlattform)";
}

if (!empty($selectedFach)) {
    if (!empty($selectedPlattform)) {
        $sql .= " AND"; 
    } else {
        $sql .= " WHERE";
    }
    $sql .= " fach.ID IN ($placeholderFach)";
}

$sql .= "
GROUP BY games.name, games.beschreibung, games.bild
ORDER BY games.name ASC
";

$stmt = $pdo->prepare($sql);
// $erfolg = $stmt->execute();

// Füge die ausgewählten Plattformen und Fächer als Parameter hinzu
$params = array_merge($selectedPlattform, $selectedFach);
$success = $stmt->execute($params);


if ($success) {

    $rows = $stmt->fetchAll();

    foreach ($rows as &$row) {
        $row['plattform_bild'] = explode(',', $row['plattform_bild']); // Wandelt die Bild-URLs in ein Array um
    }

    echo json_encode($rows);

} else {

    echo json_encode(array('error' => 'Fehler beim Abrufen der Daten.'));

}



