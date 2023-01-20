<?php
// htmlspecialchars自作ライブラリ
function h($value)
{
    return htmlspecialchars($value, ENT_QUOTES);
}
