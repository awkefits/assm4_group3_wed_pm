<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/classes/RecipeRepository.php';
$repo = new RecipeRepository();
$featured = $repo->featured(6);
$stats = ['recipes' => 0, 'cooks' => 0, 'reviews' => 0];
try {
    $stats['recipes'] = (int)db()->query('SELECT COUNT(*) FROM recipes WHERE status = "approved"')->fetchColumn();
    $stats['cooks'] = (int)db()->query('SELECT COUNT(*) FROM users WHERE role = "user"')->fetchColumn();
    $stats['reviews'] = (int)db()->query('SELECT COUNT(*) FROM comments WHERE status = "approved"')->fetchColumn();
} catch (Throwable $e) {}
$pageTitle = 'Discover recipes that fit your life';
require __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="container hero-grid">
        <div class="hero-copy">
            <span class="eyebrow">A community kitchen for every skill level</span>
            <h1>Find dinner. Share a favourite. <em>Cook with confidence.</em></h1>
            <p>CookBook makes recipes easier to discover, follow, save and discuss—whether you are cooking your first meal or sharing a family classic.</p>
            <form class="hero-search" action="<?= e(url('browse.php')) ?>" method="get">
                <label class="sr-only" for="home-q">Search recipes</label>
                <input id="home-q" type="search" name="q" placeholder="Try “chicken adobo”, “tofu”, or “30 minute dinner”">
                <button class="button button-primary" type="submit">Search recipes</button>
            </form>
            <div class="quick-links"><span>Popular:</span><a href="<?= e(url('browse.php?cuisine=Filipino')) ?>">Filipino</a><a href="<?= e(url('browse.php?diet=Vegetarian')) ?>">Vegetarian</a><a href="<?= e(url('browse.php?max_minutes=30')) ?>">Under 30 min</a></div>
        </div>
        <div class="hero-panel">
            <img src="<?= e(url('assets/images/hero-cooking.svg')) ?>" alt="Illustration of fresh ingredients and a cooking pot">
            <div class="hero-stat-row">
                <div><strong><?= number_format($stats['recipes']) ?>+</strong><span>community recipes</span></div>
                <div><strong><?= number_format($stats['cooks']) ?>+</strong><span>home cooks</span></div>
                <div><strong><?= number_format($stats['reviews']) ?>+</strong><span>discussions</span></div>
            </div>
        </div>
    </div>
</section>

<section class="section container">
    <div class="section-heading"><div><span class="eyebrow">Made by the community</span><h2>Recipes worth saving</h2></div><a class="text-link" href="<?= e(url('browse.php')) ?>">Explore all recipes →</a></div>
    <div class="recipe-grid">
        <?php foreach ($featured as $recipe) require __DIR__ . '/includes/recipe_card.php'; ?>
    </div>
</section>

<section class="section section-soft">
    <div class="container feature-grid">
        <article><span class="feature-icon">⌕</span><h3>Find exactly what you want</h3><p>Search by ingredient, cuisine, dietary preference, preparation time, rating, popularity or newest recipes.</p></article>
        <article><span class="feature-icon">♡</span><h3>Build your own cookbook</h3><p>Favourite recipes and organise bookmarks into personal folders for fast access later.</p></article>
        <article><span class="feature-icon">✦</span><h3>Cook with trusted guidance</h3><p>Structured ingredients, numbered steps, nutrition per serving, ratings and moderated community discussion.</p></article>
        <article><span class="feature-icon">◎</span><h3>Stay connected</h3><p>Follow creators, receive notifications, discuss recipes and share by link, email or social platforms.</p></article>
    </div>
</section>

<section class="section container split-callout">
    <div><span class="eyebrow">Share your kitchen</span><h2>Your recipe could become someone’s new favourite.</h2><p>Add clear ingredients, step-by-step instructions, photos, video and nutrition information. New community recipes are reviewed before publication to support quality and safety.</p><a class="button button-primary" href="<?= e(is_logged_in() ? url('recipe_form.php') : url('register.php')) ?>">Share a recipe</a></div>
    <div class="mini-card"><h3>Why CookBook?</h3><ul class="check-list"><li>Structured recipe publishing</li><li>Moderated community content</li><li>Mobile-first responsive design</li><li>Personal collections and favourites</li><li>Secure accounts and role-based access</li><li>Transparent sponsored and premium labels</li></ul></div>
</section>

<section class="section section-dark">
    <div class="container">
        <div class="section-heading"><div><span class="eyebrow light">DWIN309 project team</span><h2>Who built this prototype</h2></div></div>
        <div class="team-grid">
            <article><strong>Member 1</strong><span>Mariah Angelica Serrato· K240911</span><p>Member 1 is responsible for Favorites/Bookmarks and Recipe Sharing/Social Integration (Modules 6 and 7), including Social Features, Testing, and Final Report.</p></article>
            <article><strong>Member 2</strong><span>Kasseus Andrei Baleda · K240947</span><p>Member 2 is responsible for Recipe Management and Image/Media (Modules 3 and 8), including Recipe Design and Implementation.</p></article>
            <article><strong>Member 3</strong><span>Priya Paneru · K241081</span><p>Member 3 handles Nutrition and the Admin Dashboard (Modules 9 and 10), as well as Database, Security, and Integration.</p></article>
            <article><strong>Member 4</strong><span>Dristi Kandel · K240881</span><p>Member 4 is responsible for User Management and User Profile (Modules 1 and 2), including the Requirements and System Design sections.</p></article>
            <article><strong>Member 5</strong><span>Duc Long Nguyen · K230861</span><p>Member 5 handles Search/Browsing and Recipe Viewing (Modules 4 and 5), including Search/Viewing and UI Design.</p></article>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
