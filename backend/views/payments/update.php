<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Payments */

$this->title = 'Update Payments: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Payments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="payments-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
