<?php
session_start();
require_once ("./class/bbsConfig.php");
require_once ("./class/bbsDtbs.php");
require_once ("./class/authLogin.php");

$config = new BbsConfig();
$dtbs = new BbsDtbs();
$login = new AuthLogin();

//画像の保管場所へのパス
$updir = "./thumbnails/";

//それぞれのセッションの値を代入する
$title = $config->session('title');
$comment = $config->session('comment');
$comment = $config->escape($comment);
$images = $config->session('images');

//hiddenから受け取った値
$user_id = $config->session('user_id');
$post_id = $config->session('post_id');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["ok"])) {
        $dtbs->statusInsert($comment, $user_id, $post_id, $images);
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
        <link rel="stylesheet" type="text/css" href="./css/style.css" />
        <title><?php echo $config->citeName; ?> | 新規コメント</title>
    </head>

    <body>
        <div id="wrap">
            <div id="head">
                <h1><?php echo $config->escape($title); ?> | トピック</h1>
            </div>
            <div id="content">
                <div style="text-align: right"><a href="./logout.php">ログアウト</a></div>
                <p><?php echo $config->escape($member["user_name"]); ?>さん</p>
                <p>以下の内容で投稿します。よろしいですか？</p>
                <dl>
                    <dt>コメント</dt>
                    <dd>
                        <?php echo nl2br($comment); ?>  
                    </dd>
                    <dt>写真など</dt>
                    <dd>
                        <?php if (count($images) > 0): ?>

                            <!--画像をあるだけ表示していく-->
                            <?php foreach ($images as $file) : ?>


                                <img src="<?php echo $updir . $file; ?>" alt="" />

                                <?php
                            endforeach;
                            ?>
                        <?php endif; ?>
                    </dd>
                </dl>
                <ul class="nasi">
                    <li>
                        <form action="./topic.php?id=<?php echo $post_id; ?>" method="post">
                            <div><p><input type="submit" name="ng" value="書き直す" /></p></div>
                        </form>
                    </li>
                    <li>
                        <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
                            <div><p><input type="submit" name="ok" value="投稿する" /></p></div>
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
