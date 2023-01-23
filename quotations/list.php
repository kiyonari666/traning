<?php
require_once('./../const/status.php');
require_once('./../config/database.php');
require_once('./../function/common.php');
require_once('./../function/quote.php');

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

$search = "";
$listOrder = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
}
if (isset($_GET['listOrder']) && !empty($_GET['listOrder'])) {
    $listOrder = $_GET['listOrder'];
}
$sql = "select * from quotations where company_id=:id";
if (!empty($search)) {
    $sql .= " && status=:status";
}
if (!empty($listOrder) && $listOrder === 'desc') {
    $sql .= " order by id desc limit :start,10";
} else {
    $sql .= " order by id limit :start,10";
}
$stmt = $db->prepare($sql);
$stmt->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
if (!empty($search)) {
    $stmt->bindValue(':status', $search, PDO::PARAM_STR);
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
                <?php if (!empty($search)) : ?>
                    <p>検索結果</p>
                <?php endif; ?>
            </div>
            <div class="headerRight">
                <h2><?php echo $resCmpanies['name'] ?></h2>
                <?php if (!empty($search)) : ?>
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
                    <?php if (!empty($listOrder)) : ?>
                        <input type="hidden" name="listOrder" value="<?php echo $listOrder; ?>">
                    <?php endif; ?>
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

                    <th class="sortFrom">
                            <p>見積番号</p>
                            <form action="">
                                <select name="listOrder">
                                    <?php if (!empty($listOrder) && $listOrder === "asc") : ?>
                                        <option hidden><?php echo "昇順"; ?></option>
                                    <?php elseif (!empty($listOrder) && $listOrder === "desc") : ?>
                                        <option hidden><?php echo "降順"; ?></option>
                                    <?php endif; ?>
                                    <option value="asc">昇順</option>
                                    <option value="desc">降順</option>
                                </select>
                                <?php if (!empty($search)) : ?>
                                    <input type="hidden" name="searchParam" value="<?php echo $search; ?>">
                                <?php endif; ?>
                                <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
                                <input type="submit" value="OK">
                            </form>
                        </th>
                    
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
                            <?php $total = h($record['total']); ?>
                            <td><p><?php echo h(thousandsSeparator($total)); ?>円</p></td>
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
                <?php if (!empty($search) && !empty($listOrder) && $page >= 2) : ?>
                    <a class="fowardButton" href="./list.php?page=<?php echo h($page - 1); ?>&id=<?php echo $_GET['id']; ?>&search=<?php echo h($search); ?>&listOrder=<?php echo h($listOrder); ?>">&larr; 前へ</a>
                <?php elseif (!empty($search) && $page >= 2) : ?>
                    <a class="fowardButton" href="./list.php?page=<?php echo h($page - 1); ?>&id=<?php echo $_GET['id']; ?>&search=<?php echo h($search); ?>">&larr; 前へ</a>
                <?php elseif (!empty($listOrder) && $page >= 2) : ?>
                    <a class="fowardButton" href="./list.php?page=<?php echo h($page - 1); ?>&id=<?php echo $_GET['id']; ?>&listOrder=<?php echo h($listOrder); ?>">&larr; 前へ</a>    
                <?php elseif ($page >= 2) : ?>
                    <a class="fowardButton" href="./list.php?page=<?php echo h($page - 1); ?>">&larr; 前へ</a>
                <?php endif; ?>

                <?php if (!empty($search) && !empty($listOrder) && $page < $maxPage) : ?>
                    <a  class="backButton" href="./list.php?page=<?php echo h($page + 1); ?>&id=<?php echo $_GET['id']; ?>&search=<?php echo h($search); ?>&listOrder=<?php echo h($listOrder); ?>">次へ &rarr;</a>    
                <?php elseif (!empty($search) && $page < $maxPage) : ?>
                    <a  class="backButton" href="./list.php?page=<?php echo h($page + 1); ?>&id=<?php echo $_GET['id']; ?>&search=<?php echo h($search); ?>">次へ &rarr;</a>    
                <?php elseif (!empty($listOrder) && $page < $maxPage) : ?>
                    <a  class="backButton" href="./list.php?page=<?php echo h($page + 1); ?>&id=<?php echo $_GET['id']; ?>&listOrder=<?php echo h($listOrder); ?>">次へ &rarr;</a>    
                <?php elseif ($page < $maxPage) : ?>
                        <a  class="backButton" href="./list.php?page=<?php echo h($page + 1); ?>">次へ &rarr;</a>
                <?php endif; ?>
            </div>
        </div>

    </div>
</body>
</html>
