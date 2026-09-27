<?php
declare(strict_types=1); require_once __DIR__.'/includes/functions.php'; require_login(); if($_SERVER['REQUEST_METHOD']!=='POST') redirect('index.php'); verify_csrf();
$following=(int)($_POST['following_id']??0); $me=(int)current_user()['id']; if(!$following||$following===$me) redirect('index.php');
$stmt=db()->prepare('SELECT id FROM follows WHERE follower_id=? AND following_id=?');$stmt->execute([$me,$following]);$id=$stmt->fetchColumn();
if($id){$stmt=db()->prepare('DELETE FROM follows WHERE id=?');$stmt->execute([$id]);flash('success','Creator unfollowed.');audit('UNFOLLOW_USER',$following);}else{$stmt=db()->prepare('INSERT INTO follows(follower_id,following_id) VALUES(?,?)');$stmt->execute([$me,$following]);notify($following,'follow',current_user()['display_name'].' started following you.',url('profile.php?id='.$me));flash('success','You are now following this creator.');audit('FOLLOW_USER',$following);} redirect('profile.php?id='.$following);
