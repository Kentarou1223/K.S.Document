<?php
//(大前提)データベースへの接続
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//【Mission③-1】 編集対象番号の入力があったら
if (isset($_POST["compile"])){
    //先に必要な変数の定義
    $compile=$_POST["compile"];
    $pass3=$_POST["pass3"];
    
    //対応するIDが存在するかどうかの確認
    $sql='SELECT id FROM tbmission_501';
    $smtm=$pdo->prepare($sql);
    $smtm->execute();
    $result=$smtm->fetchAll();
    foreach($result as $raw){
    if ($compile==$raw['id']){
    //対応する情報をデータベースから取得する
    $sql='SELECT*FROM tbmission_501 where id=:id';
    $stmt=$pdo->prepare($sql);
    $stmt->bindParam(':id', $compile, PDO::PARAM_INT);
    $stmt->execute();
    $results=$stmt->fetchAll();
    }//ここ迄一致する対象番号があった時の反応
    }//ここ迄対応するIDが存在した時の対応
}//ここ迄【Mission③-1】 編集対象番号の入力があった際の対応
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>mission_5-01</title>
    </head>
    <body>
        <p>お題：コロナが終わったらどこの国に行きたい？</p>
        <form action="" method="post">
            <input name="num" type="hidden" value="<?php
            if (isset($results)){
                foreach($results as $row){
                    if ($row['password']==$pass3){
                        echo $row['id'];
                    } else { echo "投稿番号";//ここ迄 パスワードが一致した時
                    }//ここまでpassが一致しなかった時
                }//ここまでがforeach関数
            }//ここまでが$resultが存在した時の対応
            else {echo "投稿番号";
            }?>">
            <input name="str" type="text" value="<?php
            if (isset($results)){
                foreach($results as $row){
                    if ($row['password']==$pass3){
                        echo $row['name'];
                    }//ここ迄 パスワードが一致した時
                }//ここまでがforeach関数
            }//ここまでが$resultが存在した時の対応
            ?>" placeholder="名前" required><br>
            <input name="comment" type="text" value="<?php
            if (isset($results)){
                foreach($results as $row){
                    if ($row['password']==$pass3){
                        echo $row['comment'];
                    }//ここ迄 パスワードが一致した時
                }//ここまでがforeach関数
            }//ここまでが$resultが存在した時の対応
            ?>"
            placeholder="コメント" required><br>
            <input name="pass1" type="password" placeholder="パスワード" required>
            <input name="submit" type="submit">
        </form>
        <br>
        <form action="" method="post">
            <input name="delete" type="text" placeholder="削除対象番号(半角)" required><br>
            <input name="pass2" type="password" placeholder="パスワード" required>
            <input name="submit" type="submit">
        </form>
        <br>
        <form action="" method="post">
            <input name="compile" type="text" placeholder="編集対象番号(半角)" required><br>
            <input name="pass3" type="password" placeholder="パスワード" required>
            <input name="submit" type="submit">
        </form>
    </body>
</html>

<?php
//【大前提】データベースへの接続
//(Mission_4-01)データベースに接続する！
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
//(mission_4-02)「tbmission_501」テーブルを既成・使用



//【Mission①】入力された名前とコメントをDBのカラムに登録するプロセス
if(isset($_POST["str"])&&isset($_POST["comment"])){
    //先に必要な変数の定義
    $num=$_POST["num"];
    $str=$_POST["str"];
    $date=date("Y-m-d H:i:s");
    $com=$_POST["comment"];
    $pass1=$_POST["pass1"];
    
       //(条件①A-1) 再度投稿である場合
         if ($num!="投稿番号"){
             $id=$num;
             $sql='UPDATE tbmission_501 SET name=:name, comment=:comment, create_date=:create_date, password=:password WHERE id=:id';
             $stmt=$pdo->prepare($sql);
             $stmt->bindParam(':name', $str, PDO::PARAM_STR);
             $stmt->bindParam(':comment', $com, PDO::PARAM_STR);
             $stmt->bindParam(':create_date', $date, PDO::PARAM_STR);
             $stmt->bindParam(':password', $pass1, PDO::PARAM_STR);
             $stmt->bindParam(':id', $id, PDO::PARAM_INT);
             $stmt->execute();
         } else {//ここ迄(条件①A-1)再度投稿である場合
         
       //(条件①A-2) 再度投稿でない場合
         $sql=$pdo->prepare("INSERT INTO tbmission_501(name,comment,create_date,password) VALUES(:name,:comment,:create_date,:password)");
         //テーブルに上記の要素を書き込む作業
         $sql->bindParam(':name', $str, PDO::PARAM_STR);
         $sql->bindParam(':create_date', $date, PDO::PARAM_STR);
         $sql->bindParam(':comment', $com, PDO::PARAM_STR);
         $sql->bindParam(':password', $pass1, PDO::PARAM_STR);
         $sql->execute();
         }//ここ迄(条件①A-2)再度表示でない場合
         
     //データを読み取りブラウザに表示する作業
     $sql='SELECT*FROM tbmission_501';
     $stmt=$pdo->query($sql);
     $results=$stmt->fetchAll();
     echo "<hr>";
     foreach($results as $row){
         //$rowの中にはテーブルのカラム名が入る
         echo $row['id'].'<名前>';
         echo $row['name'].'<日付>';
         echo $row['create_date'].'<br>';       
         echo '　　'.$row['comment'].'<br>';
     }//ここ迄 foreach関数
} else {//←ここ迄【Mission①】：名前とコメントに入力があった際の対応


//【Mission②】削除対象番号の入力があった際の対応
if (isset($_POST["delete"])) {
    //先に必要な変数の定義
    $delete=$_POST["delete"];
    $pass2=$_POST["pass2"];
    
    //対応するIDが存在するかどうかの確認
    $spl='SELECT id FROM tbmission_501';
    $sttm=$pdo->prepare($spl);
    $sttm->execute();
    $result=$sttm->fetchAll();
    $array=array_column($result, 'id');
    if (in_array($delete, $array)){
    
    //パスワードが一致した時のみ、と言う条件
    $sql='SELECT*FROM tbmission_501 where id=:id';
    $stmt=$pdo->prepare($sql);
    $stmt->bindParam(':id', $delete, PDO::PARAM_INT);
    $stmt->execute();
    $results=$stmt->fetchAll();
    foreach ($results as $row){
        if ($row['password']==$pass2){
    
    //削除する過程
    $sql='delete from tbmission_501 where id=:id';
    $stmt=$pdo->prepare($sql);
    $stmt->bindParam(':id', $delete, PDO::PARAM_INT);
    $stmt->execute();
    
    //データを読み取り再度ブラウザに表示
    $sql='SELECT*FROM tbmission_501';
    $stmt=$pdo->query($sql);
    $results=$stmt->fetchAll();
    echo "<hr>";
    foreach($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].'<名前>';
        echo $row['name'].'<日付>';
        echo $row['create_date'].'<br>';
        echo '　　'.$row['comment'].'<br>';
    }//ここ迄 foreach関数
        }//ここ迄 passが一致した時のみ、と言う条件(168〜188)
        else {
        $sql = 'SELECT*FROM tbmission_501';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        echo "<hr>";
        foreach($results as $row){
         //$rowの中にはテーブルのカラム名が入る
         echo $row['id'].'<名前>';
         echo $row['name'].'<日付>';
         echo $row['create_date'].'<br>';       
         echo '　　'.$row['comment'].'<br>';
     }}//ここまでがpassが一致しなかった時(189〜200)
    }//ここまでがpassのforeach関数
    }
    else {
        $sql = 'SELECT*FROM tbmission_501';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        echo "<hr>";
        foreach($results as $row){
         //$rowの中にはテーブルのカラム名が入る
         echo $row['id'].'<名前>';
         echo $row['name'].'<日付>';
         echo $row['create_date'].'<br>';       
         echo '　　'.$row['comment'].'<br>';
        }}//ここまでが対応するpassがなかった時の話
}//ここ迄【Mission②】：削除対象番号の入力があった際の対応
else {
    $sql = 'SELECT*FROM tbmission_501';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    echo "<hr>";
    foreach($results as $row){
         //$rowの中にはテーブルのカラム名が入る
         echo $row['id'].'<名前>';
         echo $row['name'].'<日付>';
         echo $row['create_date'].'<br>';       
         echo '　　'.$row['comment'].'<br>';
     }//ここまでがforeach関数
}//ここまでがMission②elseの話
}//ここまでがMission①elseの話
?>