<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("No permission", "danger");
    die(header("Location: " . get_url("landing.php")));
}

$db = getDB();

$username = se($_GET, "username", "", false);
$search = se($_GET, "search", "", false);
$sort = se($_GET, "sort", "uw.created", false);
$limit = (int)se($_GET, "limit", 10, false);

if ($limit < 1 || $limit > 100) {
    $limit = 10;
}

$allowed_sorts = ["uw.created", "a.title", "Users.username"];
if (!in_array($sort, $allowed_sorts)) {
    $sort = "uw.created";
}

$query = "SELECT 
            uw.id as watchlist_id,
            Users.id as user_id,
            Users.username,
            a.id as anime_id,
            a.title,
            a.description,
            a.picture_url,
            uw.created,
            (
                SELECT COUNT(*) 
                FROM UserWatchlist uw2 
                WHERE uw2.anime_id = a.id
            ) as total_users
          FROM UserWatchlist uw
          JOIN Users ON uw.user_id = Users.id
          JOIN Anime a ON uw.anime_id = a.id
          WHERE 1=1";

$params = [];

if (!empty($username)) {
    $query .= " AND Users.username LIKE :username";
    $params[":username"] = "%$username%";
}

if (!empty($search)) {
    $query .= " AND a.title LIKE :search";
    $params[":search"] = "%$search%";
}

if ($sort === "a.title" || $sort === "Users.username") {
    $query .= " ORDER BY $sort ASC LIMIT $limit";
} else {
    $query .= " ORDER BY $sort DESC LIMIT $limit";
}

$stmt = $db->prepare($query);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="anime-box">
    <h3>All User Watchlists</h3>
    <center>
    <label>Total shown: <?php echo count($results); ?></label>
    </center>
    <form method="GET" class="anime-filter-form">
        <input 
            type="search" 
            name="username" 
            placeholder="Username Filter"
            value="<?php echo htmlspecialchars($username); ?>"
        />

        <input 
            type="search" 
            name="search" 
            placeholder="Anime Title Filter"
            value="<?php echo htmlspecialchars($search); ?>"
        />

        <select name="sort">
            <option value="uw.created" <?php echo $sort === "uw.created" ? "selected" : ""; ?>>Recently Added</option>
            <option value="a.title" <?php echo $sort === "a.title" ? "selected" : ""; ?>>Anime Title</option>
            <option value="Users.username" <?php echo $sort === "Users.username" ? "selected" : ""; ?>>Username</option>
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

    <?php if (!empty($username)) : ?>
        <form method="POST" action="<?php echo get_url('admin/remove_matching_watchlists.php'); ?>">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>" />
            <button type="submit" onclick="return confirm('Remove all watchlist associations for matching users?');">
                Remove All Matching User Associations
            </button>
        </form>
    <?php endif; ?>

    <?php if (empty($results)) : ?>
        <p>No results available</p>
    <?php else : ?>
        <table class="anime-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Image</th>
                    <th>Anime</th>
                    <th>Description</th>
                    <th>Total Users</th>
                    <th>Added</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($results as $row) : ?>
                    <tr>
                        <td>
                            <a href="<?php echo get_url('public_profile.php?id=' . $row['user_id']); ?>">
                                <?php se($row, "username"); ?>
                            </a>
                        </td>

                        <td>
                            <?php if (!empty($row["picture_url"])) : ?>
                                <img src="<?php se($row, "picture_url"); ?>" width="70" />
                            <?php else : ?>
                                No image
                            <?php endif; ?>
                        </td>

                        <td><?php se($row, "title"); ?></td>

                        <td>
                            <?php
                            $desc = se($row, "description", "", false);
                            echo htmlspecialchars(substr($desc, 0, 120));
                            echo strlen($desc) > 120 ? "..." : "";
                            ?>
                        </td>

                        <td><?php se($row, "total_users"); ?></td>

                        <td><?php se($row, "created"); ?></td>

                        <td>
                            <a href="<?php echo get_url('anime_details.php?id=' . $row['anime_id']); ?>">View</a>
                            |
                            <a 
                                href="<?php echo get_url(
                                    'admin/remove_user_watchlist.php?id=' . 
                                    $row['watchlist_id'] . 
                                    '&return=' . urlencode($_SERVER['REQUEST_URI'])
                                ); ?>"
                                onclick="return confirm('Remove this relationship?');"
                            >
                                Remove
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once(__DIR__ . "/../../../partials/flash.php"); ?>