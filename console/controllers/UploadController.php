<?php
namespace console\controllers;

use backend\models\Parser;
use yii\console\Controller;

class UploadController extends Controller
{
    public function actionIndex()
    {
        $productId = 140;
        
        $this->getOrderNumberByProductId($productId);
    }
        
    public function getOrderNumberByProductId($productId)
    {
        $invoices = Invoice::find()->select('id')->where(['product_id' => $productId])->asArray()->all();

        var_dump($invoices);
    }
}