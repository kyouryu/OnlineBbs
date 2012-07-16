<?php
session_start();
require_once ("./class/bbsConfig.php");


$config = new BbsConfig();
$post_id =$config->session('post_id');
$post_id =$config->escape($post_id);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="./css/style.css" />
<title><?php echo $config->citeName; ?>｜投稿完了</title>
</head>

<body>
<div id="wrap">
<div id="head">
<h1>コメント投稿</h1>
<div style="text-align: right"><a href="./logout.php">ログアウト</a></div>
</div>

<div id="content">
<p>書き込みが完了しました。反映に時間がかかることがあります。</p>
<p><a href="./index.php">トピック一覧へ</a></p>
<!--トピックIDのリンクを設置-->
<p><a href="./topic.php?id=<?php echo $post_id;?>">投稿したトピックへ</a></p>
</div>

</div>
</body>
</html>