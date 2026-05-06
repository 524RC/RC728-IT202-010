<?php
require(__DIR__ . "/../../partials/nav.php");

$query = "SELECT id, mal_id, is_api, title, description, picture_url, mal_url, created, modified FROM Anime";
$params = null;

$search = "";
$limit = 10;
$sort = "modified";

if (isset($_GET["search"])) {
    $search = se($_GET, "search", "", false);
    if (!empty($search)) {
        $query .= " WHERE title LIKE :search";
        $params = [":search" => "%$search%"];
    }
}

if (isset($_GET["limit"])) {
    $limit = (int)se($_GET, "limit", 10, false);
    if ($limit < 1 || $limit > 100) {
        $limit = 10;
    }
}

if (isset($_GET["sort"])) {
    $sort = se($_GET, "sort", "modified", false);
}

$allowed_sorts = ["title", "created", "modified", "is_api"];

if (!in_array($sort, $allowed_sorts)) {
    $sort = "modified";
}
// this one allows it so that the most recent anime added is put to the top when it is filtered otherwise the newst records will show first
if ($sort === "title") {
    $query .= " ORDER BY title ASC LIMIT $limit";
} else {
    $query .= " ORDER BY $sort DESC LIMIT $limit";
}

$db = getDB();
$stmt = $db->prepare($query);
$anime_list = [];

try {
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results) {
        $anime_list = $results;
    } else {
        flash("No results available", "warning");
    }
} catch (PDOException $e) {
    flash("There was an error fetching anime, please try again later", "danger");
    error_log("Error fetching anime: " . var_export($e->errorInfo, true));
}
?>
<div class="anime-box">
<h3>Anime List</h3>

<form method="GET" class="anime-filter-form">
    <input 
        type="search" 
        name="search" 
        placeholder="Anime Title Filter"
        value="<?php echo htmlspecialchars($search); ?>" 
    />

    <select name="sort">
        <option value="modified" <?php echo $sort === "modified" ? "selected" : ""; ?>>Recently updated</option>
        <option value="created" <?php echo $sort === "created" ? "selected" : ""; ?>>Recently added</option>
        <option value="title" <?php echo $sort === "title" ? "selected" : ""; ?>>Alphabetical</option>
        <option value="is_api" <?php echo $sort === "is_api" ? "selected" : ""; ?>>Group by source</option>
    </select>
<div>
    <label for="limit" class = "filerFontSize" >Filter Limit:</label>
    <input 
        id="limit"
        type="number" 
        name="limit" 
        min="1" 
        max="100" 
        value="<?php echo $limit; ?>" 
    />
</div>

    <input type="submit" value="Search" />
</form>

<table class="anime-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Title</th>
            <th>Description</th>
            <th>Source</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        <?php if (empty($anime_list)) : ?>
            <tr>
                <td colspan="100%">No results available</td>
            </tr>
        <?php else : ?>
            <?php foreach ($anime_list as $anime) : ?>
                <tr>
                    <td><?php se($anime, "id"); ?></td>

                    <td>
                        <?php if (!empty($anime["picture_url"])) : ?>
                            <img 
                                src="<?php se($anime, "picture_url"); ?>" 
                                alt="Anime image" 
                                width="70" 
                            />
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

                    <td>
                        <?php echo se($anime, "is_api", 0, false) ? "API" : "Manual"; ?>
                    </td>

                    <td>
                        <a href="<?php get_url('anime_details.php?id=' . $anime['id'], true); ?>">View</a>

                        <?php if (has_role("Admin")) : ?>
                            |
                            <a href="<?php get_url('admin/edit_anime.php?id=' . $anime['id'], true); ?>">Edit</a>
                            |
                            <a 
                                href="<?php echo get_url('admin/delete_anime.php?id=' . $anime['id'] . '&return=' . urlencode($_SERVER['REQUEST_URI'])); ?>"
                                onclick="return confirm('Are you sure you want to delete this anime?');"
                            >
                                Delete
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
</div>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>