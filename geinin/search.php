<?php
header("Content-type: text/html; charset=utf-8");

if(empty($_POST)) {
	header("Location: pdo_search_form.html");
	exit();
}else{
	//名前入力判定
	if (!isset($_POST['yourname'])  || $_POST['yourname'] === "" ){
		$errors['name'] = "名前が入力されていません。";
	}
}

if(count($errors) === 0){
	
	$dsn = 'mysql:host=mysql628.db.sakura.ne.jp;dbname=dboy_name;charset=utf8';
	$user = 'dboy';
	$password = 'geinin-9821';

	try{
		$dbh = new PDO($dsn, $user, $password);
		$statement = $dbh->prepare("SELECT * FROM name WHERE name LIKE (:name) OR unit LIKE (:unit) OR office LIKE (:office)");
	
		if($statement){
			$yourname = $_POST['yourname'];
			$like_yourname = "%".$yourname."%";
			//プレースホルダへ実際の値を設定する
			$statement->bindValue(':name', $like_yourname, PDO::PARAM_STR);
			$statement->bindValue(':unit', $like_yourname, PDO::PARAM_STR);
			$statement->bindValue(':office', $like_yourname, PDO::PARAM_STR);		

			if($statement->execute()){
				//レコード件数取得
				$row_count = $statement->rowCount();
				
				while($row = $statement->fetch()){
					$rows[] = $row;
				}
				
			}else{
				$errors['error'] = "検索失敗しました。<br>登録されていません";
			}
			
			//データベース接続切断
			$dbh = null;	
		}
	
	}catch (PDOException $e){
		print('Error:'.$e->getMessage());
		$errors['error'] = "データベース接続失敗しました。";
	}
}

?>

<!DOCTYPE html>
<html>
<head>
<title>検索結果</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="css/style.css" type="text/css" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

</head>
<body>
<div class="container mt-3 pt-5 pm-5 text-center">
<?php if (count($errors) === 0): ?>

<h1 class="text-center ">検索結果</h1>
<p><?=htmlspecialchars($yourname, ENT_QUOTES, 'UTF-8')."さんで検索しました。"?></p>
<p>該当は<?=$row_count?>件です。</p>


<?php
foreach((array)$rows as $row){
// データベースからの日付のハイフンを抜く
$date1 = DateTime::createFromFormat('Y-m-d', $row['birthday']);
$date2 = DateTime::createFromFormat('Y-m-d', $row['geireki']);

$birthday = $date1->format('Ymd');
$gei =  $date2->format('Ymd');

// 現在の年月日を取得
$base  = new DateTime();
$today = $base->format('Ymd');

$age = (int) (($today - $birthday) / 10000);
$geireki = (int) (($today - $gei) / 10000);


?> 
<article>
  <h2 class="mt-5"><strong><?=htmlspecialchars($row['name'],ENT_QUOTES,'UTF-8')?></strong></h2>
  <section class="row">
    <section class="col-md-3 mt-2">
	    <div class="inner-box">
		    <h3 class="bg-warning m-0 border1 small">ユニット</h3>
		    <div class="bg-light">
		    	<?=htmlspecialchars($row['unit'],ENT_QUOTES,'UTF-8')?>
	    	</div>
    	</div>
    </section>
    <section class="col-md-3 mt-2">
	    <div class="inner-box">
	      <h3 class="bg-warning m-0 p-0">年齢</h3>
		    <div class="bg-light">
		      <?=htmlspecialchars($age,ENT_QUOTES,'UTF-8')?>才
		    </div>
    	</div>
    </section>
    <section class="col-md-3 mt-2">
	    <div class="inner-box">
		    <h3 class="bg-warning m-0 p-0">芸歴</h3>
		    <div class="bg-light">
		    	<?=htmlspecialchars($geireki,ENT_QUOTES,'UTF-8')?>年
	    	</div>
    	</div>
    </section>
    <section class="col-md-3 mt-2">
	    <div class="inner-box">
		    <h3 class="bg-warning m-0 p-0">事務所</h3>
		    <div class="bg-light text-break">
		    	 <?=htmlspecialchars($row['office'],ENT_QUOTES,'UTF-8')?> 
	    	</div>
    	</div>
    </section>
  </section>

  <section class="mt-3">
	  	<h3 class="bg-warning">エピソード</h3>
	  	<table class="table table-sm table-bordered table-striped" >
	  		<tr>
	  			<td><p class="text-left"><?=htmlspecialchars($row['episode1'],ENT_QUOTES,'UTF-8')?></p></td>
		  	</tr>
	  		<tr>
	  			<td><p class="text-left"><?=htmlspecialchars($row['episode2'],ENT_QUOTES,'UTF-8')?></p></td>
		  	</tr>  		
	  	</table>
  </section>
</article>


<?php 
} 
?>

<?php elseif(count($errors) > 0): ?>
<?php
foreach($errors as $value){
	echo "<p>".$value."</p>";
}
?>
<?php endif; ?>


        <!-- jQuery first, then Popper.js, then Bootstrap JS, then Font Awesome -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
        <script defer src="https://use.fontawesome.com/releases/v5.7.2/js/all.js"></script>
 　　 <div class="row">
		<div class="col-12 text-center">
		  <button type="button" onclick="history.back();" class="btn btn-primary return">戻る</button>
　　　  </div>
　　　</div>
</div>
</body>
</html>