<?php
require_once __DIR__.'/config/bootstrap.php';
require_login();
$error='';
$subjects=$db->query("SELECT s.*, sem.name semester FROM subjects s JOIN semesters sem ON sem.semester_id=s.semester_id ORDER BY sem.name,s.course_code")->fetchAll();

if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    $title=trim($_POST['title']??'');
    $description=trim($_POST['description']??'');
    $subject_id=(int)($_POST['subject_id']??0);
    $file=$_FILES['note_file']??null;
    $allowed=['application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','image/jpeg','image/png'];
    if($title==='' || !$subject_id || !$file || $file['error']!==UPLOAD_ERR_OK) $error='Complete all required fields.';
    elseif($file['size']>10*1024*1024) $error='Maximum file size is 10 MB.';
    elseif(!in_array((new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']),$allowed,true)) $error='Unsupported file type.';
    else{
        $mime=(new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $ext=strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));
        $safe=bin2hex(random_bytes(16)).'.'.$ext;
        $dir=__DIR__.'/uploads';
        if(!is_dir($dir)) mkdir($dir,0755,true);
        move_uploaded_file($file['tmp_name'],$dir.'/'.$safe);
        $stmt=$db->prepare("INSERT INTO notes(user_id,subject_id,title,description,file_name,file_path,file_type,file_size) VALUES(?,?,?,?,?,?,?,?)");
        $stmt->execute([$_SESSION['user_id'],$subject_id,$title,$description,$file['name'],'uploads/'.$safe,$mime,$file['size']]);
        flash('success','Note uploaded and sent for admin approval.');
        redirect('index.php');
    }
}
$page_title='Upload Note'; require __DIR__.'/includes/header.php';
?>
<div class="card"><h2>Upload a Note</h2>
<?php if($error): ?><div class="alert error"><?=e($error)?></div><?php endif;?>
<form method="post" enctype="multipart/form-data" data-validate>
<input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
<div class="form-group"><label>Title</label><input required name="title" value="<?=old('title')?>"></div>
<div class="form-group"><label>Subject</label><select required name="subject_id"><option value="">Select subject</option><?php foreach($subjects as $s): ?><option value="<?=$s['subject_id']?>"><?=e($s['course_code'].' — '.$s['title'].' ('.$s['semester'].')')?></option><?php endforeach;?></select></div>
<div class="form-group"><label>Description</label><textarea name="description"><?=old('description')?></textarea></div>
<div class="form-group"><label>File (PDF, Word, JPG, PNG; max 10 MB)</label><input required type="file" name="note_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"></div>
<button class="button">Upload</button>
</form></div>
<?php require __DIR__.'/includes/footer.php'; ?>
