<?php
$err_msg = array();
$rows = array();
$host     = 'localhost';
$username = 'yuki242m';   // MySQLのユーザ名（ユーザ名を入力してください）
$password = '';       // MySQLのパスワード（空でOKです）
$dbname   = 'codecamp';   // MySQLのDB名(今回、MySQLのユーザ名を入力してください)
$dbh = ''; 
$charset = 'utf8';
$submit = '';
$name = '';
$comment = '';


$dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;
$log = date('Y,m,d H:i:s');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $submit = $_POST['submit'];
  if(isset($_POST['name']) && isset($_POST['comment']) === TRUE) {
    $name = trim(mb_convert_kana($_POST['name'], 's', 'utf-8'));
    $comment = trim(mb_convert_kana($_POST['comment'], 's', 'utf-8'));
  }
  if($name === '') {
    $err_msg[] = '名前入力が正しくありません';
  }
  if($comment === '') {
    $err_msg[] = 'コメント入力が正しくありません';
  }

	if (mb_strlen($name) > 20) {
	    $err_msg[] = '名前は２０文字以内で入力して下さい';
	}
	if (mb_strlen($comment) > 100) {
	    $err_msg[] = 'コメントは１００文字以内で入力して下さい';
	}
}    
try {
  $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  
  if (count($err_msg) === 0) {
	  if($submit === '送信') {
      try {  
      $sql = "INSERT INTO
        tokyo_post
        (user_name,
        user_comment,
        datetime)
      VALUES (?,?,?)";
      $stmt = $dbh->prepare($sql);
      $stmt->bindValue(1,$name,PDO::PARAM_STR);
      $stmt->bindValue(2,$comment,PDO::PARAM_STR);
      $stmt->bindValue(3,$log,PDO::PARAM_STR);
      $stmt->execute();
      } catch (PDOException $e) {
        echo '接続できませんでした。理由：'.$e->getMessage();
      }
	  }  
  } else {
    print 'バリデーションでエラーがあります。';
  }

  try {
    $sql = "SELECT
      id,
      user_name,
      user_comment,
      datetime
    FROM tokyo_post
    ORDER BY datetime DESC";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll();
  } catch (PDOException $e) {
    echo '接続できませんでした。理由：'.$e->getMessage();
  }
} catch (PDOException $e) {
  echo '接続できませんでした。理由：'.$e->getMessage();
}
function h($str){
  return htmlspecialchars($str, ENT_QUOTES, "utf-8");
}
?>


<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<title>オリンピック掲示板</title>
  <link rel="stylesheet" href="html5reset-1.6.1.css">
  <link rel="stylesheet" href="olympic_dbbbs.css">
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script type="text/javascript">
  
  var timerId;
  // カウントダウンの終了日
  var endDateTime   = new Date("2020/07/24 20:00:00");
 
  function countDownTimer() {
    /* ここにカウントダウンの処理を追記する */
 
    var startDateTime = new Date();
    var remaining = endDateTime - startDateTime;
 
    if( remaining < 0 ){
      $("#contents").html('東京オリンピック開催中です');
      // 繰り返し動作を停止 
      clearInterval(timerId);
      return
    }
 
    var daySeconds = 24 * 60 * 60 * 1000;
 
    // 期限から現在までの残り日数
    var d = Math.floor(remaining / daySeconds)
    // 期限から現在までの残り時間
    var h = Math.floor((remaining % daySeconds) / (60 * 60 * 1000))
    // 期限から現在までの残り分数
    var m = Math.floor((remaining % daySeconds) / (60 * 1000)) % 60
    // 期限から現在までの残り秒数
    var s = Math.floor((remaining % daySeconds) / 1000) % 60 % 60
 
    $("#TimeRemaining").text(d + '日' + h + '時間' + m + '分' + s + '秒');
  }
 
  $(function() {
    // 指定した一定時間ごとに関数countDownTimer()を呼び出す
    timerId = setInterval('countDownTimer()', 1000);
    $("p").click(function() {
        if($('#TimeRemaining').is(':hidden')){
            $('#TimeRemaining').slideDown();
        }else {
            $('#TimeRemaining').slideUp();
        }
    })
  });
</script>
	<!--jquery-->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.min.js"></script>
  <script>
    $(document).ready(function(){
      $('.slider').bxSlider({
        auto: true,
        pause: 4000,
        adaptiveHeight: true,
        adaptiveHeightSpeed: 1000
        });
    });
  </script>
  <!--jquery-->
</head>

<body>
<div class="body_one">
  
  <img class="img" src="./material/tokyo_olympic2020_kaisai.png" width="700px" height="500px">
  <div class="tokyo" id='contents'>
    <p>東京オリンピックまであと</p>
    <div id="TimeRemaining"></div>
	</div>
	
  <!--jquery-->
  <div class="slider">
  <div><img src="./material/pexels-photo-236937.jpeg" alt=""></div>
  <div><img src="./material/bobsled-team-run-olympics-38631.jpeg" alt=""></div>
  <div><img src="./material/chuttersnap-210450.jpg" alt=""></div>
  <div><img src="./material/matt-lee-19500.jpg" alt=""></div>
  </div>
	<!--jquery-->
	
	<h1>オリンピック掲示板</h1>
	
	    <?php
	    if (count($err_msg) > 0) { ?>
	        <ul>
	            <?php foreach ($err_msg as $value){ ?>
	                <li>
	                    <?php print h($value); ?>
	                </li>
	            <?php } ?>
	        </ul>
	    <?php } ?>
	<form class="form" method="post"> 
  	<label for="l_name">名前：</label>
  	<input id="l_name" type="text" name="name" width="11"><br>
  	<label for="L_comment">コメント：</label><br>
  	<textarea id="L_comment" name="comment" rows="4" cols="80"></textarea>
  	<input type="submit" name="submit" value="送信" style="width:100px; border-color: blue;">
	</form>
	
	<div class="frame">
	<?php
	foreach($rows as $value) { ?>
	  <?php print h($value['id']); ?>：
    <span class="flame_name"><?php print h($value['user_name']); ?></span>
	  <span>：<?php print h($value['datetime']); ?></span>
	  <p class="flame_comment"><?php print h($value["user_comment"]); ?></p>
	<?php }	?>
	</div>
</div>
</body>
</html>