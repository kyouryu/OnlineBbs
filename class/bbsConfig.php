<?php

class BbsConfig  {

    //サイト名　サイト名を記述してください。
    public $citeName='かんたん掲示板';

//サイトURL
public $cite_url = 'http://localhost/online_bbs/';


     /*データベースに接続する関数*/
public function connectDb() {
mysql_connect('localhost', 'root', '1192911') or die('could not connect to DB: '.mysql_error());
mysql_select_db('kyouryu_online_bbs') or die('could not select DB: '.mysql_error());

}



  /*セッション変数を作成する関数*/
    public function set($name, $value) {
        return $_SESSION[$name] = $value;
    }
 
 
    
    /*セッション変数の値を取得する関数*/
    public function session($name, $default=null) {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
    }
        return $default;
    }

    
    /*セッション変数をリセットする関数*/
    public function remove($name) {
        unset($_SESSION[$name]);
    }

    /*セッション変数全てをリセットする*/
    public function clear() {
        $_SESSION = array();
    }
    
    /*post変数の値を取得する関数*/
    public function getpost($name, $default=null) {
        
        //値があるなら
        if (isset($_POST[$name])) {
            //その値を返す
            return $_POST[$name];
    }
        //ないなら「null」を返す
        return $default;
    }
    
    
    /*GET変数の値を取得する関数*/
    public function getrequest($name, $default=null) {
        if (isset($_REQUEST[$name])) {
            return $_REQUEST[$name];
    }
        return $default;
    }


    /*値をエスケープ処理する関数*/
    public function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    
//    SQL 文中で用いる文字列の特殊文字をエスケープする
  public function mysqlEscape($string) {
return mysql_real_escape_string($string);
}


//ジャンプ先
function jump($string) {
header('Location:'.$this->cite_url.$string);
exit;
}


   //パスワードをハッシュ化
public function hashPassword($password) {
        return sha1($password);
    }

    

    }
    
?>
