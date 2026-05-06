<?php
require(__DIR__ . "/../../partials/nav.php");

if (!is_logged_in(true)) {
    flash("You must be logged in to view your watchlist", "warning");
    die(header("Location: " . get_url("login.php")));
}

$user_id = se($_SESSION["user"], "id", 0, false);

$db = getDB();
$search = "";
$sort = "uw.created";
$limit = 10;

$query = "SELECT a.id, a.title, a.description, a.picture_url, uw.created
FROM UserWatchlist uw
JOIN Anime a ON uw.anime_id = a.id
WHERE uw.user_id = :user_id";

$params = [":user_id" => $user_id];

if (isset($_GET["search"])) {
    $search = se($_GET, "search", "", false);

    if (!empty($search)) {
        $query .= " AND a.title LIKE :search";
        $params[":search"] = "%$search%";
    }
}

if (isset($_GET["limit"])) {
    $limit = (int)se($_GET, "limit", 10, false);

    if ($limit < 1 || $limit > 100) {
        $limit = 10;
    }
}

if (isset($_GET["sort"])) {
    $sort = se($_GET, "sort", "uw.created", false);
}

$allowed_sorts = ["uw.created", "a.title"];

if (!in_array($sort, $allowed_sorts)) {
    $sort = "uw.created";
}

if ($sort === "a.title") {
    $query .= " ORDER BY a.title ASC LIMIT $limit";
} else {
    $query .= " ORDER BY uw.created DESC LIMIT $limit";
}

$stmt = $db->prepare($query);
$stmt->execute($params);
$watchlist = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="anime-box">
    <h3>My Watchlist</h3>
    <center>
    <label>Total shown: <?php echo count($watchlist); ?></label>
    <form method="POST" action="<?php echo get_url('clear_watchlist.php'); ?>">
        <button 
            type="submit"
            onclick="return confirm('Are you sure you want to clear your watchlist?');"
        >
            Clear Watchlist
        </button>
    </form>
    </center>

    <form method="GET">
        <input 
            type="search" 
            name="search" 
            placeholder="Anime Title Filter" 
            value="<?php echo htmlspecialchars($search); ?>" 
        />

        <select name="sort">
            <option value="uw.created" <?php echo $sort === "uw.created" ? "selected" : ""; ?>>Recently added</option>
            <option value="a.title" <?php echo $sort === "a.title" ? "selected" : ""; ?>>Title</option>
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
    <?php if (empty($watchlist)) : ?>
        <p>No results available</p>
    <?php else : ?>
        <table class="anime-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Added</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($watchlist as $anime) : ?>
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
                        <td><?php se($anime, "created"); ?></td>
                        <td>
                            <a href="<?php echo get_url('anime_details.php?id=' . $anime['id']); ?>">View</a>
                            |
                            <a href="<?php echo get_url('remove_watchlist.php?id=' . $anime['id']); ?>">
                                Remove
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once(__DIR__ . "/../../partials/flash.php"); ?>