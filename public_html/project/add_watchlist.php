<?php
require(__DIR__ . "/../../partials/nav.php");

if (!is_logged_in(true)) {
    flash("You must be logged in to add anime to your watchlist", "warning");
    die(header("Location: " . get_url("login.php")));
}

$anime_id = (int)se($_GET, "id", 0, false);

if ($anime_id <= 0) {
    flash("Invalid anime id", "warning");
    die(header("Location: " . get_url("list_anime.php")));
}

$user_id = se($_SESSION["user"], "id", 0, false);

$db = getDB();

$stmt = $db->prepare("INSERT INTO UserWatchlist (user_id, anime_id)
VALUES (:user_id, :anime_id)
ON DUPLICATE KEY UPDATE created = created");

try {
    $stmt->execute([
        ":user_id" => $user_id,
        ":anime_id" => $anime_id
    ]);
    flash("Anime added to your watchlist", "success");
} catch (PDOException $e) {
    flash("There was an error adding this anime to your watchlist", "danger");
}

die(header("Location: " . get_url("anime_details.php?id=" . $anime_id)));
?>