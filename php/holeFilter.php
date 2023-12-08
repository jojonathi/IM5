<?php

require("config.php");

// Holen der eindeutigen Fächer
$sqlFach = "
    SELECT fach.name, COUNT(DISTINCT fach_game.game_ID) AS game_count
    FROM fach
    LEFT JOIN fach_game ON fach.ID = fach_game.fach_ID
    GROUP BY fach.name
    HAVING game_count > 0
";
$stmtFach = $pdo->prepare($sqlFach);
$stmtFach->execute();
$fachResult = $stmtFach->fetchAll();

// Holen der eindeutigen Plattformen mit Anzahl der Spiele
$sqlPlattform = "
    SELECT plattform.name, plattform.bild, COUNT(DISTINCT plattform_game.game_ID) AS game_count
    FROM plattform
    LEFT JOIN plattform_game ON plattform.ID = plattform_game.plattform_ID
    GROUP BY plattform.name
    HAVING game_count > 0
";
$stmtPlattform = $pdo->prepare($sqlPlattform);
$stmtPlattform->execute();
$plattformResult = $stmtPlattform->fetchAll();

// Hole der eindeutigen Genres
$sqlGenre = "
SELECT genre.name, COUNT(DISTINCT genre_game.game_ID) AS game_count
FROM genre
LEFT JOIN genre_game ON genre.ID = genre_game.genre_ID
GROUP BY genre.name
HAVING game_count > 0
";

$stmtGenre = $pdo->prepare($sqlGenre);
$stmtGenre->execute();
$genreResult = $stmtGenre->fetchAll();

// Kombinieren der Filterwerte in einem Array
$filterData = [
    "fach" => $fachResult,
    "plattform" => $plattformResult,
    "genre" => $genreResult
];

// Rückgabe der Filterwerte als JSON
echo json_encode($filterData);
