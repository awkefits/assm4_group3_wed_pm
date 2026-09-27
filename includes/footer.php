<?php
/** Shared site footer. Update group member details before submission. */
?>
</main>
<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <div class="brand footer-brand"><span class="brand-mark">CB</span><span><strong>CookBook</strong><small>Community recipes made clearer.</small></span></div>
            <p>A responsive database-driven recipe sharing platform created for the final assessment for <strong>DWIN309</strong> at Kent Institute Australia.</p>
        </div>
        <div>
            <h3>Explore</h3>
            <a href="<?= e(url('browse.php')) ?>">Browse recipes</a>
            <a href="<?= e(url('recipe_form.php')) ?>">Share a recipe</a>
            <a href="<?= e(url('help.php')) ?>">Help &amp; safety</a>
        </div>
        <div>
            <h3>Group details</h3>
            <p>Member 1 — Mariah Angelica Serrato· K240911</p>
            <p>Member 2 — Kasseus Andrei Baleda · K240947</p>
            <p>Member 3 — Priya Paneru · K241081</p>
            <p>Member 4 — Dristi Kandel · K240881</p>
            <p>Member 5 — Duc Long Nguyen · K230861</p>
        </div>
    </div>
    <div class="container footer-bottom">© <?= date('Y') ?> CookBook · DWIN309 formative project prototype</div>
</footer>
</body>
</html>
