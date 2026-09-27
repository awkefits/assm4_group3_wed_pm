# CookBook — Online Food Recipe Sharing Platform

DWIN309 Assessment 4 working prototype built with **HTML, CSS, JavaScript, plain PHP and MySQL/MariaDB**. No PHP framework is used.

## 1. XAMPP installation

1. Copy the folder **COOKBOOK ONLINE FOOD RECIPE SHARING PLATFORM** to `C:\xampp\htdocs\`.
2. Start **Apache** and **MySQL** in XAMPP.
3. Open `http://localhost/phpmyadmin`.
4. Import `database/cookbook.sql`. The script creates a database named `cookbook` and inserts demo data.
5. The default database settings are in `config/config.php`:
   - host: `127.0.0.1`
   - port: `3306`
   - database: `cookbook`
   - username: `root`
   - password: blank
6. If your MySQL credentials differ, change `DB_USER` and `DB_PASS` in `config/config.php`.
7. Open:
   `http://localhost/COOKBOOK%20ONLINE%20FOOD%20RECIPE%20SHARING%20PLATFORM/`

The base URL is detected automatically, including a folder name containing spaces.

## 2. Demo credentials

All seeded accounts use **Password123!**

| Role | Email | Purpose |
|---|---|---|
| Admin | `admin@cookbook.test` | Moderation, user roles, site settings, audit logs |
| User | `maria@cookbook.test` | Creator with published Filipino recipes |
| User | `alex@cookbook.test` | Creator/follower/bookmark demo |
| User | `jamie@cookbook.test` | User with a pending recipe for moderation |

## 3. Important before submission

The assessment rubric requires group members and contributions to appear on the home page and footer. Replace the **Member 1 / Member 2 / Member 3** placeholders in:

- `index.php`
- `includes/footer.php`

with each student's **name, student ID and implemented modules**. Do not invent member details.

## 4. Main functions

- Secure registration, login, logout and prototype password recovery
- User/Admin role-based access control
- Profile and profile image editing
- Creator follow/unfollow and notifications
- Recipe create/edit/delete with moderation workflow
- Structured ingredients and numbered steps
- Cover image, image gallery, direct MP4/WebM attachment and external video link
- Cuisine/category/dietary/ingredient/time search filters
- Sort by popularity, rating, newest and quickest
- Recipe details, ingredients, steps, nutrition and media
- Per-serving nutrition calculation from full-recipe nutrition totals
- Favourites and bookmark collections/folders
- Ratings and community comments
- Email, Facebook, X, WhatsApp and copy-link sharing
- Explainable personalised recommendation page
- Admin recipe/comment moderation
- Admin user roles and account activation
- Sponsored/premium content labels and monetisation settings
- Audit logging and configurable site settings
- Responsive mobile, tablet and desktop interface
- Client-side usability enhancements plus server-side validation

## 5. Security implemented

- `password_hash()` / `password_verify()` for credentials
- PDO prepared statements with emulated prepares disabled
- CSRF tokens on state-changing forms
- Session ID regeneration after login
- HttpOnly / SameSite session cookie options
- Role checks for administrator routes
- Ownership checks before editing/deleting recipes/media
- Server-side file MIME validation and size limits
- HTML output escaping with `htmlspecialchars()`
- Database foreign keys and unique constraints
- Audit logging for significant user/admin actions
- Password reset tokens are random, hashed in the database and expire after one hour

The local password-recovery demo displays the one-time reset URL because XAMPP normally has no email transport configured. A production system would send this link by email and would not display it on screen.

## 6. Project structure

```text
COOKBOOK ONLINE FOOD RECIPE SHARING PLATFORM/
├── admin/              Admin dashboard and moderation
├── assets/
│   ├── css/            Responsive styling
│   ├── images/         Local SVG demo artwork
│   └── js/             Progressive UI interactions
├── classes/            Data access objects/repositories
├── config/             Application and PDO database config
├── database/           MySQL schema and seed data
├── docs/               Requirement matrix, diagrams and test plan
├── includes/           Shared helpers, header, footer and cards
├── tests/              DB smoke test for the local environment
├── uploads/            User-uploaded profiles/recipe media
└── *.php               Public pages and action controllers
```

## 7. Run the included database smoke test

After importing the database, open:

`http://localhost/COOKBOOK%20ONLINE%20FOOD%20RECIPE%20SHARING%20PLATFORM/tests/smoke_test.php`

It performs read-only checks against important database relationships and reports PASS/FAIL. Remove or restrict the tests folder before any real deployment.

## 8. Documentation supplied for the report

The `docs` folder contains:

- `HD_REQUIREMENT_MATRIX.md` — assessment requirement → implementation evidence
- `SYSTEM_DESIGN.md` — architecture, validation and security design notes
- `TEST_PLAN.md` — manual functional/security/responsive test cases
- `ERD.mmd` — Mermaid ERD source
- `DFD.mmd` — Mermaid data-flow diagram source
- `VIVA_GUIDE.md` — technical questions the team should be able to explain

These files are supporting notes; the assessment still requires the group to prepare and submit its own Microsoft Word report.
