<?php
declare(strict_types=1);
require_once __DIR__.'/includes/functions.php';
require_once __DIR__.'/classes/UserRepository.php';
require_login();
$repo=new UserRepository(); $profile=$repo->findById((int)current_user()['id']); $errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf(); $name=trim($_POST['display_name']??''); $bio=trim($_POST['bio']??''); $diet=trim($_POST['dietary_preferences']??'');
 if(mb_strlen($name)<2||mb_strlen($name)>80)$errors[]='Display name must be between 2 and 80 characters.';
 if(mb_strlen($bio)>500)$errors[]='Bio must be 500 characters or fewer.';
 $image=null;
 if(!$errors){ try{$image=save_upload($_FILES['profile_image']??[], 'profiles',['image/jpeg','image/png','image/webp'],3*1024*1024);}catch(Throwable $e){$errors[]=$e->getMessage();}}
 if(!$errors){$repo->updateProfile((int)current_user()['id'],$name,$bio,$diet,$image);$_SESSION['user']['display_name']=$name;audit('PROFILE_UPDATE',(int)current_user()['id']);flash('success','Profile updated.');redirect('profile.php');}
}
$pageTitle='Edit profile'; require __DIR__.'/includes/header.php';
?>
<section class="section container narrow"><div class="panel"><span class="eyebrow">Your profile</span><h1>Edit profile</h1><?php if($errors):?><div class="form-errors"><ul><?php foreach($errors as $x):?><li><?=e($x)?></li><?php endforeach;?></ul></div><?php endif;?><form method="post" enctype="multipart/form-data" class="form-stack"><?=csrf_field()?><label>Display name<input name="display_name" maxlength="80" required value="<?=e($_POST['display_name']??$profile['display_name'])?>"></label><label>Bio<textarea name="bio" maxlength="500" rows="5" placeholder="Tell the community what you love to cook."><?=e($_POST['bio']??$profile['bio'])?></textarea></label><label>Dietary preferences<input name="dietary_preferences" maxlength="255" value="<?=e($_POST['dietary_preferences']??$profile['dietary_preferences'])?>" placeholder="e.g. High-protein, vegetarian"></label><label>Profile picture<input type="file" name="profile_image" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG or WebP up to 3 MB.</small></label><button class="button button-primary">Save changes</button></form></div></section>
<?php require __DIR__.'/includes/footer.php'; ?>
