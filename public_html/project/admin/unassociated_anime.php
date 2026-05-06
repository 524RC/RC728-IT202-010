<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("No permission", "danger");
    die(header("Location: " . get_url("landing.php")));
}

$db = getDB();

$search = se($_GET, "search", "", false);
$sort = se($_GET, "sort", "created", false);
$limit = (int)se($_GET, "limit", 10, false);

if ($limit < 1 || $limit > 100) {
    $limit = 10;
}

$allowed_sorts = ["title", "created", "modified", "is_api"];
if (!in_array($sort, $allowed_sorts)) {
    $sort = "created";
}

$query = "SELECT id, title, description, picture_url, is_api, created, modified
FROM Anime
WHERE id NOT IN (
    SELECT anime_id FROM UserWatchlist
)";

$params = [];

if (!empty($search)) {
    $query .= " AND title LIKE :search";
    $params[":search"] = "%$search%";
}

if ($sort === "title") {
    $query .= " ORDER BY title ASC LIMIT $limit";
} else {
    $query .= " ORDER BY $sort DESC LIMIT $limit";
}

$stmt = $db->prepare($query);
$stmt->execute($params);
$anime_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="anime-box">
    <h3>Unassociated Anime</h3>

    <p>Total shown: <?php echo count($anime_list); ?></p>

    <form method="GET" class="anime-filter-form">
        <input 
            type="search" 
            name="search" 
            placeholder="Anime Title Filter"
            value="<?php echo htmlspecialchars($search); ?>"
        />

        <select name="sort">
            <option value="created" <?php echo $sort === "created" ? "selected" : ""; ?>>Recently Added</option>
            <option value="modified" <?php echo $sort === "modified" ? "selected" : ""; ?>>Recently Updated</option>
            <option value="title" <?php echo $sort === "title" ? "selected" : ""; ?>>Alphabetical</option>
            <option value="is_api" <?php echo $sort === "is_api" ? "selected" : ""; ?>>API/Manual</option>
        </select>

        <label>Limit:</label>
        <input 
            type="number" 
            name="limit" 
            min="1" 
            max="100" 
            value="<?php echo $limit; ?>"
        />

        <input type="submit" value="Search" />
    </form>

    <?php if (empty($anime_list)) : ?>
        <p>No results available</p>
    <?php else : ?>
        <table class="anime-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Source</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($anime_list as $anime) : ?>
                    <tr>
                        <td>
                            <?php if (!empty($anime["picture_url"])) : ?>
                                <img src="<?php se($anime, "picture_url"); ?>" width="70" />
                            <?php else : ?>
                                No image
                            <?php endif; ?>
                        </td>

                        <td><?php se($anime, "title"); ?></td>

                        <td>
                            <?php
                            $desc = se($anime, "description", "", false);
                            echo htmlspecialchars(substr($desc, 0, 120));
                            echo strlen($desc) > 120 ? "..." : "";
                            ?>
                        </td>

                        <td><?php echo se($anime, "is_api", 0, false) ? "API" : "Manual"; ?></td>

                        <td><?php se($anime, "created"); ?></td>

                        <td>
                            <a href="<?php echo get_url('anime_details.php?id=' . $anime['id']); ?>">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>