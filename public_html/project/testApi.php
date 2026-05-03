<?php
require(__DIR__ . "/../../partials/nav.php");
//rc728 4/20/26
$result = [];
if (isset($_GET["keyword"]) && !empty($_GET["keyword"])) {
    $data = ["q" => $_GET["keyword"]];
    $endpoint = "https://myanimelist.p.rapidapi.com/v2/anime/search";
    $isRapidAPI = true;
    $rapidAPIHost = "myanimelist.p.rapidapi.com";
    $result = get($endpoint, "RAPIDAPI_KEY", $data, $isRapidAPI, $rapidAPIHost);


//rc728 4/29/26

    error_log("API Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
    $result = json_decode($result["response"], true);

    $db = getDB();
    foreach ($result as $anime) {
        $mal_id      = $anime["myanimelist_id"] ?? null;
        $title       = $anime["title"] ?? null;
        $description = $anime["description"] ?? null;
        $picture_url = $anime["picture_url"] ?? null;
        $mal_url     = $anime["myanimelist_url"] ?? null;

        $stmt = $db->prepare(
            "INSERT INTO Anime (mal_id, is_api, title, description, picture_url, mal_url)
             VALUES (:mal_id, 1, :title, :description, :picture_url, :mal_url)
             ON DUPLICATE KEY UPDATE
                title = VALUES(title),
                description = VALUES(description),
                picture_url = VALUES(picture_url),
                mal_url = VALUES(mal_url),
                modified = CURRENT_TIMESTAMP"
        );
        $stmt->execute([
            ":mal_id"      => $mal_id,
            ":title"       => $title,
            ":description" => $description,
            ":picture_url" => $picture_url,
            ":mal_url"     => $mal_url
        ]);
    }
} else {
    $result = [];
}
}
?>
<div class="container-fluid">
    <center>
    <h1>Anime Search</Search></h1>
    <p>Search any anime from MyAnimeList</p>
    </center>
    <div class="anime-container">
            <?php foreach ($result as $anime) : ?>
                <div class="anime-card">
                    
                    <?php if (!empty($anime["picture_url"])) : ?>
                        <div class="img-container">
                            <img src="<?php se($anime, "picture_url"); ?>" alt="Anime image" />
                        </div>
                    <?php endif; ?>

                    <h2><?php se($anime, "title"); ?></h2>

                    <p>
                        <?php echo htmlspecialchars(str_replace("...read more.", "", $anime["description"] ?? "")); ?>
                    </p>

                    <?php if (!empty($anime["myanimelist_url"])) : ?>
                        <a href="<?php se($anime, "myanimelist_url"); ?>" target="_blank">
                            View on MyAnimeList
                        </a>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>
        </div>
    <div class="row">
        <form>
            <div>
                <label>Keyword</label>
                <input name="keyword" />
                <input type="submit" value="Fetch Anime" />
            </div>
        </form>
    </div>
</div>
<?php
require(__DIR__ . "/../../partials/flash.php");