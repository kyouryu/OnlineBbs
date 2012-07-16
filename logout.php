<?php
/*以下はマニュアル作業*/
session_start();
//セッション情報を初期化
$_SESSION = array();
//セッションIDを保存する際にクッキーを使用していれば
if (ini_get("session.use_cookies")) {
//現在のセッション中のクッキー情報を配列に代入する
$params = session_get_cookie_params();
//過去の時間をセットすることでセッション中のクッキー情報を削除できる。
setcookie(session_name(), '', time() - 42000,
$params["path"], $params["domain"],
$params["secure"], $params["httponly"]
);
}
// 最終的に、セッションを破壊する
session_destroy();
// Cookie名をつけたのも削除
setcookie('login_id', '', time()-420000);
setcookie('user_name', '', time()-420000);
setcookie('password', '', time()-420000);
header('Location: login.php');
?>
