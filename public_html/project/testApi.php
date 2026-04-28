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

    error_log("API Response: " . var_export($result, true));
    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        $result = [];
    }
}
?>
<div class="container-fluid">
    <h1>Anime Info</h1>
    <p>This is a test of MyAnimeList API</p>
    <form>
        <div>
            <label>Keyword</label>
            <input name="keyword" />
            <input type="submit" value="Fetch Anime" />
        </div>
    </form>
    <div class="row">
        <?php if (isset($result)) : ?>
            <?php foreach ($result as $anime) : ?>
                <pre><?php var_export($anime); ?></pre>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php
require(__DIR__ . "/../../partials/flash.php");