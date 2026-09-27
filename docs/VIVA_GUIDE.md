# Viva Preparation Guide

Each student should understand the code they claim as their contribution. Be ready to explain these topics using the actual project files.

1. **Why no framework?** The assessment explicitly prohibits web frameworks, so the solution uses plain PHP, PDO, HTML, CSS and JavaScript.
2. **How does the database connect?** `config/db.php` creates one PDO connection with `ERRMODE_EXCEPTION`, associative fetches and native prepared statements.
3. **How are passwords protected?** Registration uses `password_hash()`, login uses `password_verify()`.
4. **How is SQL injection reduced?** Dynamic values use prepared statements rather than string concatenation.
5. **What is CSRF?** A malicious site could submit actions using a logged-in user's session. CookBook includes and verifies a per-session token on POST actions.
6. **What is XSS?** Untrusted text could become executable HTML/JS. `e()` escapes output before rendering.
7. **How does moderation work?** Standard-user recipe submissions save with `pending`; public queries require `approved`; admin can approve/reject and the author receives a notification.
8. **How do recipe relationships work?** A recipe owns many ingredients, steps and media; tags are many-to-many using `recipe_tags`; deleting a recipe cascades to dependent rows.
9. **How does search work?** `RecipeRepository::search()` builds whitelisted filters and a whitelisted ORDER BY while binding user-supplied values.
10. **How are recommendations generated?** A transparent score boosts followed creators, cuisines from favourites and profile dietary-tag matches; rating remains separate.
11. **How are nutrition values calculated?** The creator enters totals for the whole recipe; PHP divides each total by the recipe serving count.
12. **How are uploads validated?** PHP `finfo` checks actual MIME type, file size is limited, names are randomised and only permitted image/video types are saved.
13. **Why separate repository classes?** They reduce repeated SQL and separate data access from page markup, improving maintainability and testability.
14. **How is mobile usability handled?** CSS media queries reorganise grids/forms/nav and JavaScript opens the mobile navigation without a UI library.
15. **What would be improved for production?** Real transactional email, object/cloud media storage, antivirus scanning, rate limiting, stronger CSP/security headers, pagination, automated tests, backups, monitoring and an external nutrition source if required.
