# High Distinction Requirement Matrix

This matrix links the DWIN309 business problems and functional requirements to concrete prototype evidence.

## Business problem specifications

| Problem | Implemented response | Evidence |
|---|---|---|
| Limited sharing options | Structured recipe editor with ingredients, ordered steps, cover image, gallery, direct video and external video; social/email/direct-link sharing | `recipe_form.php`, `recipe.php`, `recipe_media` |
| Poor discoverability | Keyword, ingredient, cuisine, category, dietary and total-time filters; rating/popularity/newest/quickest sorting | `browse.php`, `RecipeRepository::search()` |
| Low community engagement | Ratings, comments, follows and notifications | `rating_action.php`, `comment_action.php`, `follow_action.php`, `notifications.php` |
| Content quality control | New user recipes default to pending; admin approve/reject; comments can be hidden; audit history | `admin/recipes.php`, `admin/comments.php`, `audit_logs` |
| Lack of personalisation | Favourites, collection folders and rule-based recommendations from saved cuisines/followed creators/diet preferences | `bookmarks.php`, `recommendations.php` |
| Limited mobile support | Responsive CSS breakpoints, mobile navigation, touch-friendly controls and single-column cooking view | `assets/css/style.css`, `assets/js/app.js` |
| Monetisation challenges | Sponsored and premium recipe flags; transparent labels; admin monetisation settings | `admin/recipes.php`, `admin/settings.php`, recipe cards/details |

## Functional modules

### 1. User Management
- Registration: `register.php`
- Login/logout: `login.php`, `logout.php`
- Password recovery/reset: `forgot_password.php`, `reset_password.php`
- User/Admin access: `require_login()`, `require_admin()`, role field in `users`

### 2. User Profile
- Bio, dietary preferences and profile image: `profile.php`, `profile_edit.php`
- User's recipes: profile creator grid
- Saved recipes: favourites + `bookmarks.php`

### 3. Recipe Management
- Create/edit/delete: `recipe_form.php`
- Ingredients: `ingredients`
- Ordered method: `recipe_steps`
- Categories and cuisine: `recipes`
- Tags: `tags`, `recipe_tags`
- Photos/video: `recipe_media`, cover image and upload storage

### 4. Search and Browsing
- Ingredient/cuisine/category/diet filters: `browse.php`
- Keyword search: title, description and ingredients
- Sort: rating, popularity, date and quickest

### 5. Recipe Viewing
- Full recipe page: `recipe.php`
- Ingredient list, ordered cooking method, gallery and media
- Serving adjustment in JavaScript
- Creator details and recipe metadata

### 6. Favourite and Bookmark
- Favourite toggle: `favorite_action.php`
- Collection folders: `bookmark_folders`
- Saved recipe management: `bookmarks.php`

### 7. Sharing and Social Integration
- Email share (`mailto:`)
- Facebook share
- X share
- WhatsApp share
- Copy direct link using Clipboard API

### 8. Image and Media
- Cover upload
- Multiple gallery images
- Local MP4/WebM attachment
- External video URL
- Existing gallery media removal
- MIME and file-size validation

### 9. Nutrition
- Whole-recipe nutrition entry
- Automatic per-serving calculation using recipe serving count
- Calories, protein, carbohydrates, fat, fibre and sodium fields

### 10. Admin Dashboard
- Overview metrics: `admin/index.php`
- User roles/activation: `admin/users.php`
- Recipe moderation and sponsored/premium controls: `admin/recipes.php`
- Comment moderation: `admin/comments.php`
- Site configuration: `admin/settings.php`
- Audit trail: `admin/audit.php`

## Rubric evidence

- **Layout/design:** shared reusable header/footer; consistent responsive component system.
- **Home page:** business value, recipe discovery, team placeholders, DWIN309 statement in footer.
- **UX:** clear navigation, filter forms, empty states, validation messages, responsive design and Help page.
- **Back end:** PDO connection, repository classes, normalized relational schema, FKs/indexes/constraints.
- **Web technologies:** semantic HTML, vanilla CSS/JS, plain PHP and MySQL—no framework.
- **Code:** reusable helpers/partials/repositories, descriptive names, docblocks and separation of data access from views.
- **Database:** single import script creates schema and seed data with all major relationships.
- **Viva:** see `VIVA_GUIDE.md` for explanations the team should understand, not memorise mechanically.
