<?php
session_start();
require_once ("../class/bbsConfig.php");
require_once ("../class/bbsDtbs.php");


$config = new BbsConfig();
$dtbs = new BbsDtbs();


//それぞれのセッションの値を代入する
$user_name = $config->session('user_name');
$password = $config->session('password');

//フォームからPOSTによって要求された場合
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//POST変数の値が「check」なら    
    if (!empty($_POST["ok"])) {

//値をテーブルに追加する
        $dtbs->userInsert($user_name, $password);
    }
}

$user_name = $config->escape($user_name);
$password = $config->escape($password);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="../css/style.css" />
        <title><?php echo $config->citeName; ?>｜新規登録</title>
    </head>

    <body>
        <div id="wrap">
            <div id="head">
                <h1>会員内容確認</h1>
            </div>

            <div id="content">
                <p>記入した内容を確認して、「登録する」ボタンをクリックしてください</p>

                <dl>
                    <dt>ニックネーム</dt>
                    <dd>
<?php echo $user_name; ?>
                    </dd>

                    <dt>パスワード</dt>
                    <dd>
                        【表示されません】
                    </dd>

                </dl>

                <ul class="nasi">
                    <li>
                        <form action="./form.php" method="post">

                            <div><p><input type="submit" name="ng" value="書き直す" /></p></div>

                        </form>
                    </li>
                    <li>
                        <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">

                            <div><p><input type="submit" name="ok" value="登録する" /></p></div>
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
