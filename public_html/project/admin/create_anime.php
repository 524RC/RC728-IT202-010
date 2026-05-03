<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("landing.php")));
}

$title = "";
$description = "";
$picture_url = "";
$mal_url = "";

if (isset($_POST["title"], $_POST["description"], $_POST["picture_url"], $_POST["mal_url"])) {
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
        flash("Picture URL must be a valid URL", "warning");
        $hasError = true;
    }

    if (!empty($mal_url) && !filter_var($mal_url, FILTER_VALIDATE_URL)) {
        flash("MyAnimeList URL must be a valid URL", "warning");
        $hasError = true;
    }

    if (!$hasError) {
        $db = getDB();

        $stmt = $db->prepare("SELECT id FROM Anime WHERE title = :title");
        $stmt->execute([":title" => $title]);

        if ($stmt->fetch()) {
            flash("This anime already exists", "warning");
            $hasError = true;
        }

        if (!$hasError) {
            $stmt = $db->prepare("INSERT INTO Anime 
                (mal_id, is_api, title, description, picture_url, mal_url) 
                VALUES (NULL, 0, :title, :description, :picture_url, :mal_url)");

            try {
                $stmt->execute([
                    ":title" => $title,
                    ":description" => $description,
                    ":picture_url" => $picture_url,
                    ":mal_url" => $mal_url
                ]);
                flash("Successfully created anime $title!", "success");
            } catch (PDOException $e) {
                flash("There was an error creating the anime", "danger");
            }
        }
    }
}
?>

<h3>Create Anime</h3>
<form method="POST" onsubmit="return validate(this)">
    <div>
        <label for="title">Title</label>
        <input id="title" name="title" required maxlength="255"
            value="<?php echo htmlspecialchars($title); ?>" />
    </div>

    <div>
        <label for="desc">Description</label>
        <textarea name="description" id="desc"><?php echo htmlspecialchars($description); ?></textarea>
    </div>

    <div>
        <label for="pic">Picture URL</label>
        <input id="pic" name="picture_url" type="url"
            value="<?php echo htmlspecialchars($picture_url); ?>" />
    </div>

    <div>
        <label for="mal">MyAnimeList URL</label>
        <input id="mal" name="mal_url" type="url"
            value="<?php echo htmlspecialchars($mal_url); ?>" />
    </div>

    <input type="submit" value="Create Anime" />
</form>

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
        alert("Picture URL must be a valid URL");
        return false;
    }

    if (malUrl.length > 0 && !malUrl.startsWith("http")) {
        alert("MyAnimeList URL must be a valid URL");
        return false;
    }

    return true;
}
</script>

<?php
require_once(__DIR__ . "/../../../partials/flash.php");
?>