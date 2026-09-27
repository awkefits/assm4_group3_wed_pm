-- CookBook Online Food Recipe Sharing Platform
-- DWIN309 Assessment 4 prototype schema (MySQL/MariaDB)
-- Import this file using phpMyAdmin. No framework is required.

CREATE DATABASE IF NOT EXISTS cookbook CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cookbook;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS audit_logs, notifications, password_resets, recipe_media, nutrition, bookmarks, bookmark_folders, favorites, follows, comments, ratings, recipe_tags, tags, recipe_steps, ingredients, recipes, site_settings, users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(190) NOT NULL UNIQUE,
    display_name VARCHAR(80) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user','admin') NOT NULL DEFAULT 'user',
    bio VARCHAR(500) NULL,
    profile_image VARCHAR(255) NULL,
    dietary_preferences VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_users_role_active (role, is_active)
) ENGINE=InnoDB;

CREATE TABLE recipes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    author_id INT UNSIGNED NOT NULL,
    title VARCHAR(150) NOT NULL,
    slug VARCHAR(190) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    cuisine VARCHAR(80) NOT NULL,
    category VARCHAR(80) NOT NULL,
    difficulty ENUM('Easy','Medium','Hard') NOT NULL DEFAULT 'Easy',
    prep_minutes SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    cook_minutes SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    servings SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    cover_image VARCHAR(255) NULL,
    video_url VARCHAR(500) NULL,
    status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    is_sponsored TINYINT(1) NOT NULL DEFAULT 0,
    is_premium TINYINT(1) NOT NULL DEFAULT 0,
    views INT UNSIGNED NOT NULL DEFAULT 0,
    published_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_recipes_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_recipes_status_publish (status, published_at),
    INDEX idx_recipes_discovery (cuisine, category, difficulty),
    FULLTEXT INDEX ft_recipe_text (title, description)
) ENGINE=InnoDB;

CREATE TABLE ingredients (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT UNSIGNED NOT NULL,
    name VARCHAR(120) NOT NULL,
    quantity VARCHAR(30) NULL,
    unit VARCHAR(40) NULL,
    notes VARCHAR(180) NULL,
    image_path VARCHAR(255) NULL,
    sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    CONSTRAINT fk_ingredients_recipe FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    INDEX idx_ingredient_name (name),
    INDEX idx_ingredient_recipe (recipe_id, sort_order)
) ENGINE=InnoDB;

CREATE TABLE recipe_steps (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT UNSIGNED NOT NULL,
    step_number SMALLINT UNSIGNED NOT NULL,
    instruction TEXT NOT NULL,
    image_path VARCHAR(255) NULL,
    CONSTRAINT fk_steps_recipe FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    UNIQUE KEY uq_recipe_step (recipe_id, step_number)
) ENGINE=InnoDB;

CREATE TABLE tags (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE recipe_tags (
    recipe_id INT UNSIGNED NOT NULL,
    tag_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (recipe_id, tag_id),
    CONSTRAINT fk_recipe_tags_recipe FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    CONSTRAINT fk_recipe_tags_tag FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE ratings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    recipe_id INT UNSIGNED NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT chk_rating CHECK (rating BETWEEN 1 AND 5),
    CONSTRAINT fk_ratings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_ratings_recipe FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    UNIQUE KEY uq_user_recipe_rating (user_id, recipe_id)
) ENGINE=InnoDB;

CREATE TABLE comments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    recipe_id INT UNSIGNED NOT NULL,
    body VARCHAR(1000) NOT NULL,
    status ENUM('approved','hidden') NOT NULL DEFAULT 'approved',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_comments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_comments_recipe FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    INDEX idx_comments_recipe_status (recipe_id, status, created_at)
) ENGINE=InnoDB;

CREATE TABLE follows (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    follower_id INT UNSIGNED NOT NULL,
    following_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_follows_follower FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_follows_following FOREIGN KEY (following_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uq_follow (follower_id, following_id)
) ENGINE=InnoDB;

CREATE TABLE favorites (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    recipe_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_favorites_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_favorites_recipe FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    UNIQUE KEY uq_favorite (user_id, recipe_id)
) ENGINE=InnoDB;

CREATE TABLE bookmark_folders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    name VARCHAR(80) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_folders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uq_folder_name (user_id, name)
) ENGINE=InnoDB;

CREATE TABLE bookmarks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    folder_id INT UNSIGNED NOT NULL,
    recipe_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_bookmarks_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_bookmarks_folder FOREIGN KEY (folder_id) REFERENCES bookmark_folders(id) ON DELETE CASCADE,
    CONSTRAINT fk_bookmarks_recipe FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    UNIQUE KEY uq_bookmark_folder_recipe (user_id, folder_id, recipe_id)
) ENGINE=InnoDB;

CREATE TABLE nutrition (
    recipe_id INT UNSIGNED PRIMARY KEY,
    calories_total DECIMAL(10,1) NOT NULL DEFAULT 0,
    protein_g_total DECIMAL(10,1) NOT NULL DEFAULT 0,
    carbs_g_total DECIMAL(10,1) NOT NULL DEFAULT 0,
    fat_g_total DECIMAL(10,1) NOT NULL DEFAULT 0,
    fibre_g_total DECIMAL(10,1) NOT NULL DEFAULT 0,
    sodium_mg_total DECIMAL(10,1) NOT NULL DEFAULT 0,
    notes VARCHAR(255) NULL,
    CONSTRAINT fk_nutrition_recipe FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE recipe_media (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT UNSIGNED NOT NULL,
    media_type ENUM('image','video','external_video') NOT NULL,
    file_path VARCHAR(255) NULL,
    external_url VARCHAR(500) NULL,
    sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_media_recipe FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE password_resets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    token_hash CHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reset_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_reset_token (token_hash, expires_at)
) ENGINE=InnoDB;

CREATE TABLE notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    type VARCHAR(30) NOT NULL,
    message VARCHAR(255) NOT NULL,
    link_url VARCHAR(500) NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    read_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_notifications_user_read (user_id, is_read, created_at)
) ENGINE=InnoDB;

CREATE TABLE site_settings (
    setting_key VARCHAR(80) PRIMARY KEY,
    setting_value TEXT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    action VARCHAR(80) NOT NULL,
    target_id INT UNSIGNED NULL,
    details VARCHAR(500) NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_audit_time_action (created_at, action)
) ENGINE=InnoDB;

-- Demo password for every seeded account: Password123!
INSERT INTO users (id,email,display_name,password_hash,role,bio,dietary_preferences) VALUES
(1,'admin@cookbook.test','CookBook Admin','$2y$12$uBTlliHVeO0258gTmgMc7OJ5enNB5JJHhF9V2236m6mooZojZ3Q.i','admin','Platform administrator responsible for moderation and community safety.',NULL),
(2,'maria@cookbook.test','Maria Santos','$2y$12$uBTlliHVeO0258gTmgMc7OJ5enNB5JJHhF9V2236m6mooZojZ3Q.i','user','Sydney-based Filipino home cook sharing practical family meals.','High-protein'),
(3,'alex@cookbook.test','Alex Green','$2y$12$uBTlliHVeO0258gTmgMc7OJ5enNB5JJHhF9V2236m6mooZojZ3Q.i','user','I like simple plant-forward meals and reliable weeknight recipes.','Vegetarian'),
(4,'jamie@cookbook.test','Jamie Lee','$2y$12$uBTlliHVeO0258gTmgMc7OJ5enNB5JJHhF9V2236m6mooZojZ3Q.i','user','Learning one recipe at a time.','Dairy-Free');

INSERT INTO recipes (id,author_id,title,slug,description,cuisine,category,difficulty,prep_minutes,cook_minutes,servings,cover_image,status,is_sponsored,is_premium,views,published_at) VALUES
(1,2,'Weeknight Chicken Adobo','weeknight-chicken-adobo','A balanced Filipino chicken adobo with soy, vinegar, garlic and peppercorns. The method is structured for an easy weeknight dinner and works well with steamed rice.','Filipino','Dinner','Easy',15,35,4,'assets/images/adobo.svg','approved',0,0,382,NOW()),
(2,2,'Crispy Tofu Sisig Bowl','crispy-tofu-sisig-bowl','A lighter meat-free sisig-inspired bowl with crisp tofu, onion, chilli and a creamy citrus dressing. It is filling, affordable and simple to prepare.','Filipino','Lunch','Medium',20,25,4,'assets/images/tofu-sisig.svg','approved',0,0,241,NOW()),
(3,3,'Mediterranean Chickpea Power Bowl','mediterranean-chickpea-power-bowl','A colourful vegetarian bowl with chickpeas, roasted vegetables, cucumber and lemon tahini. Great for meal preparation and flexible toppings.','Mediterranean','Lunch','Easy',15,25,4,'assets/images/chickpea-bowl.svg','approved',1,0,529,NOW()),
(4,3,'Miso Glazed Salmon & Greens','miso-glazed-salmon-greens','Fast salmon with a savoury miso glaze, steamed greens and sesame. Designed for a simple high-protein dinner.','Japanese','Dinner','Easy',10,20,2,'assets/images/salmon.svg','approved',0,1,196,NOW()),
(5,4,'Banana Oat Breakfast Cups','banana-oat-breakfast-cups','Portable oat cups made with banana, rolled oats and cinnamon. This new community submission is included to demonstrate moderation workflow.','Australian','Breakfast','Easy',10,20,6,'assets/images/oat-cups.svg','pending',0,0,0,NULL);

INSERT INTO ingredients (recipe_id,name,quantity,unit,notes,sort_order) VALUES
(1,'Chicken thigh','700','g','boneless, cut into pieces',1),(1,'Soy sauce','1/3','cup','',2),(1,'Cane vinegar','1/3','cup','',3),(1,'Garlic','6','cloves','crushed',4),(1,'Bay leaves','2','','',5),(1,'Black peppercorns','1','tsp','',6),(1,'Water','1/2','cup','',7),
(2,'Firm tofu','500','g','pressed and cubed',1),(2,'Red onion','1','medium','finely diced',2),(2,'Red chilli','1','','sliced',3),(2,'Light mayonnaise','3','tbsp','',4),(2,'Calamansi or lemon juice','2','tbsp','',5),(2,'Soy sauce','1','tbsp','',6),
(3,'Chickpeas','2','cups','cooked and drained',1),(3,'Capsicum','1','large','sliced',2),(3,'Zucchini','1','medium','sliced',3),(3,'Cucumber','1','medium','diced',4),(3,'Tahini','3','tbsp','',5),(3,'Lemon juice','2','tbsp','',6),(3,'Olive oil','1','tbsp','',7),
(4,'Salmon fillets','2','','about 150 g each',1),(4,'White miso','1.5','tbsp','',2),(4,'Soy sauce','1','tbsp','',3),(4,'Honey','1','tsp','',4),(4,'Broccoli','250','g','steamed',5),(4,'Sesame seeds','1','tsp','',6),
(5,'Ripe bananas','2','','mashed',1),(5,'Rolled oats','2','cups','',2),(5,'Milk','1','cup','',3),(5,'Cinnamon','1','tsp','',4);

INSERT INTO recipe_steps (recipe_id,step_number,instruction) VALUES
(1,1,'Combine chicken, soy sauce, vinegar, garlic, bay leaves, peppercorns and water in a saucepan.'),(1,2,'Bring to a gentle boil, then lower the heat and simmer uncovered for about 25 minutes.'),(1,3,'Turn the chicken pieces and continue cooking until tender and the sauce has reduced.'),(1,4,'Taste the sauce, adjust seasoning if needed, and serve with rice or vegetables.'),
(2,1,'Pat the tofu dry and air-fry or pan-fry until golden and crisp on the edges.'),(2,2,'Mix onion, chilli, mayonnaise, citrus juice and soy sauce in a large bowl.'),(2,3,'Fold the hot crispy tofu through the dressing until evenly coated.'),(2,4,'Serve immediately in bowls with rice, lettuce or steamed vegetables.'),
(3,1,'Heat the oven to 210°C. Toss capsicum and zucchini with olive oil and roast until lightly browned.'),(3,2,'Whisk tahini with lemon juice and enough water to create a pourable dressing.'),(3,3,'Divide chickpeas, roasted vegetables and cucumber between bowls.'),(3,4,'Drizzle with lemon tahini and add optional herbs before serving.'),
(4,1,'Mix miso, soy sauce and honey to make the glaze.'),(4,2,'Brush the glaze over the salmon and bake or air-fry until just cooked through.'),(4,3,'Steam the broccoli until tender-crisp.'),(4,4,'Serve the salmon with greens and finish with sesame seeds.'),
(5,1,'Mix mashed banana, oats, milk and cinnamon.'),(5,2,'Divide into a greased muffin tray and bake at 180°C until set and lightly golden.');

INSERT INTO tags (id,name) VALUES (1,'High Protein'),(2,'Vegetarian'),(3,'Dairy-Free'),(4,'Meal Prep'),(5,'Under 30 Minutes'),(6,'Filipino'),(7,'Gluten-Free');
INSERT INTO recipe_tags VALUES (1,1),(1,3),(1,6),(2,2),(2,6),(3,2),(3,4),(4,1),(4,5),(5,2),(5,4);

INSERT INTO nutrition (recipe_id,calories_total,protein_g_total,carbs_g_total,fat_g_total,fibre_g_total,sodium_mg_total,notes) VALUES
(1,1720,136,34,102,4,4800,'Approximate values for the whole recipe.'),
(2,1180,74,46,74,13,2100,'Approximate values for the whole recipe.'),
(3,1680,54,210,68,46,1420,'Approximate values for the whole recipe.'),
(4,860,72,22,50,8,1780,'Approximate values for the whole recipe.');

INSERT INTO ratings (user_id,recipe_id,rating) VALUES (3,1,5),(4,1,5),(2,3,4),(4,3,5),(2,4,5),(3,2,4),(4,2,5);
INSERT INTO comments (user_id,recipe_id,body,status) VALUES
(3,1,'The reduced sauce was really good. I added steamed broccoli on the side.','approved'),
(4,1,'Easy to follow for a first adobo attempt. The numbered steps helped a lot.','approved'),
(2,3,'Great meal-prep idea. The lemon tahini works well with extra cucumber.','approved');
INSERT INTO favorites (user_id,recipe_id) VALUES (3,1),(4,1),(2,3),(4,3),(2,4),(3,4),(4,2);
INSERT INTO bookmark_folders (id,user_id,name) VALUES (1,3,'Weeknight dinners'),(2,3,'Meal prep'),(3,2,'Healthy ideas');
INSERT INTO bookmarks (user_id,folder_id,recipe_id) VALUES (3,1,1),(3,1,4),(3,2,3),(2,3,3),(2,3,4);
INSERT INTO follows (follower_id,following_id) VALUES (3,2),(4,2),(2,3);
INSERT INTO notifications (user_id,type,message,link_url,is_read) VALUES (2,'follow','Alex Green started following you.','profile.php?id=3',0),(2,'comment','Alex Green commented on “Weeknight Chicken Adobo”.','recipe.php?id=1',0),(1,'moderation','Recipe “Banana Oat Breakfast Cups” is awaiting moderation.','admin/recipes.php',0);

INSERT INTO site_settings (setting_key,setting_value) VALUES
('site_tagline','Cook. Share. Discover.'),
('moderation_message','Community recipes are reviewed before public publication.'),
('sponsor_notice','Sponsored recipes are clearly labelled and remain subject to moderation.'),
('premium_enabled','1'),
('ads_enabled','1');

INSERT INTO audit_logs (user_id,action,target_id,details,ip_address) VALUES
(1,'SYSTEM_SEED',NULL,'Demo database imported','127.0.0.1'),
(4,'RECIPE_CREATE',5,'Status: pending','127.0.0.1');
