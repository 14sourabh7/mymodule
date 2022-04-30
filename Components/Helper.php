<?php

namespace App\Mymodule\Components;

use Phalcon\Escaper;

class Helper
{
    public function sanitize($value)
    {
        $escaper = new Escaper();
        return $escaper->escapeHtml($value);
    }
}
