<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("No permission", "danger");
    die(header("Location: " . get_url("landing.php")));
}

$id = (int)se($_GET, "id", 0, false);

if ($id <= 0) {
    flash("Invalid ID", "danger");
    die(header("Location: " . get_url("list_anime.php")));
}

$db = getDB();

// fetch existing data
$stmt = $db->prepare("SELECT * FROM Anime WHERE id = :id");
$stmt->execute([":id" => $id]);
$anime = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$anime) {
    flash("Anime not found", "danger");
    die(header("Location: " . get_url("list_anime.php")));
}

// prefill values
$title = $anime["title"];
$description = $anime["description"];
$picture_url = $anime["picture_url"];
$mal_url = $anime["mal_url"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = se($_POST, "title", "", false);
    $description = se($_POST, "description", "", false);
    $picture_url = se($_POST, "picture_url", "", false);
    $mal_url = se($_POST, "mal_url", "", false);

    $stmt = $db->prepare("UPDATE Anime 
        SET title = :title, description = :description, picture_url = :picture_url, mal_url = :mal_url
        WHERE id = :id");

    try {
        $stmt->execute([
            ":title" => $title,
            ":description" => $description,
            ":picture_url" => $picture_url,
            ":mal_url" => $mal_url,
            ":id" => $id
        ]);
        flash("Anime updated", "success");
    } catch (PDOException $e) {
        flash("Error updating anime", "danger");
    }
}
?>

<h3>Edit Anime</h3>
<form method="POST">
    <input name="title" value="<?php echo htmlspecialchars($title); ?>" required />
    <textarea name="description"><?php echo htmlspecialchars($description); ?></textarea>
    <input name="picture_url" value="<?php echo htmlspecialchars($picture_url); ?>" />
    <input name="mal_url" value="<?php echo htmlspecialchars($mal_url); ?>" />
    <input type="submit" value="Update" />
</form>

<?php require(__DIR__ . "/../../../partials/flash.php"); ?>