<!-- 1.connect.php でDBにつなぐ
2.denpyoとtokuiに対してSQL発行
3. 2の戻り値を表示する
4.shosai,shohinに対しSQL発行
5. 3の戻り値を、ループしてテーブル上に表示する
6.色と幅をつける -->

<?php
if(!empty($_GET['code']) ){
    // 1.connect.php でDBにつなぐ
    require_once('connect.php');
    
    // 2.denpyoとtokuiに対してSQL文発行
    $sql = "SELECT `denpyo_id`, denpyo.`tokui_id`,tokui_name ,tokui_addr,`hiduke`
    FROM `denpyo`
        LEFT JOIN tokui
        ON denpyo.tokui_id = tokui.tokui_id
    WHERE denpyo_id = ?";

    $stmt = $dbh->prepare($sql);//プリペアードステートメント
    $stmt->bindValue(1 ,$_GET['code'],PDO::PARAM_INT);
    $stmt->execute();    //SQL実行 ここでプレースホルダでbindValue()のなかの1もここで一区切り

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // ASSOC はクエリ結果をフィールド名の連想配列で取り出す

        echo "<p>", $row["denpyo_id"];
        echo "<p>", $row["tokui_id"];
        echo "<p>", $row["tokui_name"];
        echo "<p>", $row["tokui_addr"];
        echo "<p>", $row["hiduke"];

    $sql_shosai_shohin = "SELECT  shosai.shohin_id AS 商品コード, shohin_name AS 商品名 ,num AS 数量
    FROM `shosai` 
        LEFT JOIN shohin 
        ON shosai.shohin_id = shohin.shohin_id
    WHERE `denpyo_id` = ?  ";

    $stmt = $dbh->prepare($sql_shosai_shohin);//プリペアードステートメント
    $stmt->bindValue(1 ,$_GET['code'],PDO::PARAM_INT);//上の方のSQLはexecuteで終わっているのでbindValue()の中のはてなの番号は1とする
    $stmt->execute();    //SQL実行

    echo "<table border=1>    <thead>
    <tr>   <th>商品コード</th>  <th>商品名</th>  <th>数量</th>  </tr> </thead> <tboby> ";

    foreach ($stmt as $key => $value) {//フェッチせず直接回す
        echo "<tr><td>{$value["商品コード"]} </td>
        <td>{$value["商品名"]}</td>
        <td>{$value["数量"]}</td>"; 
        echo "</tr>";
    }
    echo "</tboby></table>";
    exit;   //帳票情報があれば下のフォームは映らなくなる
}
// var_dump( $stmt->fetch(PDO::FETCH_ASSOC) );
// $row = $stmt->fetch(PDO::FETCH_ASSOC);
//     // ASSOC はクエリ結果をフィールド名の連想配列で取り出す
//     echo "<p>", $row["商品コード"];
//     echo "<p>", $row["商品名"];
//     echo "<p>", $row["数量"];
?>
<form><!--method="get"が初期値 -->
    <label>伝票番号</label>
    <input type="number" name="code">
    <input type="submit" value="検索"><!--type="submit"にしないと送信インプットにならない-->
</form>
