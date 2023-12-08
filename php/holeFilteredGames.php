<?php

require("config.php");

// Filter Variables
$selectedPlattform = $_POST['plattform'] ?? [];
$selectedFach = $_POST['fach'] ?? [];
$selectedGenre = $_POST['genre'] ?? [];

$separatedPlattform = explode(",", $selectedPlattform);
$separatedFach = explode(",", $selectedFach);
$separatedGenre = explode(",", $selectedGenre);

$arraySelectedPlattform = (array) $separatedPlattform;
$arraySelectedFach = (array) $separatedFach;
$arraySelectedGenre = (array) $separatedGenre;

$selectedPlattformFormatted = '';
$selectedFachFormatted = '';
$selectedGenreFormatted = '';

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

if (count($arraySelectedGenre) > 1) {
    $selectedGenreFormatted = "'".implode("', '", $separatedGenre)."'";
} else {
    $selectedGenreFormatted = "'".$selectedGenre."'";
}

// Überprüfung Variablen
// echo json_encode(array('plattform' => $selectedPlattformFormatted, 'fach' => $selectedFachFormatted, 'genre' => $selectedGenreFormatted));

// SQL-Abfrage
$sql = "
SELECT games.name, games.beschreibung, games.bild AS game_bild, games.jahr, games.linkURL, 
    GROUP_CONCAT(DISTINCT fach.name SEPARATOR ', ') AS fach,   
    GROUP_CONCAT(DISTINCT plattform.name) AS plattform,
    GROUP_CONCAT(DISTINCT plattform.bild) AS plattform_bild,
    GROUP_CONCAT(DISTINCT genre.name SEPARATOR ', ') AS genre,  
    COUNT(DISTINCT games.ID) AS game_count
FROM games
JOIN fach_game ON games.ID = fach_game.game_ID
JOIN fach ON fach_game.fach_ID = fach.ID
JOIN plattform_game ON games.ID = plattform_game.game_ID
JOIN plattform ON plattform_game.plattform_ID = plattform.ID
JOIN genre_game ON games.ID = genre_game.game_ID
JOIN genre ON genre_game.genre_ID = genre.ID
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

if (!empty($selectedGenre)) {
    if (!empty($selectedPlattform) OR !empty($selectedFach)) {
        $sql .= " AND ";
    }
    $sql .= "genre.name IN ($selectedGenreFormatted)";
}

$sql .= "
GROUP BY games.name, games.beschreibung, games.bild
ORDER BY games.name ASC";

$stmt = $pdo->prepare($sql);
$erfolg = $stmt->execute();

if ($erfolg) {

    $rows = $stmt->fetchAll();

    foreach ($rows as &$row) {
        $row['plattform_bild'] = explode(',', $row['plattform_bild']);
    }

    echo json_encode($rows);

} else {

    echo json_encode(array('error' => 'Fehler beim Abrufen der Daten.'));

}