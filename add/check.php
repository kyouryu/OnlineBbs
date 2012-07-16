<?php
session_start();
require_once ("../class/bbsConfig.php");
require_once ("../class/bbsDtbs.php");
require_once ("../class/authLogin.php");

$config = new BbsConfig();
$dtbs = new BbsDtbs();
$login = new AuthLogin();
$title = $config->session('title');
$contents = $config->session('contents');
$contents = $config->escape($contents);
//hiddenから取得
$user_id = $config->session('user_id');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["ok"])) {

        $dtbs->topicInsert($user_id, $title, $contents);
    }
}

//それぞれのセッションの値を代入する
$login_id = $config->session('login_id');
$login_time = $config->session('login_time');

//ログイン中のユーザー情報を呼び出す
$member = $login->nowLogin($login_id, $login_time);
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
                <h1>トピック内容確認</h1>
            </div>

            <div id="content">
                <div style="text-align: right"><a href="../logout.php">ログアウト</a></div>   
                <div style="text-align: right"><a href="../index.php">トピック一覧</a></div>
                <dl>
                    <p>以下の内容でトピックを作成します。よろしいですか？</p>
                    <dt>ニックネーム</dt>
                    <dd>
                        <?php echo $config->escape($member["user_name"]); ?>さん
                    </dd>
                    <dt>タイトル</dt>
                    <dd>
                        <?php echo $config->escape($title); ?>
                    </dd>
                    <dt>本文</dt>
                    <dd>
                        <?php echo nl2br($contents); ?>
                    </dd>
                </dl>

                <ul>
                    <li>
                        <form action="./form.php" method="post">
                            <div><p><input type="submit" name="ng" value="書き直す" /></p></div>

                        </form>
                    </li>
                    <li>
                        <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
                            <div><p><input type="submit" name="ok" value="作成する" /></p></div>
                        </form>
                    </li>
                </ul>

            </div>
            <div id="foot">
                <p><img src="../images/txt_copyright.png" width="136" height="15" alt="(C) H2O Space. MYCOM" /></p>
            </div>
        </div>
    </body>
</html>
