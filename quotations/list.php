<?php
require_once('./../const/status.php');
require_once('./../config/database.php');
require_once('./../function/common.php');

// ページング用、ページ番号取得部
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}

// 検索ウィンドウにワードを残す処理
$textValue = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $textValue = [
        $_GET['search'] => STATUS_LIST[$_GET['search']]
    ];
    $statusKey = array_keys($textValue);
}

// ページング用、最大ページ数取得部
$sql = "select count(*) from quotations where company_id=:id";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $sql .= " && status=:status";
}
$stmt = $db->prepare($sql);
$stmt->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $stmt->bindValue(':status', $_GET['search'], PDO::PARAM_STR);
}
$stmt->execute();
$tableNum = $stmt->fetch();
$maxPage = ceil($tableNum['count(*)'] / 10);

// quotationテーブルデータ取得部
$start = ($page - 1) * 10;

$searchParam = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchParam = $_GET['search'];
}
$sql = "select * from quotations where company_id=:id";
if (!empty($searchParam)) {
    $sql .= " && status=:status";
}
$sql .= " order by id limit :start,10";
$stmt = $db->prepare($sql);
$stmt->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $stmt->bindValue(':status', $_GET['search'], PDO::PARAM_STR);
}
$stmt->execute();
$res = $stmt->fetchAll();

// companiesテーブルデータ取得部
// ※　.=でSQLを追加するとき、.= " " 左記のように半角スペース忘れない
$sql = "select * from companies where id=:id";
$stmt = $db->prepare($sql);
$stmt->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
$stmt->execute();
$resCmpanies = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./../stylesheet/style.css">
</head>
<body>
    <div class="listWrap">

        <header>
            <div class="headerLeft">
                <h1>見積一覧</h1>
                <?php if (!empty($searchParam)) : ?>
                    <p>検索結果</p>
                <?php endif; ?>
            </div>
            <div class="headerRight">
                <h2><?php echo $resCmpanies['name'] ?></h2>
                <?php if (!empty($searchParam)) : ?>
                    <a  class="listBackButton" href="./list.php?id=<?php echo h($_GET['id']) ?>">見積一覧へ戻る</a>
                <?php endif; ?>
                <a href="./../companies/list.php" class="companyListBackButton">会社一覧へ戻る</a>
                
            </div>
        </header>

        <div class="listContainer">
            <div class="listContainerTop">
                <!-- 新規登録ボタン -->
                <a class="newCreateButton" href="./create.php?id=<?php echo h($_GET['id']) ?>&name=<?php echo h($resCmpanies['name']) ?>">見積作成</a>
                <!-- 社名検索フォーム     -->               
                <form action="" method="get">
                    <input type="hidden" name="id" value="<?php echo h($_GET['id']) ?>">
                    <select name="search" class="searchWind">
                        <?php if (!empty($textValue)) : ?>
                            <option value="" selected hidden><?php echo $textValue[$statusKey[0]]; ?></option>
                        <?php endif; ?>
                        <?php foreach (STATUS_LIST as $key => $val) : ?>         
                            <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                        <?php endforeach; ?> 
                    </select>
                    <input type="submit" class="searchSubmitButton" value="検索">
                </form>                                     
            </div>
            
            <div class="listContainerMain">
                <!-- レコードリスト出力部 -->     
                <table>
                    <tr>
                        <th><p>見積番号</p></th>
                        <th><p>見積名</p></th>            
                        <th><p>担当者名</p></th>            
                        <th><p>金額</p></th>            
                        <th><p>見積書有効期限</p></th>            
                        <th><p>納期</p></th>            
                        <th><p>状態</p></th>                      
                        <th class="tableCellCenter"><p>編集</p></th>            
                        <th class="tableCellCenter"><p>削除</p></th>            
                    </tr>
                    <?php foreach ($res as $record) : ?>
                        <tr>
                            <td><p><?php echo h($record['no']); ?></p></td>
                            <td><p><?php echo h($record['title']); ?></p></td>
                            <td><p><?php echo h($resCmpanies['manager_name']); ?></p></td>                           
                            <td><p><?php  echo number_format(h($record['total'])); ?>円</p></td>             
                            <td><p><?php echo h($record['validity_period']); ?></p></td>
                            <td><p><?php echo h($record['due_date']); ?></p></td>
                            <td><p><?php echo STATUS_LIST[h($record['status'])]; ?></p></td>
                            <td class="tableCellCenter"><a href="./update.php?id=<?php echo h($record['id']) ?>&company_id=<?php echo h($resCmpanies['id']) ?>&name=<?php echo h($resCmpanies['name']) ?>">編集</a></td>
                            <td class="tableCellCenter"><a href="./delete.php?id=<?php echo h($record['id']) ?>&company_id=<?php echo h($resCmpanies['id']) ?>&name=<?php echo h($resCmpanies['name']) ?>">削除</a></td>                        
                        </tr>
                    <?php endforeach; ?>    
                </table>
            </div>      
            
            <div class="listContainerBottom">
                <!-- ページ移動リンク -->
                <?php if (!empty($searchParam) && $page >= 2) : ?>
                    <a class="fowardButton" href="./list.php?page=<?php echo h($page - 1); ?>&id=<?php echo $_GET['id']; ?>&search=<?php echo h($searchParam); ?>">&larr; 前へ</a>
                <?php elseif ($page >= 2) : ?>
                    <a class="fowardButton" href="./list.php?page=<?php echo h($page - 1); ?>&id=<?php echo $_GET['id']; ?>">&larr; 前へ</a>
                <?php endif; ?>
                <?php if (!empty($searchParam) && $page < $maxPage) : ?>
                    <a  class="backButton" href="./list.php?page=<?php echo h($page + 1); ?>&id=<?php echo $_GET['id']; ?>&search=<?php echo h($searchParam); ?>">次へ &rarr;</a>    
                <?php elseif ($page < $maxPage) : ?>
                        <a  class="backButton" href="./list.php?page=<?php echo h($page + 1); ?>&id=<?php echo $_GET['id']; ?>">次へ &rarr;</a>
                <?php endif; ?>
            </div>
        </div>

    </div>
</body>
</html>
