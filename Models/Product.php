<?php

namespace App\Mymodule\Models;

use App\Core\Models\BaseMongo;

class Product extends BaseMongo
{
    protected $table = "product";
    protected $collectionName = "product";
}
