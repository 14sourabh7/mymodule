<?php

namespace App\Mymodule\Controllers;

use App\Core\Controllers\BaseController;
use App\Mymodule\Models\Order;
use App\Mymodule\Models\Product;  // If working with Models

class OrderController extends BaseController
{

    public function getAction()
    {
        $obj = new Order();
        $order = $obj->find()->toArray();
        $this->response->setStatusCode(200);
        return $this->response->setJsonContent($order);
    }

    public function addAction()
    {
        $obj = new Order();
        $product = new Product();
        $container = $product->getCollectionForTable(false);
        $product_id = $this->helper->sanitize($this->request->getPost('product_id'));
        $quantity = $this->helper->sanitize($this->request->getPost('quantity'));
        $status = "Order Placed";

        if ($product_id && $quantity) {
            $id = new \MongoDB\BSON\ObjectID($product_id);
            $product =   $container->find(['_id' => $id]);
            $product = iterator_to_array($product);
            $product =  count($product);
            if ($product < 1) {
                $this->response->setStatusCode(406);
                return $this->response->setJsonContent(['error' => 'product does not exist']);
            }

            $obj->product_id = $product_id;
            $obj->quantity = $quantity;
            $obj->status = $status;
            $result = $obj->save();
            $id = ((array)$obj->_id)['oid'];

            if ($result) {
                $this->response->setStatusCode(200);
                return $this->response->setJsonContent(['success' => 'order placed with id: ' . $id]);
            }
            $this->response->setStatusCode(400);
            return $this->response->setJsonContent(['error' => 'unable to place order']);
        }
        $this->response->setStatusCode(406);
        return $this->response->setJsonContent(['error' => 'product_id and quantity is required']);
    }

    public function updateAction()
    {

        $updateItems = $this->request->getPut();

        foreach ($updateItems as $key => $value) {
            $updateItems[$key] = $this->helper->sanitize($value);
        }

        if ($updateItems['id'] && $updateItems['status']) {
            $obj = new Order();
            $id = new \MongoDB\BSON\ObjectID($updateItems['id']);
            $container = $obj->getCollectionForTable(false);
            unset($updateItems['id']);
            $container->updateOne(['_id' => $id], ['$set' => ['status' => $updateItems['status']]]);
            return $this->response->setJsonContent(['success' => 'updated']);
        }
        $this->response->setStatusCode(406);
        return $this->response->setJsonContent(['error' => 'id and status are required']);
    }


    public function deleteAction()
    {
        $id = $this->helper->sanitize($this->request->get('id'));
        if ($id) {
            $obj = new Order();
            $idx = new \MongoDB\BSON\ObjectID($id);
            $container = $obj->getCollectionForTable(false);
            $container->deleteOne(['_id' => $idx]);
            return $this->response->setJsonContent(['success' => 'item deleted id: ' . $id]);
        }
        $this->response->setStatusCode(406);
        return $this->response->setJsonContent(['error' => 'id is required']);
    }
}
