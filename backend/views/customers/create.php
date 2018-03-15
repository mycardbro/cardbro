<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Customers */

$this->title = 'Create Customers';
$this->params['breadcrumbs'][] = ['label' => 'Customers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-wrapper">
    <div class="container-fluid content">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
    </div>
</div>
