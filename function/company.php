<?php
// 郵便番号にハイフン挿入用ファンクション
function addHyphen(string $postalCode): string
{
    return  substr_replace($postalCode, '-', 3, 0);
}
