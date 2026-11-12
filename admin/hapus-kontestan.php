<?php
require_once __DIR__.'/../config/bootstrap.php';require_admin();
if($_SERVER['REQUEST_METHOD']!=='POST')redirect('admin/kontestan.php');verify_csrf();$id=(int)($_POST['id']??0);
try{$stmt=$pdo->prepare('DELETE FROM kontestan WHERE id=?');$stmt->execute([$id]);flash('success','Kontestan berhasil dihapus.');}catch(PDOException $e){flash('error','Kontestan tidak dapat dihapus karena sudah memiliki suara.');}
redirect('admin/kontestan.php');
