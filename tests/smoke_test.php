<?php
/** Read-only local database smoke test. Remove/restrict before deployment. */
declare(strict_types=1);
require_once __DIR__ . '/../config/db.php';
header('Content-Type: text/html; charset=utf-8');
$tests = [];
function check(string $name, callable $fn): void { global $tests; try { $ok=(bool)$fn(); $tests[]=[$name,$ok,$ok?'PASS':'FAIL']; } catch(Throwable $e){$tests[]=[$name,false,'ERROR: '.$e->getMessage()];} }
check('Database connection', fn()=>db()->query('SELECT 1')->fetchColumn()==1);
check('Users seeded', fn()=>(int)db()->query('SELECT COUNT(*) FROM users')->fetchColumn()>=4);
check('Approved recipes exist', fn()=>(int)db()->query('SELECT COUNT(*) FROM recipes WHERE status="approved"')->fetchColumn()>=4);
check('Pending moderation recipe exists', fn()=>(int)db()->query('SELECT COUNT(*) FROM recipes WHERE status="pending"')->fetchColumn()>=1);
check('Recipe 1 has ingredients', fn()=>(int)db()->query('SELECT COUNT(*) FROM ingredients WHERE recipe_id=1')->fetchColumn()>=2);
check('Recipe 1 has ordered steps', fn()=>(int)db()->query('SELECT COUNT(*) FROM recipe_steps WHERE recipe_id=1')->fetchColumn()>=2);
check('Ratings relationship works', fn()=>(float)db()->query('SELECT AVG(rating) FROM ratings WHERE recipe_id=1')->fetchColumn()>0);
check('Bookmark folders exist', fn()=>(int)db()->query('SELECT COUNT(*) FROM bookmark_folders')->fetchColumn()>0);
check('Foreign-key relationships join', fn()=>(int)db()->query('SELECT COUNT(*) FROM recipes r JOIN users u ON u.id=r.author_id')->fetchColumn()>=5);
check('Admin account exists', fn()=>(int)db()->query('SELECT COUNT(*) FROM users WHERE role="admin"')->fetchColumn()>=1);
?><!doctype html><html><head><meta charset="utf-8"><title>CookBook smoke test</title><style>body{font:16px system-ui;max-width:900px;margin:40px auto;padding:0 18px}table{border-collapse:collapse;width:100%}td,th{border:1px solid #ddd;padding:10px;text-align:left}.pass{color:#17623f}.fail{color:#8a2e2e}</style></head><body><h1>CookBook database smoke test</h1><p>This page is read-only and is intended for the local XAMPP test environment.</p><table><tr><th>Test</th><th>Result</th></tr><?php foreach($tests as [$name,$ok,$result]):?><tr><td><?=htmlspecialchars($name)?></td><td class="<?=$ok?'pass':'fail'?>"><?=htmlspecialchars($result)?></td></tr><?php endforeach;?></table></body></html>
