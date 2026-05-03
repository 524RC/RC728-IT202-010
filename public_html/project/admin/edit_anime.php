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

    $hasError = false;

    if (empty($title)) {
        flash("Title is required", "warning");
        $hasError = true;
    }

    if (strlen($title) > 255) {
        flash("Title must be 255 characters or less", "warning");
        $hasError = true;
    }

    if (!empty($picture_url) && !filter_var($picture_url, FILTER_VALIDATE_URL)) {
        flash("Invalid picture URL", "warning");
        $hasError = true;
    }

    if (!empty($mal_url) && !filter_var($mal_url, FILTER_VALIDATE_URL)) {
        flash("Invalid MyAnimeList URL", "warning");
        $hasError = true;
    }

    if (!$hasError) {
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
}
?>

<div class="anime-box">
<h3>Edit Anime</h3>

<form method="POST" onsubmit="return validate(this)">
    <div>
        <input name="title" required maxlength="255"
               value="<?php echo htmlspecialchars($title); ?>" />
    </div>

    <div>
        <textarea name="description"><?php echo htmlspecialchars($description); ?></textarea>
    </div>

    <div>
        <input name="picture_url" type="url"
               value="<?php echo htmlspecialchars($picture_url); ?>" />
    </div>

    <div>
        <input name="mal_url" type="url"
               value="<?php echo htmlspecialchars($mal_url); ?>" />
    </div>

    <input type="submit" value="Update" />
</form>
</div>
<script>
function validate(form) {
    let title = form.title.value.trim();
    let pictureUrl = form.picture_url.value.trim();
    let malUrl = form.mal_url.value.trim();

    if (title.length === 0) {
        alert("Title is required");
        return false;
    }

    if (title.length > 255) {
        alert("Title must be 255 characters or less");
        return false;
    }

    if (pictureUrl.length > 0 && !pictureUrl.startsWith("http")) {
        alert("Picture URL must be valid");
        return false;
    }

    if (malUrl.length > 0 && !malUrl.startsWith("http")) {
        alert("MyAnimeList URL must be valid");
        return false;
    }

    return true;
}
</script>

<?php require(__DIR__ . "/../../../partials/flash.php"); ?>