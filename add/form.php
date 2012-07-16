<?php
session_start();
require_once ("../class/bbsConfig.php");
require_once ("../class/bbsDtbs.php");
require_once ("../class/authLogin.php");

$config = new BbsConfig();
$dtbs = new BbsDtbs();
$login = new AuthLogin();
$title = "";
$contents = "";


//それぞれのセッションの値を代入する
$login_id = $config->session('login_id');
$login_time = $config->session('login_time');

//ログイン中のユーザ情報を取得する


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST["check"])) {
        $user_name = $config->getpost('user_name');
        $user_id = $config->getpost('user_id');
        $title = $config->getpost('title');

        $contents = $config->getpost('contents');

        $errors = $dtbs->topicCheck($user_id, $title, $contents);
    } elseif (!empty($_POST["ng"])) {
        $user_id = $config->session('user_id');
        $title = $config->session('title');
        $contents = $config->session('contents');
    }
}

$member = $login->nowLogin($login_id, $login_time);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="../css/style.css" />
        <title><?php echo $config->citeName; ?>｜新規トピック</title>
    </head>
    <body>
        <div id="wrap">
            <div id="head">
                <h1>新規トピック作成</h1>
            </div>
            
            <div id="content">
                <div style="text-align: right"><a href="../logout.php">ログアウト</a></div>   
            <div style="text-align: right"><a href="../index.php">トピック一覧</a></div>
                <p>次のフォームに必要事項をご記入ください。</p>
                <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
                    <input type="hidden" name="user_id" value="<?php echo $member['id'] ?>" />
                    <dl>
                        <dt>ニックネーム</dt>
                        <dd>
<?php echo $member['user_name'] ?>さん
                        </dd>
                        <dt>タイトル<span class="required">（全角20文字以内）</span></dt>
                        <p class="error">
                            <?php
                            if (!empty($errors["title"])) {
                                echo $errors["title"];
                            }
                            ?>
                        </p>
                        <dd>
                            <input type="text" name="title" size="60" value="<?php echo $config->escape($title); ?>" />
                        </dd>
                        <dt>本文<span class="required">（全角140文字以内）</span></dt>
                        <p class="error">
<?php
if (!empty($errors["contents"])) {
    echo $errors["contents"];
}
?>
                        </p>
                        <dd>
                            <textarea name="contents" cols="50" rows="5"><?php echo $config->escape($contents); ?></textarea>
                        </dd>

                    </dl>
                    <div><p><input type="submit" name="check" value="入力内容を確認する" /></p></div>
                </form>
                 </div>
       <div id="foot">
                    <p><img src="../images/txt_copyright.png" width="136" height="15" alt="(C) H2O Space. MYCOM" /></p>
                </div>
        </div>
    </body>
</html>
