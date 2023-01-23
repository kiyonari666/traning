<?php
require_once('./../config/database.php');
require_once('./../function/common.php');
require_once('./../function/company.php');

// ページング用、ページ番号取得部
$page = 1;
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $page = $_GET['page'];
}

// 検索ウィンドウにワードを残す処理
$textValue = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $textValue = $_GET['search'];
}

// ページング用、最大ページ数取得部
$sql = "select count(*) from companies";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $sql .= " where name like :name";
}
$stmt = $db->prepare($sql);
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $stmt->bindValue(':name', '%'. $_GET['search'].'%', PDO::PARAM_STR);
}
$stmt->execute();
$tableNum = $stmt->fetch();
$maxPage = ceil($tableNum['count(*)'] / 10);


// companiesテーブルデータ取得部
// ※　.=でSQLを追加するとき、.= " " 左記のように半角スペース忘れない
$start = ($page - 1) * 10;

$searchParam = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchParam = $_GET['search'];
}
$sql = "select * from companies";
if (!empty($searchParam)) {
    $sql .= " where name like :name";
}
$sql .= " order by id limit :start,10";
$stmt = $db->prepare($sql);
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $stmt->bindValue(':name', '%'. $_GET['search'].'%', PDO::PARAM_STR);
}
$stmt->execute();
$res = $stmt->fetchAll();
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
                <h1>会社一覧</h1>
                <?php if (!empty($searchParam)) : ?>
                    <p>検索結果</p>
                <?php endif; ?>
            </div>
            <div class="headerRight">
                <?php if (!empty($searchParam)) : ?>
                    <a  class="listBackButton" href="./list.php">一覧へ戻る</a>
                <?php endif; ?>
            </div>
        </header>

        <div class="listContainer">
            <div class="listContainerTop">
                <!-- 新規登録ボタン -->
                <a class="newCreateButton" href="./create.php">新規登録</a>
                <!-- 社名検索フォーム     -->   

                <form action="" method="get">
                    <input type="text" class="searchWind" name="search" maxlength="225" value="<?php echo h($textValue) ?>">
                    <?php if (!empty($_GET['listOrder']) && isset($_GET['listOrder'])) : ?>
                        <input type="hidden" name="listOrder" value="<?php echo $_GET['listOrder']; ?>">
                    <?php endif; ?>  
                    <input type="submit" class="searchSubmitButton" value="検索">
                </form>               
            </div>

            <div class="listContainerMain">
                
                <!-- レコードリストソート分岐 -->
                <?php if (isset($_GET['listOrder']) && !empty($_GET['listOrder'])) : ?>
                    <?php $listOrder = $_GET['listOrder']; ?>
                <?php endif; ?>
                <?php if (!empty($listOrder) && $listOrder === "asc") : ?>
                    <?php asort($res); ?>
                <?php elseif (!empty($listOrder) && $listOrder === "desc") : ?>
                    <?php arsort($res); ?> 
                <?php endif; ?>

                <!-- レコードリスト出力部 -->     
                <table>
                    <tr>
                        <th class="sortFrom">
                            <p>会社番号</p>
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
                                <?php if (!empty($searchParam)) : ?>
                                    <input type="hidden" name="$searchParam" value="<?php echo $searchParam; ?>">
                                <?php endif; ?>
                                <input type="submit" value="OK">
                            </form>
                        </th>
                        <th><p>会社名</p></th>            
                        <th><p>担当者名</p></th>            
                        <th><p>電話番号</p></th>            
                        <th><p>住所</p></th>            
                        <th><p>メールアドレス</p></th>            
                        <th><p>見積一覧</p></th>            
                        <th><p>請求一覧</p></th>            
                        <th class="tableCellCenter"><p>編集</p></th>            
                        <th class="tableCellCenter"><p>削除</p></th>            
                    </tr>
                    <?php foreach ($res as $record) : ?>
                        <tr>
                            <td><p><?php echo h($record['id']); ?></p></td>
                            <td><p><?php echo h($record['name']); ?></p></td>
                            <td><p><?php echo h($record['manager_name']); ?></p></td>
                            <td><p><?php echo h($record['phone_number']); ?></p></td>                    
                            <td>
                                <?php $postalCode = h($record['postal_code']); ?>
                                <p>〒<?php echo h(addHyphen($postalCode)); ?></p>
                                <p><?php echo h($record['address']); ?></p>
                            </td>
                            <td><p><?php echo h($record['mail_address']); ?></p></td>
                            <td class="tableCellCenter"><a href="./../quotations/list.php?id=<?php echo h($record['id']) ?>" class="estimateLink">見積一覧</a></td>
                            <td class="tableCellCenter"><a href="#" class="estimateLink">請求一覧</a></td>
                            <td class="tableCellCenter"><a href="./update.php?id=<?php echo h($record['id']) ?>">編集</a></td>
                            <td class="tableCellCenter"><a href="./delete.php?id=<?php echo h($record['id']) ?>">削除</a></td>                        
                        </tr>
                    <?php endforeach; ?>    
                </table>
            </div>

            <div class="listContainerBottom">
                <!-- ページ移動リンク -->
                <?php if (!empty($searchParam) && !empty($listOrder) && $page >= 2) : ?>
                    <a class="fowardButton" href="./list.php?page=<?php echo h($page - 1); ?>&search=<?php echo h($searchParam); ?>&listOrder=<?php echo h($listOrder); ?>">&larr; 前へ</a>
                <?php elseif (!empty($searchParam) && $page >= 2) : ?>
                    <a class="fowardButton" href="./list.php?page=<?php echo h($page - 1); ?>&search=<?php echo h($searchParam); ?>">&larr; 前へ</a>
                <?php elseif (!empty($listOrder) && $page >= 2) : ?>
                    <a class="fowardButton" href="./list.php?page=<?php echo h($page - 1); ?>&listOrder=<?php echo h($listOrder); ?>">&larr; 前へ</a>    
                <?php elseif ($page >= 2) : ?>
                    <a class="fowardButton" href="./list.php?page=<?php echo h($page - 1); ?>">&larr; 前へ</a>
                <?php endif; ?>

                <?php if (!empty($searchParam) && !empty($listOrder) && $page < $maxPage) : ?>
                    <a  class="backButton" href="./list.php?page=<?php echo h($page + 1); ?>&search=<?php echo h($searchParam); ?>&listOrder=<?php echo h($listOrder); ?>">次へ &rarr;</a>    
                <?php elseif (!empty($searchParam) && $page < $maxPage) : ?>
                    <a  class="backButton" href="./list.php?page=<?php echo h($page + 1); ?>&search=<?php echo h($searchParam); ?>">次へ &rarr;</a>    
                <?php elseif (!empty($listOrder) && $page < $maxPage) : ?>
                    <a  class="backButton" href="./list.php?page=<?php echo h($page + 1); ?>&listOrder=<?php echo h($listOrder); ?>">次へ &rarr;</a>    
                <?php elseif ($page < $maxPage) : ?>
                        <a  class="backButton" href="./list.php?page=<?php echo h($page + 1); ?>">次へ &rarr;</a>
                <?php endif; ?>


            </div>            
        </div>

    </div>
</body>
</html>
