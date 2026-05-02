<?php
require(__DIR__ . "/../../partials/nav.php");

$id = (int)se($_GET, "id", 0, false);

if ($id <= 0) {
    flash("Invalid anime id", "warning");
    die(header("Location: " . get_url("list_anime.php")));
}

$db = getDB();
$stmt = $db->prepare("SELECT * FROM Anime WHERE id = :id");
$stmt->execute([":id" => $id]);
$anime = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$anime) {
    flash("Anime not found", "warning");
    die(header("Location: " . get_url("list_anime.php")));
}
?>

<div class="anime-box">
    <h3><?php se($anime, "title"); ?></h3>

    <?php if (!empty($anime["picture_url"])) : ?>
        <img src="<?php se($anime, "picture_url"); ?>" alt="Anime image" width="150" />
    <?php endif; ?>

    <p><strong>Description:</strong></p>
    <p><?php se($anime, "description"); ?></p>

    <p><strong>Source:</strong> <?php echo se($anime, "is_api", 0, false) ? "API" : "Manual"; ?></p>

    <?php if (!empty($anime["mal_url"])) : ?>
        <p>
            <a href="<?php se($anime, "mal_url"); ?>" target="_blank">View on MyAnimeList</a>
        </p>
    <?php endif; ?>

    <p>
        <a href="<?php get_url('list_anime.php', true); ?>">Back to List</a>

        <?php if (has_role("Admin")) : ?>
            |
            <a href="<?php get_url('admin/edit_anime.php?id=' . $anime['id'], true); ?>">Edit</a>
            |
            <a href="<?php get_url('admin/delete_anime.php?id=' . $anime['id'], true); ?>"
               onclick="return confirm('Are you sure you want to delete this anime?');">Delete</a>
        <?php endif; ?>
    </p>
</div>

<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>