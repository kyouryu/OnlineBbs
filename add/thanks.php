<?php
session_start();
require_once ("../class/bbsConfig.php");


$config = new BbsConfig();

$topic_id = $config->session('topic_id');
$topic_id=$config->escape($topic_id);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="../css/style.css" />
<title><?php echo $config->citeName; ?>｜新規トピック作成</title>
</head>

<body>
<div id="wrap">
<div id="head">
<h1>トピック作成完了</h1>
</div>
 
<div id="content">
     <div style="text-align: right"><a href="../logout.php">ログアウト</a></div>
<p>作成が完了しました。反映に時間がかかることがありますので、<br/>
    表示されていない場合は少々お待ちください。</p>
    <p><a href="../topic.php?id=<?php echo $topic_id ?>">投稿したトピックを確認する</a></p>
<p><a href="../index.php">トピックス一覧へ</a></p>
</div>
   <div id="foot">
                    <p><img src="../images/txt_copyright.png" width="136" height="15" alt="(C) H2O Space. MYCOM" /></p>
                </div>
            </div>
        
    </body>
</html>
