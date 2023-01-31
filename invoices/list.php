<?php
require_once('./../const/status.php');
require_once('./../config/database.php');
require_once('./../function/common.php');
require_once('./../function/quote.php');

// 通常表示・検索表示のレコード取得
$sql = 'select * from quotations where company_id=:companyId';
$companyId = $_GET['companyId'] ?? '';
$search = $_GET['search'] ?? '';
$recordSort = $_GET['recordSort'] ?? '';
$page = $_GET['page'] ?? 1;
$start = ($page - 1) * 10;
if ($search !== '') {
    $sql .= ' && status=:status';
}
if (isset($recordSort)) {
    if ($recordSort === 'desc') {
        $sql .= ' order by id desc';
    } else {
        $sql .= ' order by id';
    }
}
$sql .= ' limit :start,10';
$stmt = $db->prepare($sql);
$stmt->bindValue(':companyId', $companyId, PDO::PARAM_INT);
if ($search !== '') {
    $stmt->bindValue(':status', $search, PDO::PARAM_STR);
}
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->execute();
$res = $stmt->fetchAll();

// 通常表示・検索表示のページ数取得
$sql = "select count(*) from quotations where company_id=:companyId";
if ($search !== '') {
    $sql .= " && status=:status";
}
$stmt = $db->prepare($sql);
$stmt->bindValue(':companyId', $companyId, PDO::PARAM_INT);
if ($search !== '') {
    $stmt->bindValue(':status', $search, PDO::PARAM_STR);
}
$stmt->execute();
$maxPage = $stmt->fetch();
$maxPage = ceil($maxPage['count(*)'] / 10);

// 別テーブルレコード取得
$sql = "select * from companies where id=:companyId";
$stmt = $db->prepare($sql);
$stmt->bindValue(':companyId', $companyId, PDO::PARAM_INT);
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
                <?php if ($search !== '') : ?>
                    <p>検索結果</p>
                <?php endif; ?>
            </div>
            <div class="headerRight">
                <?php if ($search !== '') : ?>
                    <a  class="listBackButton" href="./list.php?companyId<?php echo h($companyId); ?>">見積一覧へ戻る</a>
                <?php endif; ?>               
                <a href="./../companies/list.php" class="companyListBackButton">会社一覧へ戻る</a>    
            </div>
                
        </header>

        <div class="listContainer">
            <div class="listContainerTop">
                <!-- 新規登録ボタン -->
                <a class="newCreateButton" href="./create.php?companyId=<?php echo h($companyId); ?>&name=<?php echo h($resCmpanies['name']); ?>">見積作成</a>
                <!-- 検索フォーム     -->               
                <form action="" method="get">                   
                    <select name="search" class="searchWind">
                        <?php if ($search !== '') : ?>
                            <option value="" hidden><?php echo STATUS_LIST[h($search)]; ?></option>
                        <?php endif; ?>
                        <?php foreach (STATUS_LIST as $key => $val) : ?>         
                            <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                        <?php endforeach; ?> 
                    </select>
                    <input type="hidden" name="companyId" value="<?php echo h($companyId); ?>">
                    <?php if ($recordSort !== '') : ?>
                        <input type="hidden" name="recordSort" value="<?php echo h($recordSort); ?>">
                    <?php endif; ?>
                    <input type="submit" class="searchSubmitButton" value="検索">
                </form>                                     
            </div>

            <div class="listContainerMain">
                <!-- レコードリスト出力部 -->     
                <table>
                    <tr>
                        <th class="sortFrom">
                            <p>見積番号</p>
                            <!-- レコードソート実装部 -->
                            <form action="">
                                <select name="recordSort" onchange="this.form.submit()">
                                    <?php if ($recordSort === "desc") : ?>
                                        <option hidden><?php echo "降順"; ?></option>
                                    <?php endif; ?>
                                    <option hidden><?php echo "昇順"; ?></option>
                                    <option value="asc">昇順</option>
                                    <option value="desc">降順</option>
                                </select>
                                <input type="hidden" name="companyId" value="<?php echo h($companyId); ?>">
                                <?php if ($search !== '') : ?>
                                    <input type="hidden" name="search" value="<?php echo h($search); ?>">
                                <?php endif; ?>
                                <input type="hidden" name="page" value="<?php echo h($page); ?>">
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
                            <td><p><?php echo h(addNo($record['no'])); ?></p></td>           
                            <td><p><?php echo h($record['title']); ?></p></td>
                            <td><p><?php echo h($resCmpanies['manager_name']); ?></p></td>
                            <td><p><?php echo h(thousandsSeparator($record['total'])); ?>円</p></td>
                            <td><p><?php echo h($record['validity_period']); ?></p></td>
                            <td><p><?php echo h($record['due_date']); ?></p></td>
                            <td><p><?php echo STATUS_LIST[h($record['status'])]; ?></p></td>
                            <td class="tableCellCenter"><a href="./update.php?companyId=<?php echo h($companyId); ?>&id=<?php echo h($record['id']); ?>&name=<?php echo h($resCmpanies['name']); ?>">編集</a></td>
                            <td class="tableCellCenter"><a href="./delete.php?companyId=<?php echo h($companyId); ?>&id=<?php echo h($record['id']); ?>&name=<?php echo h($resCmpanies['name']); ?>">削除</a></td>                        
                        </tr>
                    <?php endforeach; ?>    
                </table>
            </div>      
            
            <div class="listContainerBottom">
                <!-- ページ移動リンク -->
                <?php if ($search !== '' && $recordSort !== '' && $page >= 2) : ?>
                    <a class="fowardButton" href="./list.php?companyId=<?php echo h($companyId); ?>&page=<?php echo h($page - 1); ?>&search=<?php echo h($search); ?>&recordSort=<?php echo h($recordSort); ?>">&larr; 前へ</a>
                <?php elseif ($search !== '' && $page >= 2) : ?>
                    <a class="fowardButton" href="./list.php?companyId=<?php echo h($companyId); ?>&page=<?php echo h($page - 1); ?>&search=<?php echo h($search); ?>">&larr; 前へ</a>
                <?php elseif ($recordSort !== '' && $page >= 2) : ?>
                    <a class="fowardButton" href="./list.php?companyId=<?php echo h($companyId); ?>&page=<?php echo h($page - 1); ?>&recordSort=<?php echo h($recordSort); ?>">&larr; 前へ</a>    
                <?php elseif ($page >= 2) : ?>
                    <a class="fowardButton" href="./list.php?companyId=<?php echo h($companyId); ?>&page=<?php echo h($page - 1); ?>">&larr; 前へ</a>
                <?php endif; ?>

                <?php if ($search !== '' && $recordSort !== '' && $page < $maxPage) : ?>
                    <a  class="backButton" href="./list.php?companyId=<?php echo h($companyId); ?>&page=<?php echo h($page + 1); ?>&search=<?php echo h($search); ?>&recordSort=<?php echo h($recordSort); ?>">次へ &rarr;</a>    
                <?php elseif ($search !== '' && $page < $maxPage) : ?>
                    <a  class="backButton" href="./list.php?companyId=<?php echo h($companyId); ?>&page=<?php echo h($page + 1); ?>&search=<?php echo h($search); ?>">次へ &rarr;</a>    
                <?php elseif ($recordSort !== '' && $page < $maxPage) : ?>
                    <a  class="backButton" href="./list.php?companyId=<?php echo h($companyId); ?>&page=<?php echo h($page + 1); ?>&recordSort=<?php echo h($recordSort); ?>">次へ &rarr;</a>    
                <?php elseif ($page < $maxPage) : ?>
                        <a  class="backButton" href="./list.php?companyId=<?php echo h($companyId); ?>&page=<?php echo h($page + 1); ?>">次へ &rarr;</a>
                <?php endif; ?>
            </div>
        </div>

    </div>
</body>
</html>
