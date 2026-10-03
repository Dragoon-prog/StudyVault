<?php
require_once __DIR__.'/config/bootstrap.php';
$page_title='Browse Notes';

$q=trim($_GET['q']??'');
$semester=(int)($_GET['semester_id']??0);
$subject=(int)($_GET['subject_id']??0);

$semesters=$db->query("SELECT * FROM semesters ORDER BY name")->fetchAll();
$subjects=$db->query("SELECT * FROM subjects ORDER BY course_code")->fetchAll();

$sql="SELECT * FROM approved_notes WHERE 1=1";
$params=[];
if($q!==''){
    $sql.=" AND (title LIKE ? OR description LIKE ? OR course_code LIKE ? OR subject_title LIKE ?)";
    $term="%$q%"; $params=[$term,$term,$term,$term];
}
if($subject){
    $sql.=" AND course_code = (SELECT course_code FROM subjects WHERE subject_id=?)";
    $params[]=$subject;
}
if($semester){
    $sql.=" AND semester = (SELECT name FROM semesters WHERE semester_id=?)";
    $params[]=$semester;
}
$sql.=" ORDER BY created_at DESC";
$stmt=$db->prepare($sql); $stmt->execute($params); $notes=$stmt->fetchAll();

require __DIR__.'/includes/header.php';
?>
<section class="hero">
<h1>Find the notes you need.</h1>
<p>Search lecture notes, study guides, and review materials by keyword, subject, or semester.</p>
</section>

<form class="searchbar" method="get">
<input name="q" placeholder="Search notes or subject code..." value="<?=e($q)?>">
<select name="semester_id"><option value="0">All semesters</option><?php foreach($semesters as $s): ?><option value="<?=$s['semester_id']?>" <?=$semester==$s['semester_id']?'selected':''?>><?=e($s['name'])?></option><?php endforeach;?></select>
<select name="subject_id"><option value="0">All subjects</option><?php foreach($subjects as $s): ?><option value="<?=$s['subject_id']?>" <?=$subject==$s['subject_id']?'selected':''?>><?=e($s['course_code'].' — '.$s['title'])?></option><?php endforeach;?></select>
<button class="button">Search</button>
</form>

<div class="grid">
<?php foreach($notes as $note): ?>
<article class="card">
<h3><?=e($note['title'])?></h3>
<p class="note-meta"><?=e($note['course_code'])?> — <?=e($note['subject_title'])?></p>
<p><?=e($note['description'])?></p>
<p class="note-meta"><?=e($note['semester'])?> · <?=e($note['uploader'])?> · <?=date('M d, Y',strtotime($note['created_at']))?></p>
<a class="button" href="download.php?id=<?=$note['note_id']?>">Download</a>
</article>
<?php endforeach; ?>
</div>
<?php if(!$notes): ?><div class="card"><p>No approved notes found.</p></div><?php endif; ?>
<?php require __DIR__.'/includes/footer.php'; ?>
