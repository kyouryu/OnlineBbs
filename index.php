<?php
session_start();
require_once ("./class/bbsConfig.php");
require_once ("./class/authLogin.php");
require_once ("./class/bbsDtbs.php");
require_once ("./class/bbsPage.php");

$config = new BbsConfig();
$login = new AuthLogin();
$dtbs = new BbsDtbs();
$page = new BbsPage();


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

if (isset($_REQUEST['pagelength'])) {
    $page_value = $_REQUEST['pagelength'];
} else {
    $page_value = "";
}

//それぞれのセッションの値を代入する
$login_id = $config->session('login_id');
$login_time = $config->session('login_time');

//ログイン中のユーザー情報を呼び出す
$member = $login->nowLogin($login_id, $login_time);

//トピックス一覧を表示する
$topics = $dtbs->allTopi($pageid, $page_value);
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
        <title><?php echo $config->citeName; ?>｜トップ</title>
    </head>

    <body>
        <div id="wrap">
            <div id="head">


                <h1>トピック一覧</h1>
            </div>
            <select name="jumpMenu" id="jumpMenu" onchange="MM_jumpMenu('parent',this,0)">
                <?php
                $dtbs->counTopi($page_value);
                ?>
            </select>
            <a href="?pagelength=all">全てを表示</a>
            <div id="content"> 
                 <div style="text-align: right"><a href="./logout.php">ログアウト</a></div>
                 <div style="text-align: center">
                <?php
//ページングを設定する
                $page->topicPaging($pageid, $page_value);
                ?>
                     </div>
                <dl>
                    <dt>ようこそ！<?php echo $member['user_name'] ?>さん</dt>
                    <p>全<?php echo $dtbs->countTotal() ?>件のトピックがあります</p>
                    <dd><a href="add/form.php">新しいトピックを作成する</a></dd>
                </dl>

                <?php
//トピックスが１つ以上あるなら
                if (count($topics) > 0):
                    ?>
                    <dt>トピック一覧</dt>
                    <?php
                    //トピックに番号をつける
                    //１件ずつトピック内容を表示する
                    foreach ($topics as $topic) :
                        ?>
                    <div class="msg">
                            <p>(<?php echo $topic["number"] ?>)
                            「<a href="./topic.php?id=<?php echo $topic['id']; ?>">
                                    <?php echo $config->escape($topic['title']); ?>
                                </a>」</p>
                            <p>
                            作成者：<span class="name"><?php echo $config->escape($topic['user_name']); ?></span>さん
                            現在の投稿数：<?php echo $config->escape($topic['count']); ?>
                            </p>
                            <p>
                            <?php
                            $contents = $config->escape($topic['contents']);
                            //改行も反映させる
                            echo nl2br($contents);
                            ?>              
                            </p>
                           <p class="day"><?php echo $config->escape($topic['created_at']); ?>
                        <?php
                        if ($login_id === $topic['user_id']):
                            ?>
                            [<a href="./delete.php?topic_id=<?php echo $topic['id']; ?>" style="color: #F33;">削除</a>]
                            </p>
                     
                            </div>
                            <?php
                        endif;
                        ?>
                
                        <?php
                        //番号に＋１していく
                    endforeach;
                    ?>    
                   
                <?php endif; ?>


                             <div style="text-align: center">
                <?php
//ページングを設定する
                $page->topicPaging($pageid, $page_value);
                ?>
                     </div>
            </div>
            <div id="foot">
                <p><img src="images/txt_copyright.png" width="136" height="15" alt="(C) H2O Space. MYCOM" /></p>
            </div>
        </div>
    </body>
</html>
