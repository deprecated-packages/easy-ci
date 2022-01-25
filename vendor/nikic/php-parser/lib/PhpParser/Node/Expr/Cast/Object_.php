<?php

declare (strict_types=1);
namespace EasyCI20220125\PhpParser\Node\Expr\Cast;

use EasyCI20220125\PhpParser\Node\Expr\Cast;
class Object_ extends \EasyCI20220125\PhpParser\Node\Expr\Cast
{
    public function getType() : string
    {
        return 'Expr_Cast_Object';
    }
}