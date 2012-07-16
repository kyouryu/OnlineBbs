<?php
class BbsPage extends BbsConfig {
//表示件数
     private $pagelength_value=array(5,10,20,50,100,200);
    /* 投稿ページング関数 */

    public function statusPaging($pageid, $topic_id,$page_value) {
         //最大5件ずつ表示したい
    
        $links = "";
        $pager = "";
        //現在のページが空なら１とする
        if ($page_value =='') {
            $pagelength =$this->pagelength_value[0];
            
        }  elseif ($page_value=="all") {
            $sql = "select count(id) from status where post_id={$topic_id}";
            if (!$query= mysql_query($sql)){
         echo "データ取得エラー"; exit;
      }
      $row = mysql_fetch_assoc($query);
	$pagelength = $row["count(id)"];//データ数を取得する
        
            
        } else {
            $pagelength=$page_value;
        }
        $pagelength=max($pagelength,1);
        
        //全行数取得のためのSQL分を関数に渡す
        $sql = sprintf("select count(id) from status where post_id='%s' order by id ", $this->mysqlEscape($topic_id)
        );
        //全行数取得のためのSQLを実行する
        $sql_count = mysql_query($sql);
        $row = mysql_fetch_assoc($sql_count) or die(mysql_error());
        $total = $row["count(id)"]; //データ数を取得する
        $totalPage = ceil($total / $pagelength);  // 合計ページ数  
        $url = "?id={$topic_id}&pagelength={$pagelength}&page=";  // リンクURL

        if ($pageid > 1) {
            $pager .= "<span style=\"font-size:86%\"><a href=" . $url . ($pageid - 1) . ">&lt;&lt;前の" . $pagelength . "件</a></span> ";
        }

        for ($i = $pageid - $totalPage; $i <= $totalPage; $i++) {
            if ($i < 1)
                continue;
            if ($i == $pageid) {
                $preTag = "<strong>";
                $aftTag = "</strong> | ";
            } else {
                $preTag = "<a href='" . $url . $i . "'>";
                $aftTag = "</a> | ";
            }

            $links .= $preTag . $i . $aftTag;
        }
        $pager .= substr($links, 0, -2);

        //
        if ($pageid < $totalPage) {
            $pager .= "<span style=\"font-size:86%\"><a href=" . $url . ($pageid + 1) . ">次の" . $pagelength . "件&gt;&gt;</a></span> ";
        }

        echo "<div class=\"pager_link\">" . $pager . " </div>";
    }
    
    
     
    /*トピックページング関数*/
    public function topicPaging($pageid,$page_value) {
        
    
        $page_link="";
        $pager = "";
        $links="";
       
        
          //現在のページが空なら１とする
       
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
        $pageid = max($pageid, 1);

        //全行数を取得する
        $sql = "select count(id) from topics order by id desc";
        
//全行数取得のためのSQLを実行する
        $sql_count = mysql_query($sql);
        $row = mysql_fetch_assoc($sql_count);
        
        //合計データ数を取得する
        $total = $row["count(id)"];
        // 合計データ数/５件表示＝合計ページ数を切り上げで求める 
        $totalPage = ceil($total / $pagelength);  
        // リンクページURL
        $url = "?pagelength=$pagelength&page=";	

        //現在のページが１より大きいなら
        if ($pageid > 1) {
            //変数に追加
            $pager .= "<span style=\"font-size:86%\"><a href=" . $url . ($pageid - 1) . ">&lt;&lt;前の" . $pagelength . "件</a></span> ";
        }
        
    }
}