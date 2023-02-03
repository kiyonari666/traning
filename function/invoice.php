<?php
// 見積番号に記号挿入用ファンクション
function addQ(string $no): string
{
    return  substr_replace($no, '-p-', 8, 0);
}

// 請求番号に記号挿入用ファンクション
function addI(string $no): string
{
    return  substr_replace($no, '-i-', 8, 0);
}
