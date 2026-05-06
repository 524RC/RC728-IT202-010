<?php
require(__DIR__ . "/../../partials/nav.php");

if (!is_logged_in(true)) {
    flash("You must be logged in", "warning");
    die(header("Location: " . get_url("login.php")));
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    flash("Invalid request", "warning");
    die(header("Location: " . get_url("my_watchlist.php")));
}

$user_id = se($_SESSION["user"], "id", 0, false);

$db = getDB();

$stmt = $db->prepare("DELETE FROM UserWatchlist WHERE user_id = :user_id");

try {
    $stmt->execute([
        ":user_id" => $user_id
    ]);

    flash("Watchlist cleared", "success");
} catch (PDOException $e) {
    flash("Error clearing watchlist", "danger");
}

die(header("Location: " . get_url("my_watchlist.php")));
?>