<?php
// 郵便番号にハイフン挿入用ファンクション
function addHyphen($postalCode)
{
    return  substr_replace($postalCode, '-', 3, 0);
}
