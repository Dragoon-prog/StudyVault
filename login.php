<?php
require_once __DIR__.'/config/bootstrap.php';
if(is_logged_in()) redirect('index.php');
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    $email=strtolower(trim($_POST['email']??'')); $password=$_POST['password']??'';
    $stmt=$db->prepare("SELECT * FROM users WHERE email=? LIMIT 1"); $stmt->execute([$email]); $user=$stmt->fetch();
    if(!$user || $user['status']==='blocked' || !password_verify($password,$user['password_hash'])) $error='Invalid email or password.';
    else{
        session_regenerate_id(true);
        $_SESSION['user_id']=$user['user_id']; $_SESSION['role']=$user['role']; $_SESSION['full_name']=$user['full_name'];
        redirect('index.php');
    }
}
$page_title='Login'; require __DIR__.'/includes/header.php';
?>
<div class="card"><h2>Login</h2>
<?php if($error): ?><div class="alert error"><?=e($error)?></div><?php endif;?>
<form method="post" data-validate>
<input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
<div class="form-group"><label>Email</label><input required type="email" name="email"></div>
<div class="form-group"><label>Password</label><input required type="password" name="password"></div>
<button class="button">Login</button>
</form></div>
<?php require __DIR__.'/includes/footer.php'; ?>
