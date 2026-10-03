<?php
require_once __DIR__.'/../config/bootstrap.php';
require_admin();
$pending=$db->query("SELECT n.*,u.full_name,s.course_code,s.title subject_title FROM notes n JOIN users u ON u.user_id=n.user_id JOIN subjects s ON s.subject_id=n.subject_id WHERE n.status='pending' ORDER BY n.created_at")->fetchAll();
$users=$db->query("SELECT user_id,full_name,email,role,status,created_at FROM users ORDER BY created_at DESC")->fetchAll();
$page_title='Admin Dashboard'; require __DIR__.'/../includes/header.php';
?>
<h1>Admin Dashboard</h1>
<div class="card"><h2>Pending Notes</h2>
<div class="table-wrap"><table class="table"><tr><th>Title</th><th>Uploader</th><th>Subject</th><th>Action</th></tr>
<?php foreach($pending as $n): ?><tr><td><?=e($n['title'])?></td><td><?=e($n['full_name'])?></td><td><?=e($n['course_code'])?></td><td>
<form method="post" action="moderate.php" style="display:inline"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=$n['note_id']?>"><input type="hidden" name="status" value="approved"><button class="button">Approve</button></form>
<form method="post" action="moderate.php" style="display:inline"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=$n['note_id']?>"><input type="hidden" name="status" value="rejected"><button class="button danger">Reject</button></form>
</td></tr><?php endforeach;?></table></div>
</div>
<div class="card"><h2>Users</h2><div class="table-wrap"><table class="table"><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th></tr><?php foreach($users as $u): ?><tr><td><?=e($u['full_name'])?></td><td><?=e($u['email'])?></td><td><?=e($u['role'])?></td><td><?=e($u['status'])?></td></tr><?php endforeach;?></table></div></div>
<?php require __DIR__.'/../includes/footer.php'; ?>
