<?php
session_start();
require_once ("./class/bbsConfig.php");
require_once ("./class/authLogin.php");
require_once ("./class/bbsDtbs.php");
require_once ("./class/BbsPage.php");


$config = new BbsConfig();
$login = new AuthLogin();
$dtbs = new BbsDtbs();
$page = new BbsPage();

$comment = "";

//ページ番号を取得
if (isset($_REQUEST['page'])) {
if (preg_match('/^[1-9][0-9]*$/', $_REQUEST['page'])) {
$pageid = (int)$_REQUEST['page'];
} else {
    $pageid = 1;
} 
}else {
    $pageid = 1;
}

//表示件数を取得
if (isset($_REQUEST['pagelength'])) {
    $page_value = $_REQUEST['pagelength'];
} else {
    $page_value = "";
}



$login_id = $config->session('login_id');
$login_time = $config->session('login_time');
$member = $login->nowLogin($login_id, $login_time);

//トピックidがセットされていないなら
if (!isset($_REQUEST["id"])) {
    //トピックス一覧へ飛ばす
   $config->jump('index.php');
} else {
    //トピックIDを取得する
    $topic_id = $config->getrequest('id');
    //トピック内容を取得する
    $topic = $dtbs->topic($topic_id);
}

//本文を取得し、改行を反映させる
$contents = $config->escape($topic['contents']);
$contents = nl2br($contents);

//コメント一覧を表示する
$posts = $dtbs->select($topic_id, $pageid, $page_value);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST["check"])) {
        $comment = $config->getpost('comment');
        $user_id = $config->getpost('user_id');
        $post_id = $config->getpost('post_id');
        $errors = $dtbs->statusCheck($topic['title'], $comment, $user_id, $post_id, $_FILES);
    } elseif (!empty($_POST["ng"])) {
        $comment = $config->session('comment');
        $user_id = $config->session('user_id');
        $post_id = $config->session('post_id');
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="./css/style.css" />
        <script type="text/javascript">
            function MM_jumpMenu(targ,selObj,restore){ //v3.0
                eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
                if (restore) selObj.selectedIndex=0;
            }
        </script>
        <title><?php echo $config->citeName; ?>｜トピック内容</title>     
    </head>
    <body>
        <div id="wrap">
            <div id="head">

                <h1><?php echo $config->escape($topic['title']); ?>トピック</h1>

                <p>作成者：<?php echo $config->escape($topic['user_name']); ?>
                    現在の投稿数：<?php echo $topic["count"] ?>件</p>
                <p><?php echo $contents; ?></p>
                <p class="day">投稿日時：<?php echo $config->escape($topic['created_at']); ?> </p>
            </div>
            <select name="jumpMenu" id="jumpMenu" onchange="MM_jumpMenu('parent',this,0)">
                <?php
                $dtbs->selectValue($topic_id, $page_value);
                ?>
            </select>
            <a href="?id=<?php echo $topic_id ?>&pagelength=all">全てを表示</a>
            <div id="content">             
                <div style="text-align: right"><a href="./logout.php">ログアウト</a></div>
                <div style="text-align: right"><a href="./index.php">トピック一覧</a></div>
                <div style="text-align: center">
                    <?php
                    $page->statusPaging($pageid, $topic_id, $page_value);
                    ?>
                </div>



                <?php if (count($posts) > 0): ?>

                    <?php foreach ($posts as $post) : ?>
<?php   $images=array(); ?>
                        <div class="msg">
                            <p>(<?php echo $post["number"] ?>)
                                <?php echo $config->escape($post['comment']); ?></p>
                            <p>      
                                
                                <?php  $images[]=$dtbs->imgSrc($post['file_name1']); ?>
                                  <?php  $images[]=$dtbs->imgSrc($post['file_name2']); ?>
                                  <?php  $images[]=$dtbs->imgSrc($post['file_name3']); ?>
                                <?php foreach ($images as $image) : ?>
                               
                                

                    
<?php if (strpos($image, 'thumbnails/') === 0) : ?>
<!--ファイルあるいはディレクトリへのパスを含む文字列を受け取って、 ファイル名を返す-->
<a href="upimages/<?php echo basename($image); ?>"><img src="<?php echo $image; ?>"></a>
<?php else : ?>
<img src="<?php echo $image; ?>">
<?php endif; ?>
    <?php endforeach; ?>
                            </p>
                            <p>作成者：<span class="name"><?php echo $config->escape($post['user_name']) ?>さん</span>
                            </p>


                            <p class="day"><?php echo $config->escape($post['created_at']); ?>

                                <?php
                                if ($login_id === $post['user_id']):
                                    ?>
                                    [<a href="./delete.php?comment_id=<?php echo $post['id']; ?>"style="color: #F33;">削除</a>]
                                </p>
                                <?php
                            endif;
                            ?>
                            <?php
                          
                        endforeach;
                        ?>
                    </div>
                <?php endif; ?>

                <form action="./topic.php?id=<?php echo $config->escape($topic['id']); ?>" method="post" enctype="multipart/form-data">
                    <!--テーブルに追加するために必要-->
                    <input type="hidden" name="user_id" value="<?php echo $config->escape($member['id']) ?>" />
                    <input type="hidden" name="post_id" value="<?php echo $config->escape($topic['id']) ?>" />
                    <dl>

                        <dt><?php echo $config->escape($member['user_name']) ?>さん、コメントをどうぞ<span class="required">（全角10000文字以内）</span></dt>
                        <p class="error">
                            <?php
                            if (!empty($errors["comment"])) {
                                echo $errors["comment"];
                            }
                            ?>
                        </p>
                        <dd>
                            <textarea name="comment" cols="50" rows="5"><?php echo $comment ?></textarea>

                        </dd>
                        <br/>
                        <dt>画像<span class="required">画像は「.gif」「.jpeg」「.png」で指定。合計ファイルサイズは100KB以下です。</span></dt>
                        <p class="error">
                            <?php
                            if (!empty($errors['image'])) {
                                echo $errors['image'];
                            }
                            ?>
                        </p>
                        <dd><input type="file" name="image[]"/><br/>
                            <input type="file" name="image[]"/><br/>
                            <input type="file" name="image[]"/><br/>

                        </dd> 
                    </dl>

                    <div><p><input type="submit" name="check" value="入力内容を確認する" /></p></div>

                </form>

                <div style="text-align: center">
                    <?php
                    $page->statusPaging($pageid, $topic_id, $page_value);
                    ?>
                </div>
            </div>
            <div id="foot">
                <p><img src="images/txt_copyright.png" width="136" height="15" alt="(C) H2O Space. MYCOM" /></p>
            </div>
        </div>
    </body>
</html>
