# CookBook Test Plan

Record actual evidence/screenshots and pass/fail results when testing in XAMPP.

| ID | Area | Test | Expected result |
|---|---|---|---|
| T01 | Install | Import `cookbook.sql` in phpMyAdmin | All tables and seed rows import without error |
| T02 | Auth | Login with `maria@cookbook.test` / `Password123!` | User login succeeds |
| T03 | Auth | Login with wrong password | Generic error; no login |
| T04 | Auth | Register a new unique account | Account created with user role |
| T05 | Auth | Attempt duplicate email | Validation/database prevents duplicate |
| T06 | Recovery | Request password reset | One-time local demo reset link generated |
| T07 | Recovery | Use valid token and set strong password | Password changes; token becomes used |
| T08 | RBAC | Open `/admin/index.php` as normal user | Access denied |
| T09 | Profile | Update bio and profile image | Profile changes persist |
| T10 | Recipe | Create a recipe with 2+ ingredients and steps | Recipe saved as pending for user |
| T11 | Recipe | Submit recipe missing title | Server validation rejects it |
| T12 | Recipe | Upload invalid image type | Upload rejected |
| T13 | Recipe | Edit own recipe | Changes persist; user version is moderated |
| T14 | Recipe | Attempt to edit another user's recipe by URL | 403/access denied |
| T15 | Media | Upload multiple gallery images | Images appear on recipe/editor |
| T16 | Media | Upload MP4/WebM within limit | Video saved and playable |
| T17 | Media | Remove existing media | Media row/file removed |
| T18 | Search | Search “chicken” | Adobo appears from title/ingredient data |
| T19 | Search | Filter cuisine=Filipino | Only matching approved recipes shown |
| T20 | Search | Filter dietary tag=Vegetarian | Tagged recipes shown |
| T21 | Search | Sort highest rating | Result order follows calculated ratings |
| T22 | Favourite | Toggle favourite | Save count/state changes |
| T23 | Bookmark | Create folder and save recipe | Recipe appears under selected folder |
| T24 | Rating | Rate a recipe 1–5 | Unique rating saved/updated |
| T25 | Comment | Post valid comment | Comment visible and author notified |
| T26 | Follow | Follow another creator | Relationship saved; creator notified |
| T27 | Nutrition | Open recipe with nutrition | Per-serving values = totals / servings |
| T28 | Share | Copy link | Clipboard receives recipe URL |
| T29 | Share | Open social/email share | Correct recipe URL is included |
| T30 | Recommendation | Open “For you” after saving/following | Recommendations are ranked by user signals |
| T31 | Admin | Approve pending recipe | Status becomes approved and user notified |
| T32 | Admin | Reject recipe | Status becomes rejected and user notified |
| T33 | Admin | Hide comment | Comment disappears from public view |
| T34 | Admin | Change normal user to admin | Role persists; admin access becomes available |
| T35 | Admin | Deactivate user | User can no longer authenticate |
| T36 | Audit | Perform moderation action | Audit row records admin/action/time/IP |
| T37 | Responsive | Test at 375px width | Navigation/cards/forms fit without horizontal overflow |
| T38 | Responsive | Test recipe at phone width | Steps are clear in single column |
| T39 | Security | POST without/incorrect CSRF token | Request is rejected with security message |
| T40 | Security | Enter `<script>` in text field | Output rendered as text, not executed |
