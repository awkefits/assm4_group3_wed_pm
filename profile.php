<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/classes/UserRepository.php';
require_once __DIR__ . '/classes/RecipeRepository.php';
$viewId = isset($_GET['id']) ? (int)$_GET['id'] : (int)(current_user()['id'] ?? 0);
if (!$viewId) redirect('login.php');
$userRepo = new UserRepository();
$recipeRepo = new RecipeRepository();
$profile = $userRepo->findById($viewId);
if (!$profile) { http_response_code(404); exit('Profile not found.'); }
$recipes = $recipeRepo->byAuthor($viewId);
$stmt = db()->prepare('SELECT r.*, (r.prep_minutes+r.cook_minutes) total_minutes, COALESCE(AVG(rt.rating),0) avg_rating, COUNT(DISTINCT f2.id) favorite_count FROM favorites f JOIN recipes r ON r.id=f.recipe_id LEFT JOIN ratings rt ON rt.recipe_id=r.id LEFT JOIN favorites f2 ON f2.recipe_id=r.id WHERE f.user_id=? AND r.status="approved" GROUP BY r.id ORDER BY f.created_at DESC LIMIT 8');
$stmt->execute([$viewId]); $saved = $stmt->fetchAll();
$stmt = db()->prepare('SELECT COUNT(*) FROM follows WHERE following_id=?'); $stmt->execute([$viewId]); $followers=(int)$stmt->fetchColumn();
$stmt = db()->prepare('SELECT COUNT(*) FROM follows WHERE follower_id=?'); $stmt->execute([$viewId]); $following=(int)$stmt->fetchColumn();
$isFollowing=false;
if (is_logged_in() && (int)current_user()['id'] !== $viewId) { $stmt=db()->prepare('SELECT 1 FROM follows WHERE follower_id=? AND following_id=?');$stmt->execute([current_user()['id'],$viewId]);$isFollowing=(bool)$stmt->fetchColumn(); }
$pageTitle=$profile['display_name']; require __DIR__.'/includes/header.php';
?>
<section class="section container">
<div class="profile-hero"><img class="profile-avatar" src="<?= e(avatar_url($profile['profile_image'])) ?>" alt="<?= e($profile['display_name']) ?> profile picture"><div><span class="eyebrow">CookBook creator</span><h1><?= e($profile['display_name']) ?></h1><p><?= e($profile['bio'] ?: 'This cook has not added a bio yet.') ?></p><div class="profile-stats"><span><strong><?= count($recipes) ?></strong> recipes</span><span><strong><?= $followers ?></strong> followers</span><span><strong><?= $following ?></strong> following</span></div><?php if ($profile['dietary_preferences']): ?><div class="tag-row"><span class="tag">Preferences: <?= e($profile['dietary_preferences']) ?></span></div><?php endif; ?></div><div class="profile-actions"><?php if (is_logged_in() && (int)current_user()['id']===$viewId): ?><a class="button button-secondary" href="<?= e(url('profile_edit.php')) ?>">Edit profile</a><?php elseif (is_logged_in()): ?><form method="post" action="<?= e(url('follow_action.php')) ?>"><?= csrf_field() ?><input type="hidden" name="following_id" value="<?= $viewId ?>"><button class="button <?= $isFollowing?'button-secondary':'button-primary' ?>"><?= $isFollowing?'Following':'Follow creator' ?></button></form><?php endif; ?></div></div>
</section>
<section class="section section-soft"><div class="container"><div class="section-heading"><div><span class="eyebrow">Published recipes</span><h2>From <?= e($profile['display_name']) ?>’s kitchen</h2></div></div><div class="recipe-grid"><?php if(!$recipes):?><div class="empty-state"><h3>No recipes yet</h3><p>Published recipes will appear here after moderation.</p></div><?php endif;?><?php foreach($recipes as $recipe): if($recipe['status']!=='approved' && (!is_logged_in() || ((int)current_user()['id']!==$viewId && !is_admin()))) continue; require __DIR__.'/includes/recipe_card.php'; endforeach;?></div></div></section>
<?php if(is_logged_in() && (int)current_user()['id']===$viewId): ?><section class="section container"><div class="section-heading"><div><span class="eyebrow">Personal cookbook</span><h2>Recently saved</h2></div><a class="text-link" href="<?= e(url('bookmarks.php')) ?>">Manage all saved recipes →</a></div><div class="recipe-grid"><?php foreach($saved as $recipe) require __DIR__.'/includes/recipe_card.php'; ?></div></section><?php endif; ?>
<?php require __DIR__.'/includes/footer.php'; ?>
