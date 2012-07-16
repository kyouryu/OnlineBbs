<?php
session_start();
require_once ("./class/bbsConfig.php");
require_once ("./class/authLogin.php");

$config = new BbsConfig();
$login = new AuthLogin();
$user_name = "";
$password = "";

//クッキーが存在するなら
if (isset($_COOKIE['user_name'])) {
    $user_name = $_COOKIE['user_name'];
    $password = $_COOKIE['password'];

    $save = 'on';
    $errors = $login->login($user_name, $password, $save);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $user_name = $config->getpost('user_name');


    $password = $config->getpost('password');


    $save = $config->getpost('save');
    $save = $config->escape($save);
    //ログイン処理を開始する
    $errors = $login->login($user_name, $password, $save);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="./css/style.css" />
        <meta http-equiv="Content-Script-Type" content="text/javascript" />
        <title><?php echo $config->citeName; ?>｜ログイン</title>
    </head>

    <body>
        <div id="wrap">
            <div id="head">
                <h1>ログインする</h1>
            </div>
            <div id="content">
                <div id="lead">
                     <p>ニックネームとパスワードを記入してログインしてください。</p>
                    <p>入会手続きがまだの方はこちらからどうぞ。</p>
                    <p>&raquo;<a href="./user/form.php">入会手続きをする</a></p>

                </div>
                <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
                    <p class="error">
<?php
if (!empty($errors["login"])) {
    echo $errors["login"];
}
?>
                    </p>

                    <dl>
                        <dt>ニックネーム</dt>
                        <dd>
                            <input type="text" name="user_name" size="30" value="<?php echo $config->escape($user_name);
                        ; ?>" />
                        </dd>
                        <dt>パスワード</dt>
                        <dd>
                            <input type="password" name="password" size="30" value="<?php echo $config->escape($password); ?>" />
                        </dd>
                        <dt>ログイン情報の記録</dt>
                        <dd>
                            <input id="save" type="checkbox" name="save" value="on" />
                            <label for="save">次回からは自動的にログインする</label>
                        </dd>
                    </dl>
                    <div>
                        <input type="submit" value="ログインする" />
                    </div>
                </form>
            </div>
            <div id="foot">
                <p><img src="images/txt_copyright.png" width="136" height="15" alt="(C) H2O Space. MYCOM" /></p>
            </div>
        </div>
    </body>
</html>
