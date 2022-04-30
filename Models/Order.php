<?php

namespace App\Mymodule\Models;

use App\Core\Models\BaseMongo;

class Order extends BaseMongo
{
    protected $table = "order";
    protected $collectionName = "order";
}
