<?php
session_start();
//関数ファイルを読み込む
require_once ("../class/bbsConfig.php");

$config = new BbsConfig();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="../css//style.css" />
<title><?php echo $config->citeName; ?>｜新規登録</title>
</head>

<body>
<div id="wrap">
<div id="head">
<h1>会員登録完了</h1>
</div>

<div id="content">
<p>会員登録が完了しました</p>
<p><a href="../login.php">ログインへ</a></p>
</div>

<div id="foot">
<p><img src="../images/txt_copyright.png" width="136" height="15" alt="(C) H2O Space. MYCOM" /></p>
</div>

</div>
</body>
</html>
