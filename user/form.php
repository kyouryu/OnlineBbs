<?php
session_start();
require_once ("../class/bbsConfig.php");
require_once ("../class/bbsDtbs.php");


$config = new BbsConfig();
$dtbs = new BbsDtbs();

//変数を初期化する
$user_name = "";
$password = "";


//フォームからPOSTによって要求された場合
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //POST変数の値が「check」なら
    if (!empty($_POST["check"])) {

//それぞれの値を取得する
        $user_name = $config->getpost('user_name');
    
    
    $password = $config->getpost('password');
    

        /*
         * 値をエラーチェックにかける
         * エラーがあるなら$errors変数に代入する
         */
        $errors = $dtbs->userCheck($user_name, $password);
    }

//POST変数の値が「ng」なら
    elseif (!empty($_POST["ng"])) {

        //それぞれのセッションの値を代入する
        $user_name = $config->session('user_name');
        $password = $config->session('password');
    }
}

$user_name = $config->escape($user_name);
$password = $config->escape($password);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="../css/style.css" />
        <title><?php echo $config->citeName; ?>｜新規登録</title>
    </head>
    <body>
<div id="wrap">
<div id="head">
<h1>新規会員登録</h1>
</div>

<div id="content">
                <p>次のフォームに必要事項をご記入ください。</p>
                <p>入会済みの方はこちらへ。</p>
                <p>&raquo;<a href="../login.php">ログイン</a></p>

                <!--自身のスクリプトまでのパスを表示する-->
                <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">

                    <dl>
                        <dt>ニックネーム<span class="required">必須【半角英数字で５〜１０文字以内】</span></dt>
                        <p class="error">

                            <!--エラーが空でないなら、エラーを表示する-->
<?php
if (!empty($errors["user_name"])) {
    echo $errors["user_name"];
}
?>
                        </p>
                        <dd>
                            <input type="text" name="user_name" size="20" maxlength="20" value="<?php 
                            //エスケープ処理をする
                            echo $user_name; 
                            ?>" />
                        </dd>
                        <dt>パスワード<span class="required">必須【半角英数字で６〜１０文字以内】</span></dt>
                        <p class="error">
                            <!--エラーが空でないなら、エラーを表示する-->
<?php
if (!empty($errors["password"])) {
    echo $errors["password"];
}
?>
                        </p>
                        <dd>
                            <input type="password" name="password" size="20" maxlength="20" value="<?php echo $password; ?>" />
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
