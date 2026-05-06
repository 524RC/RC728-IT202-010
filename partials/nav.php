<?php
ob_start();
//include functions here so we can have it on every page that uses the nav bar
//that way we don't need to include so many other files on each page
//nav will pull in functions and functions will pull in db

// checking to see if domain has a port number attached (localhost)
$domain = $_SERVER["HTTP_HOST"];
if (strpos($domain, ":")) {
    // strip the port number if present
    $domain = explode(":", $domain)[0];
}
// used for public hosting like heroku
require(__DIR__."/../lib/functions.php");
if ($domain != "localhost") {
    session_set_cookie_params([
        "lifetime" => 60 * 60, // this is cookie lifetime, not session lifetime
        "path" => "$BASE_PATH", // path to restrict cookie to; match your project folder (case sensitive)
        "domain" => $domain, // domain to restrict cookie to
        "secure" => true, // https only
        "httponly" => true, // javascript can't access
        "samesite" => "lax" // helps prevent CSRF, but allows normal navigation
    ]);
}
session_start();

?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?php get_url('styles.css', true);?>">
<script src="<?php get_url('helpers.js', true);?>"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<nav class="navbar navbar-dark bg-dark fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?php get_url('landing.php', true); ?>">Ryan's Anime Site</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasDarkNavbar">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title">Menu</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
      </div>

      <div class="offcanvas-body">
        <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">

          <?php if (is_logged_in()) : ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php get_url('landing.php', true); ?>">Landing</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php get_url('profile.php', true); ?>">Profile</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php get_url('testApi.php', true); ?>">Search Anime</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php get_url('list_anime.php', true); ?>">Anime List</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php get_url('my_watchlist.php', true); ?>">My Watchlist</a>
            </li>
          <?php endif; ?>

          <?php if (!is_logged_in()) : ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php get_url('login.php', true); ?>">Login</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php get_url('register.php', true); ?>">Register</a>
            </li>
          <?php endif; ?>

          <?php if (has_role("Admin")) : ?>
            <hr>
            <li class="nav-item">
              <a class="nav-link" href="<?php get_url('admin/create_role.php', true); ?>">Create Role</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php get_url('admin/list_roles.php', true); ?>">List Roles</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php get_url('admin/assign_roles.php', true); ?>">Assign Roles</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php get_url('admin/create_anime.php', true); ?>">Create Anime</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php get_url('admin/all_watchlists.php', true); ?>">All Watchlists</a>
            </li>
          <?php endif; ?>

          <?php if (is_logged_in()) : ?>
            <hr>
            <li class="nav-item">
              <a class="nav-link text-danger" href="<?php get_url('logout.php', true); ?>">Logout</a>
            </li>
          <?php endif; ?>

        </ul>
      </div>
    </div>
  </div>
</nav>