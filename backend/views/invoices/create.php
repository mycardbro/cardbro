<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Invoices */

$this->title = 'Create Invoices';
$this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-wrapper">
    <div class="container-fluid content">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
    </div>
</div>
