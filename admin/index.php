<?php
declare(strict_types=1);require_once __DIR__.'/../includes/functions.php';require_admin();
$stats=[
 'users'=>(int)db()->query('SELECT COUNT(*) FROM users')->fetchColumn(),
 'recipes'=>(int)db()->query('SELECT COUNT(*) FROM recipes')->fetchColumn(),
 'pending'=>(int)db()->query('SELECT COUNT(*) FROM recipes WHERE status="pending"')->fetchColumn(),
 'comments'=>(int)db()->query('SELECT COUNT(*) FROM comments')->fetchColumn(),
 'saves'=>(int)db()->query('SELECT COUNT(*) FROM favorites')->fetchColumn(),
 'views'=>(int)db()->query('SELECT COALESCE(SUM(views),0) FROM recipes')->fetchColumn(),
];
$recent=db()->query('SELECT r.id,r.title,r.status,r.created_at,u.display_name FROM recipes r JOIN users u ON u.id=r.author_id ORDER BY r.created_at DESC LIMIT 8')->fetchAll();
$top=db()->query('SELECT r.id,r.title,r.views,COUNT(DISTINCT f.id) saves,COALESCE(AVG(rt.rating),0) avg_rating FROM recipes r LEFT JOIN favorites f ON f.recipe_id=r.id LEFT JOIN ratings rt ON rt.recipe_id=r.id WHERE r.status="approved" GROUP BY r.id ORDER BY saves DESC,r.views DESC LIMIT 6')->fetchAll();
$pageTitle='Admin dashboard';require __DIR__.'/../includes/header.php';?>
<section class="admin-head"><div class="container"><span class="eyebrow light">Administrator</span><h1>CookBook operations</h1><p>Moderate community content, manage roles, review activity and configure platform settings.</p><nav class="admin-tabs"><a class="active" href="<?=e(url('admin/index.php'))?>">Overview</a><a href="<?=e(url('admin/recipes.php'))?>">Recipes</a><a href="<?=e(url('admin/comments.php'))?>">Comments</a><a href="<?=e(url('admin/users.php'))?>">Users</a><a href="<?=e(url('admin/settings.php'))?>">Settings</a><a href="<?=e(url('admin/audit.php'))?>">Audit log</a></nav></div></section>
<section class="section container"><div class="stat-grid"><?php foreach(['users'=>'Users','recipes'=>'Recipes','pending'=>'Pending moderation','comments'=>'Comments','saves'=>'Recipe saves','views'=>'Recipe views'] as $k=>$label):?><article><span><?=e($label)?></span><strong><?=number_format($stats[$k])?></strong></article><?php endforeach;?></div><div class="dashboard-grid"><section class="panel"><div class="section-heading compact"><div><h2>Recent submissions</h2></div><a class="text-link" href="<?=e(url('admin/recipes.php'))?>">Moderate →</a></div><div class="admin-list"><?php foreach($recent as $r):?><a href="<?=e(url('recipe.php?id='.$r['id']))?>"><span><strong><?=e($r['title'])?></strong><small>by <?=e($r['display_name'])?> · <?=e(date('j M',strtotime($r['created_at'])))?></small></span><span class="status status-<?=e($r['status'])?>"><?=e($r['status'])?></span></a><?php endforeach;?></div></section><section class="panel"><h2>Most engaging recipes</h2><div class="admin-list"><?php foreach($top as $r):?><a href="<?=e(url('recipe.php?id='.$r['id']))?>"><span><strong><?=e($r['title'])?></strong><small><?=number_format($r['views'])?> views · <?=$r['saves']?> saves</small></span><span><?=number_format((float)$r['avg_rating'],1)?> ★</span></a><?php endforeach;?></div></section></div></section>
<?php require __DIR__.'/../includes/footer.php';?>
