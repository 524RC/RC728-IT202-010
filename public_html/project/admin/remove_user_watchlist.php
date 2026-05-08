<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("No permission", "danger");
    die(header("Location: " . get_url("landing.php")));
}

$id = (int)se($_GET, "id", 0, false);
$return = se($_GET, "return", get_url("admin/all_watchlists.php"), false);

if ($id <= 0) {
    flash("Invalid relationship ID", "warning");
    die(header("Location: " . $return));
}

$db = getDB();

$stmt = $db->prepare("DELETE FROM UserWatchlist WHERE id = :id");

try {
    $stmt->execute([
        ":id" => $id
    ]);

    flash("Watchlist relationship removed", "success");
} catch (PDOException $e) {
    flash("Error removing relationship", "danger");
}

die(header("Location: " . $return));
?>