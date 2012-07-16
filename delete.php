<?php
session_start();
require_once ("./class/bbsConfig.php");
require_once ("./class/authLogin.php");

$config = new BbsConfig();
$login = new AuthLogin();

//それぞれのセッションの値を代入する
$login_id = $config->session('login_id');
$login_time = $config->session('login_time');



//ログイン中のユーザー情報を呼び出す
$member = $login->nowLogin($login_id,$login_time);

if(!isset($_REQUEST['topic_id']) && !isset($_REQUEST['comment_id'])) {
    $this->jump('login.php');
}

	if (isset($_REQUEST['topic_id'])) {
    $id = $_REQUEST['topic_id'];
   
	// 投稿を検査する
	$sql = sprintf('SELECT user_id FROM topics WHERE id=%d',
		mysql_real_escape_string($id)
	);
	$record = mysql_query($sql) or die(mysql_error());
	$table = mysql_fetch_assoc($record);
	if ($table['user_id'] == $member['id']) {
		// 削除
		mysql_query('DELETE FROM topics WHERE id=' . mysql_real_escape_string($id)) or die(mysql_error());
                $messe="トピックを削除しました";
        
        }
        }
       
        
        
        
        if (isset($_REQUEST['comment_id'])) {
    $id = $_REQUEST['comment_id'];
      
	// 投稿を検査する
	$sql = sprintf('SELECT * FROM status WHERE id=%d',
		mysql_real_escape_string($id)
	);
	$record = mysql_query($sql) or die(mysql_error());
	$table = mysql_fetch_assoc($record);
	if ($table['user_id'] == $member['id']) {
		// 削除
		mysql_query('DELETE FROM status WHERE id=' . mysql_real_escape_string($id)) or die(mysql_error());
              $file_names=array($table['file_name1'],$table['file_name2'],$table['file_name3']);
                foreach ($file_names as $file_name) {
                if(!empty($file_name)) {
                unlink('./thumbnails/'.$file_name );
                unlink('./upImages/'.$file_name );
                }
            

    }
                $messe="投稿内容を削除しました";
	}
        }
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>管理画面　削除ページ</title>
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta name="Keywords" content="" />
<meta name="Description" content="" />
<link href="./css/style.css" rel="stylesheet" type="text/css" media="all" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
</head>
<body>
<div id="wrap">
<div id="head">
<h1>コメント投稿</h1>
<div style="text-align: right"><a href="./logout.php">ログアウト</a></div>
</div>
<?php


?>
<div id="content">
<p><?php echo $messe ?></p>
<p><a href="./index.php">トピック一覧へ</a></p>
<!--トピックIDのリンクを設置-->
<?php

if (isset($_REQUEST['comment_id'])) :
    ?>

<p><a href="./topic.php?id=<?php echo $table['post_id'];?>">書き込んだトピックへ</a></p>
<?php 
endif;
?>
  </div>
<div id="foot">
                <p><img src="images/txt_copyright.png" width="136" height="15" alt="(C) H2O Space. MYCOM" /></p>
            </div>
        </div>
    </body>
</html>
