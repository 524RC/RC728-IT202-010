<?php
require(__DIR__ . "/../../partials/nav.php");

$user_id = (int)se($_GET, "id", 0, false);

if ($user_id <= 0) {
    flash("Invalid user", "warning");
    die(header("Location: " . get_url("landing.php")));
}

$db = getDB();

$stmt = $db->prepare("SELECT id, username, email, created FROM Users WHERE id = :id");
$stmt->execute([
    ":id" => $user_id
]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    flash("User not found", "warning");
    die(header("Location: " . get_url("landing.php")));
}
?>

<div class="anime-box">
    <h3>Public Profile</h3>

    <p>
        <strong>Username:</strong>
        <?php se($user, "username"); ?>
    </p>

    <p>
        <strong>Member Since:</strong>
        <?php se($user, "created"); ?>
    </p>
</div>

<?php require_once(__DIR__ . "/../../partials/flash.php"); ?>