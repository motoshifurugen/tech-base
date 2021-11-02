<!DOCTYPE html>
<?php
$DBdsn = 'mysql:dbname=*****;host=localhost';
$DBuser = '*****';
$DBpassword = '*****';

// フォームの初期値
$value = [
    "id" => 0,
    "name" => "",
    "comment" => ""
];

// POST処理
// 新規追加、削除、編集準備、編集

if (!empty($_POST["add"]) && 
    !empty($_POST["name"]) && 
    !empty($_POST["comment"]) &&
    !empty($_POST["password"]) &&
    $_POST["id"] == 0) {
    // 新規追加

    $name = htmlspecialchars($_POST["name"]);
    $comment = htmlspecialchars($_POST["comment"]);
    $password = htmlspecialchars($_POST["password"]);
    $date = date("Y/m/d H:i:s");
    try {
        // 接続する
        $dbh = new PDO($DBdsn, $DBuser, $DBpassword);
        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        // SQL準備
        $stmt = $dbh->prepare('INSERT INTO tweets
        (name, comment, password, date)
        VALUES
        (:name, :comment, :password, :date);');
        // バインド
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindValue(':password', $password, PDO::PARAM_STR);
        $stmt->bindValue(':date', $date, PDO::PARAM_STR);
        // SQL実行
        $stmt->execute();
        echo "【欲しい物つぶやき部屋】 <b style='color:navy'>投稿しました！</b>";
    } catch (PDOException $e) {
        echo "【欲しい物つぶやき部屋】 <b style='color:navy'>投稿に失敗しました。</b>";
        echo "<br>エラー： " . $e->getMessage() . "<br>";
        exit();
    }
    // 接続を切る
    $dbh = null;

} elseif (!empty($_POST["add"]) && 
    !empty($_POST["name"]) && 
    !empty($_POST["comment"]) &&
    $_POST["id"] != 0) {
    // 編集
    
    // 編集する投稿を取得
    $id = $_POST["id"];
    try {
        // 接続する
        $dbh = new PDO($DBdsn, $DBuser, $DBpassword);
        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        // SQL準備
        $stmt = $dbh->prepare('SELECT * FROM tweets WHERE id=:id');
        // バインド
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        // SQL実行
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC); // データを取得
    } catch (PDOException $e) {
        $res = "";
        exit();
    }

    // パスワードを検証&編集処理
    if ($_POST["password"] == $res["password"]) {
        $name = htmlspecialchars($_POST["name"]);
        $comment = htmlspecialchars($_POST["comment"]);
        $date = date("Y/m/d H:i:s");
        try {
            // SQL準備
            $stmt = $dbh->prepare('UPDATE tweets 
            SET 
            name = :name,
            comment = :comment,
            date = :date
            WHERE id=:id');
            // バインド
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindValue(':date', $date, PDO::PARAM_STR);
            // SQL実行
            $stmt->execute();
            echo "【欲しい物つぶやき部屋】 <b style='color:navy'>更新しました！</b>";
        } catch (PDOException $e) {
            echo "【欲しい物つぶやき部屋】 <b style='color:maroon'>更新に失敗しました。</b>";
            echo "<br><p style='color:red'>エラー： " . $e->getMessage() . "</p>";
            exit();
        }
    } elseif (empty($_POST["password"])) {
        echo "【欲しい物つぶやき部屋】 <b style='color:maroon'>パスワードを入力してください。</b>";
    } else {
        echo "【欲しい物つぶやき部屋】 <b style='color:maroon'>パスワードが違います。</b>";
    }
    // 接続を切る
    $dbh = null;

} elseif (!empty($_POST["delete"]) && 
    !empty($_POST["deleteId"]) &&
    $_POST["deleteId"] > 0) {
    // 削除

    // 削除する投稿を取得
    $id = $_POST["deleteId"];
    try {
        // 接続する
        $dbh = new PDO($DBdsn, $DBuser, $DBpassword);
        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        // SQL準備
        $stmt = $dbh->prepare('SELECT * FROM tweets WHERE id=:id');
        // バインド
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        // SQL実行
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC); // データを取得
    } catch (PDOException $e) {
        $res = "";
        echo "<br><p style='color:red'>エラー： " . $e->getMessage() . "</p>";
        exit();
    }
    // パスワードを検証&削除処理
    if ($_POST["deletePassword"] == $res["password"]) {
        try {
            // 接続する
            $dbh = new PDO($DBdsn, $DBuser, $DBpassword);
            $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            // SQL準備
            $stmt = $dbh->prepare('DELETE FROM tweets WHERE id=:id');
            // バインド
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            // SQL実行
            $stmt->execute();
            echo "【欲しい物つぶやき部屋】 <b style='color:navy'>削除しました！</b>";
        } catch (PDOException $e) {
            echo "【欲しい物つぶやき部屋】 <b style='color:maroon'>削除に失敗しました。</b>";
            echo "<br><p style='color:red'>エラー： " . $e->getMessage() . "</p>";
            exit();
        }
    } elseif (empty($_POST["deletePassword"])) {
        echo "【欲しい物つぶやき部屋】 <b style='color:maroon'>パスワードを入力してください。</b>";
    } else {
        echo "【欲しい物つぶやき部屋】 <b style='color:maroon'>パスワードが違います。</b>";
    }
    // 接続を切る
    $dbh = null;

} elseif (!empty($_POST["edit"]) && 
    !empty($_POST["editId"]) &&
    $_POST["editId"] > 0) {
    // 編集準備
    
    // 該当する投稿を見つけ、それをフォームの初期値に代入する。
    $editId = $_POST["editId"];
    try {
        // 接続する
        $dbh = new PDO($DBdsn, $DBuser, $DBpassword);
        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        // SQL準備
        $stmt = $dbh->prepare('SELECT * FROM tweets WHERE id=:id');
        // バインド
        $stmt->bindValue(':id', $editId, PDO::PARAM_INT);
        // SQL実行
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC); // データを取得
    } catch (PDOException $e) {
        $res = "";
        echo "<br><p style='color:red'>エラー： " . $e->getMessage() . "</p>";
        exit();
    }
    $value = [
        "id" => $res["id"],
        "name" => $res["name"],
        "comment" => $res["comment"]
    ];
    echo "【欲しい物つぶやき部屋】";
    // 接続を切る
    $dbh = null;

} else {
    // その他（GETアクセス時）
    echo "【欲しい物つぶやき部屋】";
    
}
?>

<html>
    <body>
        <?php if ($value["id"] == 0): ?>
        <div style="border:solid 2px navy;border-radius:1rem;padding:0 10px;margin-top:10px;margin-bottom:10px;">
            <p style="color:navy;">新規作成</p>
        <?php else: ?>
        <div style="border:solid 2px teal;border-radius:1rem;padding:0 10px;margin-top:10px;margin-bottom:10px;">
            <p style="color:teal;">編集</p>
        <?php endif; ?>
            <form name="add" method="POST">
                <input type="hidden" name="id" value=<?= $value['id'] ?>>
                <div style="margin-left:32px">名前：<input type="text" name="name" value=<?= $value["name"] ?>></div>
                <div>コメント：<input type="text" name="comment" size="60" value=<?= $value["comment"] ?>></div>
                <?php if ($value["id"] == 0): ?>
                    <div>password <input type="password" name="password"> ※編集や削除の際に必要なパスワードになります。</div>
                <?php else: ?>
                    <div>password <input type="password" name="password"></div>
                <?php endif; ?>
                <div style="margin:10px 10px 20px 80px;"><input type="submit" name="add" value="送信" style="width:150px;"></div>
            </form>
        </div>
        <div>投稿一覧</div>
        <hr>
    </body>
</html>

<?php
try {
    // 接続する
    $dbh = new PDO($DBdsn, $DBuser, $DBpassword);
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    // SQL準備
    $stmt = $dbh->prepare('SELECT * FROM tweets');
    // SQL実行
    $stmt->execute();
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC); // データを取得
} catch (PDOException $e) {
    $res = "";
    echo "<br><p style='color:red'>エラー： " . $e->getMessage() . "</p>";
    exit();
}
if (empty($res)) {
    return;
}
foreach ($res as $tweet) {
    // 編集ボタン表示
    echo "<form name='action' method='POST' action='' style='display:inline;'>";
    echo "<input type='hidden' name='editId' value='" . $tweet['id'] ."'>";
    echo "<input type='submit' name='edit' value='編集'style='margin:5px;color:teal;margin-right:10px;'>";
    // 編集中の文は色付ける。
    if ($value["id"] == $tweet["id"]) {
        echo "<font color='real'>";
    } else {
        echo "<font>";
    }
    echo $tweet['id'].".".$tweet['name']."：".$tweet['comment']."（".$tweet['date']."）</font>";
    // パスワード入力欄と削除ボタン表示
    echo "<div style='display:inline-block;border:solid 1px maroon;border-radius:0.3rem;padding:0 10px;margin:3px;'>";
    echo "<input type='hidden' name='deleteId' value='" . $tweet['id'] ."'>";
    echo "<input type='password' name='deletePassword' placeholder='password'>";
    echo "<input type='submit' name='delete' value='削除'style='margin:5px;color:maroon;'>";
    echo "</div>";
    echo "</form><br>";
}
// 接続を切る
$dbh = null;
?>