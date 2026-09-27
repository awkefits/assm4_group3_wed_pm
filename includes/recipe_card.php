<?php
/** Reusable recipe card partial. Expects $recipe. */
?>
<article class="recipe-card">
    <a class="recipe-media" href="<?= e(url('recipe.php?id=' . (int)$recipe['id'])) ?>">
        <img src="<?= e(recipe_image($recipe['cover_image'] ?? null)) ?>" alt="<?= e($recipe['title']) ?>">
        <?php if (!empty($recipe['is_sponsored'])): ?><span class="pill sponsored">Sponsored</span><?php endif; ?>
        <?php if (!empty($recipe['is_premium'])): ?><span class="pill premium">Premium</span><?php endif; ?>
    </a>
    <div class="recipe-card-body">
        <div class="recipe-meta"><span><?= e($recipe['cuisine']) ?></span><span>•</span><span><?= e($recipe['difficulty']) ?></span><span>•</span><span><?= (int)$recipe['total_minutes'] ?> min</span></div>
        <h3><a href="<?= e(url('recipe.php?id=' . (int)$recipe['id'])) ?>"><?= e($recipe['title']) ?></a></h3>
        <p><?= e(mb_strimwidth($recipe['description'], 0, 120, '…')) ?></p>
        <div class="recipe-card-footer">
            <span class="rating" aria-label="Rating <?= number_format((float)$recipe['avg_rating'], 1) ?> out of 5"><?= e(stars((float)$recipe['avg_rating'])) ?> <small><?= number_format((float)$recipe['avg_rating'], 1) ?></small></span>
            <span><?= (int)$recipe['favorite_count'] ?> saves</span>
        </div>
    </div>
</article>
