<?php
/** Data access object for recipe discovery, details and author management. */
declare(strict_types=1);
require_once __DIR__ . '/../config/db.php';

final class RecipeRepository
{
    private string $summarySql = '
        SELECT r.*,
               (r.prep_minutes + r.cook_minutes) AS total_minutes,
               u.display_name AS author_name,
               u.profile_image AS author_image,
               COALESCE(AVG(rt.rating), 0) AS avg_rating,
               COUNT(DISTINCT rt.id) AS rating_count,
               COUNT(DISTINCT f.id) AS favorite_count
        FROM recipes r
        JOIN users u ON u.id = r.author_id
        LEFT JOIN ratings rt ON rt.recipe_id = r.id
        LEFT JOIN favorites f ON f.recipe_id = r.id
    ';

    public function featured(int $limit = 6): array
    {
        $sql = $this->summarySql . '
            WHERE r.status = "approved"
            GROUP BY r.id
            ORDER BY r.is_sponsored DESC, favorite_count DESC, avg_rating DESC, r.published_at DESC
            LIMIT ' . max(1, min($limit, 12));
        return db()->query($sql)->fetchAll();
    }

    public function search(array $filters): array
    {
        $where = ['r.status = "approved"'];
        $params = [];

        if ($q = trim((string)($filters['q'] ?? ''))) {
            $where[] = '(r.title LIKE ? OR r.description LIKE ? OR EXISTS (SELECT 1 FROM ingredients i WHERE i.recipe_id = r.id AND i.name LIKE ?))';
            $like = '%' . $q . '%';
            array_push($params, $like, $like, $like);
        }
        if ($cuisine = trim((string)($filters['cuisine'] ?? ''))) {
            $where[] = 'r.cuisine = ?';
            $params[] = $cuisine;
        }
        if ($category = trim((string)($filters['category'] ?? ''))) {
            $where[] = 'r.category = ?';
            $params[] = $category;
        }
        if ($ingredient = trim((string)($filters['ingredient'] ?? ''))) {
            $where[] = 'EXISTS (SELECT 1 FROM ingredients i2 WHERE i2.recipe_id = r.id AND i2.name LIKE ?)';
            $params[] = '%' . $ingredient . '%';
        }
        if ($diet = trim((string)($filters['diet'] ?? ''))) {
            $where[] = 'EXISTS (SELECT 1 FROM recipe_tags rtg JOIN tags t ON t.id = rtg.tag_id WHERE rtg.recipe_id = r.id AND t.name = ?)';
            $params[] = $diet;
        }
        if ($maxMinutes = (int)($filters['max_minutes'] ?? 0)) {
            $where[] = '(r.prep_minutes + r.cook_minutes) <= ?';
            $params[] = $maxMinutes;
        }

        $sort = match ($filters['sort'] ?? 'popular') {
            'rating' => 'avg_rating DESC, rating_count DESC',
            'newest' => 'r.published_at DESC',
            'quickest' => 'total_minutes ASC',
            default => 'favorite_count DESC, r.views DESC, avg_rating DESC',
        };

        $sql = $this->summarySql . ' WHERE ' . implode(' AND ', $where) . ' GROUP BY r.id ORDER BY ' . $sort . ' LIMIT 60';
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find(int $id, bool $allowUnapproved = false): ?array
    {
        $where = $allowUnapproved ? 'r.id = ?' : 'r.id = ? AND r.status = "approved"';
        $sql = $this->summarySql . ' WHERE ' . $where . ' GROUP BY r.id LIMIT 1';
        $stmt = db()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function ingredients(int $recipeId): array
    {
        $stmt = db()->prepare('SELECT * FROM ingredients WHERE recipe_id = ? ORDER BY sort_order, id');
        $stmt->execute([$recipeId]);
        return $stmt->fetchAll();
    }

    public function steps(int $recipeId): array
    {
        $stmt = db()->prepare('SELECT * FROM recipe_steps WHERE recipe_id = ? ORDER BY step_number');
        $stmt->execute([$recipeId]);
        return $stmt->fetchAll();
    }

    public function media(int $recipeId): array
    {
        $stmt = db()->prepare('SELECT * FROM recipe_media WHERE recipe_id = ? ORDER BY sort_order, id');
        $stmt->execute([$recipeId]);
        return $stmt->fetchAll();
    }

    public function tags(int $recipeId): array
    {
        $stmt = db()->prepare('SELECT t.name FROM tags t JOIN recipe_tags rt ON rt.tag_id = t.id WHERE rt.recipe_id = ? ORDER BY t.name');
        $stmt->execute([$recipeId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function nutrition(int $recipeId): ?array
    {
        $stmt = db()->prepare('SELECT * FROM nutrition WHERE recipe_id = ? LIMIT 1');
        $stmt->execute([$recipeId]);
        return $stmt->fetch() ?: null;
    }

    public function comments(int $recipeId): array
    {
        $stmt = db()->prepare('SELECT c.*, u.display_name, u.profile_image FROM comments c JOIN users u ON u.id = c.user_id WHERE c.recipe_id = ? AND c.status = "approved" ORDER BY c.created_at DESC');
        $stmt->execute([$recipeId]);
        return $stmt->fetchAll();
    }

    public function incrementViews(int $recipeId): void
    {
        $stmt = db()->prepare('UPDATE recipes SET views = views + 1 WHERE id = ?');
        $stmt->execute([$recipeId]);
    }

    public function byAuthor(int $authorId): array
    {
        $sql = $this->summarySql . ' WHERE r.author_id = ? GROUP BY r.id ORDER BY r.created_at DESC';
        $stmt = db()->prepare($sql);
        $stmt->execute([$authorId]);
        return $stmt->fetchAll();
    }
}
