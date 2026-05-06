<?php
require(__DIR__ . "/../../partials/nav.php");

if (!is_logged_in(true)) {
    flash("You must be logged in", "warning");
    die(header("Location: " . get_url("login.php")));
}

$anime_id = (int)se($_GET, "id", 0, false);
$user_id = se($_SESSION["user"], "id", 0, false);

if ($anime_id <= 0) {
    flash("Invalid anime id", "warning");
    die(header("Location: " . get_url("my_watchlist.php")));
}

$db = getDB();

$stmt = $db->prepare("DELETE FROM UserWatchlist WHERE user_id = :user_id AND anime_id = :anime_id");

try {
    $stmt->execute([
        ":user_id" => $user_id,
        ":anime_id" => $anime_id
    ]);
    flash("Anime removed from your watchlist", "success");
} catch (PDOException $e) {
    flash("There was an error removing this anime", "danger");
}

die(header("Location: " . get_url("my_watchlist.php")));
?>