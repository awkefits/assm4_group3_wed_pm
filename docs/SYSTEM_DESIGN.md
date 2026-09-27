# System Design Notes

## Architecture

CookBook uses a simple layered architecture suitable for a plain-PHP assessment:

1. **Presentation layer** — PHP templates, semantic HTML and responsive CSS.
2. **Interaction layer** — vanilla JavaScript for mobile navigation, dynamic editor rows, copy-link and serving adjustment.
3. **Controller/action layer** — public/action PHP files validate input, authorise the request, perform the transaction and redirect.
4. **Data access layer** — PDO plus `UserRepository` and `RecipeRepository` encapsulate common database reads/writes.
5. **Persistence layer** — normalized MySQL/MariaDB tables with foreign keys, indexes and constraints.

No MVC/web framework is used.

## Major design assumptions

- A public visitor can browse only approved recipes.
- A registered user can publish, rate, comment, follow, favourite and organise bookmarks.
- A standard user's new/edited recipe returns to `pending` for moderation.
- An administrator can approve/reject recipes, hide/restore comments, manage roles/access, site settings and commercial labels.
- Password-reset email is simulated locally because standard XAMPP does not provide a configured mail service.
- Sponsored/premium status never changes the user rating calculation.

## Key validation

Server-side validation is authoritative. JavaScript/HTML constraints improve user experience but are not trusted for security.

Examples:
- valid email address; unique email database constraint
- password strength and confirmation
- recipe title/description/cuisine/category length checks
- enumerated difficulty/status values
- reasonable time/serving limits
- minimum two ingredients and two steps
- rating limited to 1–5
- comment size limited to 1000 characters
- file MIME and file-size checks
- URL validation for external videos

## Security trade-offs

### Prepared statements
PDO native prepared statements prevent user data from being interpreted as SQL syntax.

### Password storage
Passwords use PHP's adaptive `password_hash()` function. Plain-text passwords are never stored.

### CSRF protection
Every important POST action includes a per-session CSRF token and verifies it before a state change.

### XSS protection
Dynamic output is escaped using the `e()` wrapper around `htmlspecialchars()`.

### Authorisation
Admin routes call `require_admin()`. Recipe/media editing checks author ownership or administrator role before mutation.

### Upload security
Uploads are checked by MIME type with `finfo`, randomised server filenames and defined file-size limits. Production deployment should additionally store uploads outside the executable web root or block script execution in the upload directory.

### Auditability
Login outcomes, profile changes, recipe actions, moderation, role changes and settings changes are recorded in `audit_logs`.

## Performance

Indexes target common lookups: user role/status, recipe publication/discovery, ingredient name, comment recipe/status, notifications and password reset token. Discovery uses a maximum result size in the prototype.

## Mobile support

CSS breakpoints reorganise navigation, cards, editor layouts and recipe details for smaller screens. The recipe method becomes a single reading column and controls meet touch-friendly sizes.
