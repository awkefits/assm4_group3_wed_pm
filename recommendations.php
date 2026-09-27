<?php
declare(strict_types=1);require_once __DIR__.'/includes/functions.php';require_login();
$uid=(int)current_user()['id'];
// Explainable recommendations: prefer cuisines the user saved, followed creators and matching dietary tags.
$sql='SELECT r.*,u.display_name author_name,u.profile_image author_image,(r.prep_minutes+r.cook_minutes) total_minutes,
 COALESCE(AVG(rt.rating),0) avg_rating,COUNT(DISTINCT f_all.id) favorite_count,
 (CASE WHEN r.author_id IN (SELECT following_id FROM follows WHERE follower_id=:uid1) THEN 4 ELSE 0 END +
  CASE WHEN r.cuisine IN (SELECT r2.cuisine FROM favorites f2 JOIN recipes r2 ON r2.id=f2.recipe_id WHERE f2.user_id=:uid2) THEN 3 ELSE 0 END +
  CASE WHEN EXISTS (SELECT 1 FROM recipe_tags rx JOIN tags t ON t.id=rx.tag_id JOIN users ux ON ux.id=:uid3 WHERE rx.recipe_id=r.id AND ux.dietary_preferences LIKE CONCAT("%",t.name,"%")) THEN 3 ELSE 0 END) recommendation_score
 FROM recipes r JOIN users u ON u.id=r.author_id LEFT JOIN ratings rt ON rt.recipe_id=r.id LEFT JOIN favorites f_all ON f_all.recipe_id=r.id
 WHERE r.status="approved" AND r.id NOT IN (SELECT recipe_id FROM favorites WHERE user_id=:uid4)
 GROUP BY r.id ORDER BY recommendation_score DESC,avg_rating DESC,favorite_count DESC,r.published_at DESC LIMIT 18';
$stmt=db()->prepare($sql);$stmt->execute(['uid1'=>$uid,'uid2'=>$uid,'uid3'=>$uid,'uid4'=>$uid]);$recipes=$stmt->fetchAll();
$pageTitle='Recommended for you';require __DIR__.'/includes/header.php';?>
<section class="browse-head"><div class="container"><span class="eyebrow">Personalised discovery</span><h1>Picked for your cookbook</h1><p>Recommendations use your saved cuisines, followed creators and profile dietary preferences. No hidden advertising score changes community ratings.</p></div></section><section class="section container"><div class="recipe-grid"><?php foreach($recipes as $recipe)require __DIR__.'/includes/recipe_card.php';?></div></section>
<?php require __DIR__.'/includes/footer.php';?>
