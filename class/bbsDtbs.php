<?php

 class BbsDtbs extends BbsConfig {
       //表示件数
     private $pagelength_value=array(5,10,20,50,100,200);
     

    /*ユーザーのエラーチェックをする*/
    public function userCheck($user_name, $password) {
         $this->connectDb();
        $errors = array();
     
        //文字列がないなら
        if (!strlen($user_name)) {
            $errors["user_name"] = "名前を入力してください<br>";
            //半角英数字で５〜１０文字以内
        } else if (!preg_match('/^[a-zA-Z0-9]{5,10}+$/', $user_name)) {
            $errors["user_name"] = "名前は正しく入力してください<br>";
        }
        if (!strlen($password)) {
            $errors["password"] = "パスワードを入力してください<br>";
             //半角英数字で６〜１０文字以内
        } else if (!preg_match('/^[a-zA-Z0-9]{6,10}+$/', $password)) {
            $errors["password"] = "パスワードは正しく入力してください<br>";
        }

//名前の重複チェック
        $sql = sprintf("select user_name from users where user_name='%s'", $this->mysqlEscape($user_name)
        );

        $record = mysql_query($sql) or die(mysql_error());
        $count = mysql_num_rows($record);
        if ($count > 0) {
            $errors["user_name"] = "すでに登録されています<br>";
        }




//上記の処理でエラーがなかったら 
        if (count($errors) === 0) {
            //セッションを作成
            $this->set('user_name', $user_name);
            $this->set('password', $password);
            $this->jump('user/check.php');
            exit;
            //エラーがあれば
        } else {
            //エラー配列を返す
            return $errors;
        }
    }

    
    /* usersテーブルへデータを追加する*/
    public function userInsert($user_name, $password) {
         $this->connectDb();


        $password = $this->hashPassword($password);


        $sql = sprintf("insert into users (user_name, password,created_at) values ('%s', '%s',now())",
               $this->mysqlEscape($user_name), $this->mysqlEscape($password)
        );
        //保存する
        mysql_query($sql);

        //セッションを削除
        $this->remove('user_name');
        $this->remove('password');
        $this->clear();

      $this->jump('user/thanks.php');
       
        exit;
    }

   

   
     
    /*トピックのエラーチェックを行う関数*/
    public function topicCheck($user_id, $title, $contents) {
         $this->connectDb();
        //配列を初期化する
        $errors = array();
        
        //タイトルの文字列を取得できないなら
        if (!strlen($title)) {
            $errors["title"] = "タイトルを入力してください<br>";
              } else if (mb_ereg_match( '.*( |　|\t).*',$title)) {
            $errors["title"] = "タイトルは正しく入力してください<br>";
        
       //日本語の文字列の長さが１００文字より多いなら
        } else if (mb_strlen($title,"UTF-8") > 20) {
            $errors["title"] = "タイトルは全角20文字以内で入力してください";
            
        }

        //本文の文字列を取得できないなら
        if (!strlen($contents)) {
            $errors["contents"] = "本文を入力してください<br>";
             } else if (mb_ereg_match( '.*( |　|\t).*',$contents)) {
            $errors["contents"] = "本文は正しく入力してください<br>";
       //日本語の本文の長さが１００文字より多いなら
        } else if (mb_strlen($contents,"UTF-8") > 140) {
            $errors["contents"] = "本文は全角140文字以内で入力してください<br>";
        }


        //上記の処理でエラーがなかったら 
        if (count($errors) === 0) {
            //それぞれのセッションを作成する
          
            $this->set('user_id', $user_id);
            $this->set('title', $title);
            $this->set('contents', $contents);
            //以下のページへとばす
          $this->jump('add/check.php');
            exit;
            //エラーがあれば
        } else {
            //エラー配列を返す
            return $errors;
        }
    }

 
    /*topicsにデータを追加する関数*/
    public function topicInsert($user_id, $title, $contents) {
 $this->connectDb();
        $sql = sprintf("insert into topics (user_id, title,contents,created_at) values(%d,'%s','%s',now())",  
                $this->mysqlEscape($user_id),
               $this->mysqlEscape($title), 
                $this->mysqlEscape($contents)
        );
      

        mysql_query($sql);
        //追加したレコードのIDを取得
       $topic_id=mysql_insert_id();
       $this->set('topic_id', $topic_id);
        //それぞれの変数をリセットする
        $this->remove('user_id');
        $this->remove('user_name');
        $this->remove('title');
        $this->remove('contents');
        
        //現在表示しているページのURLを取得(現在実行しているスクリプトのファイル名。. 現在の表示のURL(topics.php)を取得)
        $this->jump('add/thanks.php');
        exit;
    }

    
    
    
   
    //コメント等のエラーチェック
    public function statusCheck($title,$comment, $user_id, $post_id, $_FILES) {
        
        // 動作中スクリプトの親パス/imagesディレクトリ
define('IMAGES_DIR', dirname($_SERVER['SCRIPT_FILENAME']).'/upimages');
define('THUMBNAIL_DIR', dirname($_SERVER['SCRIPT_FILENAME']).'/thumbnails');
define('THUMBNAIL_WIDTH', 72);
//サイズ制限
define('MAX_FILE_SIZE', 307200); // 300KB = 1KB/1024bytes * 300


        $this->connectDb();
//それぞれの配列を空にする        
        $errors = array();
        $images = array();
        $totalsize = "";
        if (!strlen($comment)) {
            $errors["comment"] = "本文を入力してください";
        } else if (mb_strlen($comment,"UTF-8") > 140) {
            $errors["comment"] = "本文は全角140文字以内で入力してください";
        }

        //配列が存在するなら
        if ($_FILES) {
            // 複数ファイルのアップロード対応
            foreach ($_FILES['image']["error"] as $key => $value) {
// エラーコードが０ならアップロードに成功
                if ($value === 0) {

                    //フィルサイズを取得する
                    $imagesize = filesize($_FILES['image']['tmp_name'][$key]);
                    if ($imagesize != 0) {
                        $totalsize += $imagesize;
                        //合計サイズが300Kバイトより大きいなら
                        if ($totalsize > MAX_FILE_SIZE) {
                            $errors['image'] = '合計ファイルサイズは300KB以下にしてください。';
                        }
// ファイル名
                   
                        //アップロードされたファイルであるなら
                        if (is_uploaded_file($_FILES['image']['tmp_name'][$key])) {
                            //拡張子(イメージの型)チェック     
                           $imagesize = getimagesize($_FILES['image']['tmp_name'][$key]);

                            //それが1.GIF 2.JPEG 3.PNGのどれかなら
                          switch($imagesize['mime']){
case 'image/gif':
$ext = '.gif';
break;
case 'image/jpeg':
$ext = '.jpg';
break;
case 'image/png':
$ext = '.png';
break;
default:
                     $errors['image'] = '拡張子がgif、jpeg、pngしかアップロードできません';
     return $errors;
                            }       
                                  $imageFileName = date('YmdHis') .mt_rand(1,9) . $ext;  
                                
 $imageFilePath = IMAGES_DIR . '/'.$imageFileName;
                                    //アップロードされたテンポラリファイルを、出力ファイル名で指定されたパスにコピー
                                    move_uploaded_file($_FILES['image']['tmp_name'][$key],$imageFilePath);

                             
                                   // 縮小画像を作成、保存
//元画像の幅高さ取得
$width = $imagesize[0];
$height = $imagesize[1];
if ($width > THUMBNAIL_WIDTH) {
// 元ファイルを画像タイプによって作る
switch($imagesize['mime']){
case 'image/gif':
$srcImage = imagecreatefromgif($imageFilePath);
break;
case 'image/jpeg':
// 新しい画像をファイルあるいは URL から作成
$srcImage = imagecreatefromjpeg($imageFilePath);
break;
case 'image/png':
$srcImage = imagecreatefrompng($imageFilePath);
break;
}
// 新しい高さを作る(幅をTHUMBNAIL_WIDTHで固定し、高さを比率で縮尺させる。少数点はROUND)
$thumbHeight = round($height * THUMBNAIL_WIDTH / $width);
// 縮小画像を生成
$thumbImage = imagecreatetruecolor(THUMBNAIL_WIDTH, $thumbHeight);
//imagecopyresampled()内部は、
//コピー先の画像リンクリソース。,コピー元の画像リンクリソース。,
//コピー先の x 座標。,コピー先の y 座標。,コピー元の x 座標。,コピー元の y 座標。,
//コピー先の幅。,コピー先の高さ。,コピー元の幅。,コピー元の高さ。
imagecopyresampled($thumbImage, $srcImage, 0, 0, 0, 0, 72, $thumbHeight, $width, $height);


// 縮小画像を保存する
switch($imagesize['mime']){
case 'image/gif':
imagegif($thumbImage, THUMBNAIL_DIR.'/'.$imageFileName);
break;
case 'image/jpeg':
//$thumbImage画像を「THUMBNAIL_DIR.'/'.$imageFileName」へ保存する
imagejpeg($thumbImage, THUMBNAIL_DIR.'/'.$imageFileName);
break;
case 'image/png':
imagepng($thumbImage, THUMBNAIL_DIR.'/'.$imageFileName);
break;
}
}
//ファイル名を配列へ(ｃｈｅｃｋで表示するため)
                                    $images[] = $imageFileName;
                              

                                   
                            
                            //アップロードされたファイルでないなら
                        } else {
                            $errors['image'] = 'ファイルのアップロードに失敗';
                        }
                    }
                    //４以外なら
                } elseif ($value !== 4) {
                    //ファイル自体がない
                    $errors['image'] = 'ファイルのアップロードに失敗';
                }
            }
        }

//上記の処理でエラーがなかったら 
        if (count($errors) === 0) {
$this->set('title', $title);
            $this->set('comment', $comment);
            $this->set('user_id', $user_id);
            $this->set('post_id', $post_id);
            //配列のセッションを作成する
            $this->set('images', $images);


          $this->jump('check.php');
            exit;
            //エラーがあれば
        } else {
             $errors['image'] = 'もういちど選択してください';
            //エラー配列を返す
            return $errors;
        }
    }

    
    
    /* statusテーブルにデータを追加する */
    public function statusInsert($comment, $user_id, $post_id, $images) {
$this->connectDb();
        //配列内の３つを「””」で埋める
        $images = array_pad($images, 3, "");


        $sql = sprintf("insert into status (comment, user_id, post_id,file_name1,file_name2,file_name3,created_at) 
            VALUES ('%s', '%s', '%s','%s', '%s','%s',now())", 
                $this->mysqlEscape($comment), $this->mysqlEscape($user_id), $this->mysqlEscape($post_id),
                //あれば追加していく
                $this->mysqlEscape($images[0]), $this->mysqlEscape($images[1]), $this->mysqlEscape($images[2])
        );
 
        //保存する
        mysql_query($sql);
        $this->remove('comment');
        $this->remove('user_id');
        $this->remove('images');


//現在表示しているページのURLを取得(現在実行しているスクリプトのファイル名。. 現在の表示のURL(topics.php)を取得)
        $this->jump('thanks.php');
        exit;
    }

    
    
   /*トピックス一覧を取得する関数*/
    public function allTopi($pageid,$page_value) {
        $this->connectDb();
        
        if ($page_value =='') {
            $pagelength =$this->pagelength_value[0];
            
        }  elseif ($page_value=="all") {
            $sql = "select count(id) from topics";
            if (!$query= mysql_query($sql)){
         echo "データ取得エラー"; exit;
      }
      $row = mysql_fetch_assoc($query);
	$pagelength = $row["count(id)"];//データ数を取得する
            
        } else {
            $pagelength=$page_value;
            
        }
        
        
        $pagelength = max($pagelength, 1);
        //-ページを省く
      

        //一覧表示を開始する番号を取得
        $start = ($pageid - 1) * $pagelength;
        //開始番号＋１で、「◯件から」
        $i=$start+1;
        
        //開始番号から５件分のデータを取得する
        $sql = "SELECT users.user_name, topics.* FROM users,topics 
            WHERE users.id=topics.user_id ORDER BY topics.created_at desc LIMIT {$start},{$pagelength}";

 
        $result = mysql_query($sql);

//SQLの結果がfalseでなく、かつ取得結果件数があれば
        $topics = array();
       
           
           
            //レコードを１行ずつ連想配列として抜き出す
            while ($topic = mysql_fetch_assoc($result)) {
               
                $q_id=$topic['id'];
            //各トピックの投稿件数を取得    
           $sql2 = "select count(post_id) from status where post_id='$q_id'";
      $query2=mysql_query($sql2) or die('登録できません'.mysql_error());
     
      
      $row = mysql_fetch_assoc($query2);
      //投稿件数を取得する
	$topic["count"] = $row["count(post_id)"];
        //投稿ナンバーを取得
        $topic["number"]=$i;
        
                //配列に追加していく
                $topics[] = $topic;
                $i++;
            }
           
             
            //取得した結果をメモリ上から解放
            mysql_free_result($result);
            //配列を返す
            return $topics;
        
    }
    
    
    //トピックの表示件数を取得
    public function counTopi($page_value) {
        if ($page_value =='') {
            $page_value =$this->pagelength_value[0];
        
        }
        echo "<option value=\"\">表示件数選択</option>". "\n";
    foreach($this->pagelength_value as $value){
		if($page_value==$value){
			//選択した数値をselectedになるよう指定
		echo"<option value=\"?pagelength=$value\" selected=selected>最新の{$value}件表示</option>";
			}else{
		echo"<option value=\"?pagelength=$value\">最新の{$value}件表示</option>";
			}
	}
    }
    
    
    
    //トピックスのトータル件数を取得
    public function countTotal() {

     $sql = "select count(id) from topics";
     
       $result=mysql_query($sql) or die('登録できません'.mysql_error());
       
           if (mysql_num_rows($result)) { 
      $row = mysql_fetch_assoc($result);
      
      
	$total = $row["count(id)"];//データ数を取得する
        return $total;
        
          } else {
                echo 'でーたがありません';
                 exit();
          
          }
          }
  
        
   

    /* 選択したトピック内容を取得する */
    public function topic($topic_id) {

        $sql = sprintf("SELECT u.user_name, t. * 
FROM topics t
LEFT JOIN users u ON u.id = t.user_id
WHERE t.id ='%s'
ORDER BY t.created_at", $this->mysqlEscape($topic_id)
        );

        $result = mysql_query($sql);
        $topic = mysql_fetch_assoc($result);
         $q_id=$topic['id'];
                
           $sql2 = "select count(post_id) from status where post_id='$q_id'";
        $query2=mysql_query($sql2) or die('登録できません'.mysql_error());
            if (mysql_num_rows($query2)) { 
      $row = mysql_fetch_assoc($query2);
      //データ数を取得する
	$topic["count"] = $row["count(post_id)"];
        return $topic;
    
    } else {
           echo 'でーたがありません';
                 exit();
    }
    }
    

    /* 投稿されたコメントを取得する */
    public function select($topic_id, $pageid,$page_value) {

         if ($page_value =='') {
            $pagelength =$this->pagelength_value[0];
            
        }  elseif ($page_value=="all") {
            $sql = "select count(post_id) from status where post_id={$topic_id}";
            if (!$query= mysql_query($sql)){
         echo "データ取得エラー"; exit;
      }
      $row = mysql_fetch_assoc($query);
	$pagelength = $row["count(post_id)"];//データ数を取得する
            
        } else {
            $pagelength=$page_value;
            
        }

        

        //一覧表示の開始番号を取得
        $start = ($pageid - 1) * $pagelength;
        $i=$start+1;
        $sql = sprintf("SELECT u.user_name, s. * 
FROM users u
LEFT JOIN status s ON s.user_id = u.id
WHERE s.post_id ='%s' ORDER BY s.created_at LIMIT {$start},{$pagelength}", $this->mysqlEscape($topic_id)
        );

        $result = mysql_query($sql);
//SQLの結果がfalseでなく、かつ取得結果件数があれば
           
        $posts = array();
       
            //レコードを１行ずつ連想配列として抜き出す
            while ($post = mysql_fetch_assoc($result)) {
                $post["number"]=$i;
                $posts[] = $post;
                $i++;
            }
           
            
      

        return $posts;
    }

    
    
    /* 画像を表示する */
    public function imgSrc($file_name) {


        if (!empty($file_name)) {
            //サムネイルディレクトリに画像ファイルがあるなら
                    if (file_exists($_SERVER['SCRIPT_FILENAME']).'/thumbnails'.'/'.$file_name) {
// サムネイルディレクトリにファイルがあればそれを優先
                return 'thumbnails/'.$file_name;
} else {
 return'upimages/'.$file_name;
}

    }
    }
    
    
    
    
    //投稿内容の表示件数のセレクトメニュー
    public function selectValue($topic_id,$page_value) {
//        取得した表示件数が空なら
        if ($page_value =='') {
            //5を代入
            $page_value =$this->pagelength_value[0];
        
        }
        
        //
        echo "<option value=\"\">表示件数選択</option>". "\n";
    foreach($this->pagelength_value as $value){
		if($page_value==$value){
			//選択した数値をselectedになるよう指定
		echo"<option value=\"?id={$topic_id}&pagelength=$value\" selected=selected>最新の{$value}件表示</option>";
			}else{
		echo"<option value=\"?id={$topic_id}&pagelength=$value\">最新の{$value}件表示</option>";
			}
	}
    }
    
    

    
    
    
 }
?>
