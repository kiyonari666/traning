<?php
// 郵便番号にハイフン挿入用ファンクション
function addHyphen($postalCode)
{
    return  substr_replace($postalCode, '-', 3, 0);
}
// プレフィックスに記号挿入用ファンクション
function addPrefix($prefixCode)
{
    return  substr_replace($prefixCode, '-', 8, 0);
}
