<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("No permission", "danger");
    die(header("Location: " . get_url("landing.php")));
}

$id = (int)se($_GET, "id", 0, false);
$return = se($_GET, "return", get_url("list_anime.php"), false);

if ($id <= 0) {
    flash("Invalid ID", "danger");
    die(header("Location: " . $return));
}

$db = getDB();

$stmt = $db->prepare("DELETE FROM Anime WHERE id = :id");

try {
    $stmt->execute([":id" => $id]);
    flash("Anime deleted", "success");
} catch (PDOException $e) {
    flash("Error deleting anime", "danger");
}

die(header("Location: " . $return));