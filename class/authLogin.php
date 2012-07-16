<?php

class AuthLogin extends BbsConfig {

       /* ログイン処理をする*/
    public function login($user_name, $password, $save) {

        $this->connectDb();
        $errors = array();

        //ユーザー名とパスが空でないなら
        if ($user_name !== '' && $password !== '') {
            //ユーザ名とパスに一致するユーザー情報を取得する
            $password1 = $this->hashPassword($password);
            $sql = sprintf('SELECT * FROM users WHERE user_name="%s" AND password="%s"', $this->mysqlEscape($user_name), $this->mysqlEscape($password1)
            );
            $record = mysql_query($sql) or die('データベースに接続できません：' . mysql_error());
            $table = mysql_fetch_assoc($record);

            //一致したidが存在するなら
            if (isset($table['id'])) {
                // ログイン成功
                $_SESSION['login_id'] = $table['id'];
                //現時刻のセッションを作成する
                $_SESSION['login_time'] = time();

                // セーブチェックボックスがonならログイン情報を記録する
                if ($save === 'on') {
                    
                    setcookie('user_name', $user_name, time()+60*60*24*14);
//ハッシュ化前のパス
                    setcookie('password', $password, time()+60*60*24*14);

                }
                 
                $this->jump('index.php');
                //一致したIDが存在しないなら
            } else {

                $errors["login"] = "ニックネームまたはパスワードが違います";
                return $errors;
            }
            
            //どちらかが空なら
        } else {
            $errors["login"] = "ニックネームまたはパスワードが違います";
            return $errors;
            }
          
        }
    

    
    /* 現在ログイン中かどうかを判断し、ユーザー情報を取得*/
    public function nowLogin($login_id, $login_time) {
     $this->connectDb();
        //ログインidが存在し、ログイン時間+60分が現在の時間より大きいなら
        if (isset($login_id) && $login_time +3600 > time()) {
            
            // 現在の時刻を代入
            $login_time = time();

            //ログインidの持ち主の情報を取得する
            $sql = sprintf('SELECT * FROM users WHERE id="%s"',$this->mysqlEscape($login_id)
            );
            $record = mysql_query($sql) or die(mysql_error());
            //ユーザー情報を取得
            $member = mysql_fetch_assoc($record);
            return $member;
        } else {
           $this->jump('login.php');
            exit;
    }
    }
}
?>
