<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\Orders */

$this->title = 'Replace Order: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Replace';
if ($role == 'Sales Partner') {
    echo 'You have already ordered a replacement card for this cardholder. If you need one again, please contact us at support@cardcompact.co.uk';
} elseif ($role == 'Customer Support') {
    echo 'You have already ordered a replacement card for this cardholder. If you need one again, please ask your manager';
}
?>
