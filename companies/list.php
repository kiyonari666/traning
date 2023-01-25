<?php
require_once('./../config/database.php');
require_once('./../function/common.php');
require_once('./../function/company.php');

// 通常表示・検索表示のレコード取得
$sql = 'select * from companies';
$search = $_GET['search'] ?? '';
if ($search !== '') {
    $sql .= " where name like :name";
}
$sql .= " order by id limit :start,10";
$stmt = $db->prepare($sql);
if ($search !== '') {
    $stmt->bindValue(':name', '%'. $search .'%', PDO::PARAM_STR);
}
$page = $_GET['page'] ?? 1;
$start = ($page - 1) * 10;
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->execute();
$res = $stmt->fetchAll();

// 通常表示・検索表示のページ数取得
$sql = "select count(*) from companies";
if ($search !== '') {
    $sql .= " where name like :name";
}
$stmt = $db->prepare($sql);
if ($search !== '') {
    $stmt->bindValue(':name', '%'. $search .'%', PDO::PARAM_STR);
}
$stmt->execute();
$maxPage = $stmt->fetch();
$maxPage = ceil($maxPage['count(*)'] / 10);
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
                <?php if ($search !== '') : ?>
                    <p>検索結果</p>                
            </div>
                    <div class="headerRight">                
                        <a  class="listBackButton" href="./list.php">一覧へ戻る</a>
                    </div>
                <?php endif; ?>
        </header>

        <div class="listContainer">
            <div class="listContainerTop">
                <!-- 新規登録ボタン -->
                <a class="newCreateButton" href="./create.php">新規登録</a>
                <!-- 社名検索フォーム     -->
                <form action="" method="get">
                    <input type="text" class="searchWind" name="search" maxlength="225" value="<?php echo h($search) ?>">
                    <?php $recordSort = $_GET['recordSort'] ?? ''; ?>
                    <?php if ($recordSort !== '') : ?>
                        <input type="hidden" name="recordSort" value="<?php echo $recordSort; ?>">
                    <?php endif; ?> 
                    <?php if (isset($page)) : ?>
                        <input type="hidden" name="page" value="<?php echo $page; ?>">
                    <?php endif; ?> 
                    <input type="submit" class="searchSubmitButton" value="検索">
                </form>               
            </div>

            <div class="listContainerMain">
                
                <!-- レコードリストソート分岐 -->
                <?php if ($recordSort === "desc") : ?>
                    <?php arsort($res); ?> 
                <?php else : ?>
                        <?php asort($res); ?>
                <?php endif; ?>

                <!-- レコードリスト出力部 -->     
                <table>
                    <tr>
                        <th class="sortFrom">
                            <p>会社番号</p>
                            <!-- レコードソート実装部 -->
                            <form action="">
                                <select name="recordSort" onchange="this.form.submit()">
                                    <!-- セレクト初期値 降順で検索時は降順表示、それ以外は降順表示                                 -->
                                    <?php if ($recordSort === "desc") : ?>
                                        <option hidden><?php echo "降順"; ?></option>
                                    <?php else : ?>
                                            <option hidden><?php echo "昇順"; ?></option>
                                    <?php endif; ?>
                                    <option value="asc">昇順</option>
                                    <option value="desc">降順</option>
                                </select>
                                <?php if ($search !== '') : ?>
                                    <input type="hidden" name="search" value="<?php echo $search; ?>">
                                <?php endif; ?>
                                <?php if (isset($page)) : ?>
                                    <input type="hidden" name="page" value="<?php echo $page; ?>">
                                <?php endif; ?>
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
                <?php if ($search !== '' && $recordSort !== ''  && $page >= 2) : ?>
                    <a class="fowardButton" href="./list.php?page=<?php echo h($page - 1); ?>&search=<?php echo h($search); ?>&recordSort=<?php echo h($recordSort); ?>">&larr; 前へ</a>
                <?php elseif ($search !== ''  && $page >= 2) : ?>
                    <a class="fowardButton" href="./list.php?page=<?php echo h($page - 1); ?>&search=<?php echo h($search); ?>">&larr; 前へ</a>
                <?php elseif ($recordSort !== ''  && $page >= 2) : ?>
                    <a class="fowardButton" href="./list.php?page=<?php echo h($page - 1); ?>&recordSort=<?php echo h($recordSort); ?>">&larr; 前へ</a>    
                <?php elseif ($page >= 2) : ?>
                    <a class="fowardButton" href="./list.php?page=<?php echo h($page - 1); ?>">&larr; 前へ</a>
                <?php endif; ?>

                <?php if ($search !== '' && $recordSort !== '' && $page < $maxPage) : ?>
                    <a  class="backButton" href="./list.php?page=<?php echo h($page + 1); ?>&search=<?php echo h($search); ?>&recordSort=<?php echo h($recordSort); ?>">次へ &rarr;</a>    
                <?php elseif ($search !== ''  && $page < $maxPage) : ?>
                    <a  class="backButton" href="./list.php?page=<?php echo h($page + 1); ?>&search=<?php echo h($search); ?>">次へ &rarr;</a>    
                <?php elseif ($recordSort !== ''  && $page < $maxPage) : ?>
                    <a  class="backButton" href="./list.php?page=<?php echo h($page + 1); ?>&recordSort=<?php echo h($recordSort); ?>">次へ &rarr;</a>    
                <?php elseif ($page < $maxPage) : ?>
                    <a  class="backButton" href="./list.php?page=<?php echo h($page + 1); ?>">次へ &rarr;</a>
                <?php endif; ?>
            </div>            
        </div>

    </div>
</body>
</html>
