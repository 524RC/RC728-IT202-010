<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("landing.php")));
}

$db = getDB();

$users = [];
$anime_list = [];
$username = "";
$anime_search = "";

// apply/toggle associations
if (isset($_POST["users"], $_POST["anime"])) {
    $user_ids = $_POST["users"];
    $anime_ids = $_POST["anime"];

    if (empty($user_ids) || empty($anime_ids)) {
        flash("Both users and anime need to be selected", "warning");
    } else {
        foreach ($user_ids as $uid) {
            foreach ($anime_ids as $aid) {
                try {
                    $check = $db->prepare("SELECT id FROM UserWatchlist WHERE user_id = :uid AND anime_id = :aid");
                    $check->execute([":uid" => $uid, ":aid" => $aid]);
                    $existing = $check->fetch(PDO::FETCH_ASSOC);

                    if ($existing) {
                        $stmt = $db->prepare("DELETE FROM UserWatchlist WHERE user_id = :uid AND anime_id = :aid");
                        $stmt->execute([":uid" => $uid, ":aid" => $aid]);
                        flash("Removed anime $aid from user $uid watchlist", "success");
                    } else {
                        $stmt = $db->prepare("INSERT INTO UserWatchlist (user_id, anime_id) VALUES (:uid, :aid)");
                        $stmt->execute([":uid" => $uid, ":aid" => $aid]);
                        flash("Added anime $aid to user $uid watchlist", "success");
                    }
                } catch (PDOException $e) {
                    flash("There was an error toggling the watchlist association", "danger");
                    error_log("Watchlist toggle error: " . var_export($e->errorInfo, true));
                }
            }
        }
    }
}

// search users and anime
if (isset($_POST["username"], $_POST["anime_search"])) {
    $username = trim(se($_POST, "username", "", false));
    $anime_search = trim(se($_POST, "anime_search", "", false));

    if (empty($username) || empty($anime_search)) {
        flash("Username and anime search must not be empty", "warning");
    } else {
        $stmt = $db->prepare("SELECT id, username FROM Users WHERE username LIKE :username LIMIT 25");
        $stmt->execute([":username" => "%$username%"]);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("SELECT id, title, description FROM Anime WHERE title LIKE :title LIMIT 25");
        $stmt->execute([":title" => "%$anime_search%"]);
        $anime_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<div class="anime-box">
    <h3>Assign Watchlist</h3>

    <form method="POST">
        <input type="search" name="username" placeholder="Username search" value="<?php echo htmlspecialchars($username); ?>" />
        <input type="search" name="anime_search" placeholder="Anime title search" value="<?php echo htmlspecialchars($anime_search); ?>" />
        <input type="submit" value="Search" />
    </form>

    <form id="toggleForm" method="POST"></form>

    <?php if (!empty($username)) : ?>
        <input form="toggleForm" type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>" />
    <?php endif; ?>

    <?php if (!empty($anime_search)) : ?>
        <input form="toggleForm" type="hidden" name="anime_search" value="<?php echo htmlspecialchars($anime_search); ?>" />
    <?php endif; ?>

    <table class="anime-table">
        <thead>
            <tr>
                <th>Users</th>
                <th>Anime to Assign</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>
                    <?php if (empty($users)) : ?>
                        <p>No users found</p>
                    <?php else : ?>
                        <table>
                            <?php foreach ($users as $user) : ?>
                                <tr>
                                    <td>
                                        <input 
                                            form="toggleForm" 
                                            id="user_<?php se($user, 'id'); ?>" 
                                            type="checkbox" 
                                            name="users[]" 
                                            value="<?php se($user, 'id'); ?>" 
                                        />
                                        <label for="user_<?php se($user, 'id'); ?>">
                                            <?php se($user, "username"); ?>
                                        </label>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php endif; ?>
                </td>

                <td>
                    <?php if (empty($anime_list)) : ?>
                        <p>No anime found</p>
                    <?php else : ?>
                        <?php foreach ($anime_list as $anime) : ?>
                            <div>
                                <input 
                                    form="toggleForm" 
                                    id="anime_<?php se($anime, 'id'); ?>" 
                                    type="checkbox" 
                                    name="anime[]" 
                                    value="<?php se($anime, 'id'); ?>" 
                                />
                                <label for="anime_<?php se($anime, 'id'); ?>">
                                    <?php se($anime, "title"); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>

    <div style="text-align: center; margin-top: 20px;">
        <input form="toggleForm" type="submit" value="Toggle Watchlist Association" />
    </div>
</div>

<?php
require_once(__DIR__ . "/../../../partials/flash.php");
?>