<?php

namespace App\Mymodule\Controllers;

use App\Core\Controllers\BaseController;
use App\Mymodule\Models\Product;  // If working with Models

class ProductController extends BaseController
{

    public function getAction()
    {
        $obj = new Product();
        $product = $obj->find()->toArray();
        $this->response->setStatusCode(200);
        return $this->response->setJsonContent($product);
    }

    public function addAction()
    {
        $obj = new Product();

        $name = $this->helper->sanitize($this->request->getPost('name'));
        $price = $this->helper->sanitize($this->request->getPost('price'));
        $category = $this->helper->sanitize($this->request->getPost('category'));

        if ($name && $name && $category) {
            $obj->name = $name;
            $obj->price = $price;
            $obj->category = $category;
            $result = $obj->save();
            $id = ((array)$obj->_id)['oid'];

            if ($result) {
                $this->response->setStatusCode(200);
                return $this->response->setJsonContent(['success' => 'product added with id: ' . $id]);
            }
            $this->response->setStatusCode(400);
            return $this->response->setJsonContent(['error' => 'product not added']);
        }
        $this->response->setStatusCode(406);
        return $this->response->setJsonContent(['error' => 'name price and category required']);
    }

    public function updateAction()
    {

        $updateItems = $this->request->getPut();

        foreach ($updateItems as $key => $value) {
            $updateItems[$key] = $this->helper->sanitize($value);
        }

        if ($updateItems['id']) {
            $obj = new Product();
            $id = new \MongoDB\BSON\ObjectID($updateItems['id']);
            $container = $obj->getCollectionForTable(false);
            unset($updateItems['id']);
            $container->updateOne(['_id' => $id], ['$set' => $updateItems]);
            return $this->response->setJsonContent(['success' => 'updated']);
        }
        $this->response->setStatusCode(406);
        return $this->response->setJsonContent(['error' => 'id is required']);
    }

    public function deleteAction()
    {
        $id = $this->helper->sanitize($this->request->get('id'));
        if ($id) {
            $obj = new Product();
            $idx = new \MongoDB\BSON\ObjectID($id);
            $container = $obj->getCollectionForTable(false);
            $container->deleteOne(['_id' => $idx]);
            return $this->response->setJsonContent(['success' => 'item deleted id: ' . $id]);
        }
        $this->response->setStatusCode(406);
        return $this->response->setJsonContent(['error' => 'id is required']);
    }
}
