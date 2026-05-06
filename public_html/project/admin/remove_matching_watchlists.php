<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("No permission", "danger");
    die(header("Location: " . get_url("landing.php")));
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    flash("Invalid request", "warning");
    die(header("Location: " . get_url("admin/all_watchlists.php")));
}

$username = se($_POST, "username", "", false);

if (empty($username)) {
    flash("Username filter is required", "warning");
    die(header("Location: " . get_url("admin/all_watchlists.php")));
}

$db = getDB();

$stmt = $db->prepare("DELETE uw
FROM UserWatchlist uw
JOIN Users u ON uw.user_id = u.id
WHERE u.username LIKE :username");

try {
    $stmt->execute([
        ":username" => "%$username%"
    ]);

    flash("Removed matching user watchlist associations", "success");
} catch (PDOException $e) {
    flash("Error removing matching associations", "danger");
    error_log("Remove matching watchlists error: " . var_export($e->errorInfo, true));
}

die(header("Location: " . get_url("admin/all_watchlists.php")));
?>