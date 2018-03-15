<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Cards */

$this->title = 'Update Cards: ' . $model->ID;
$this->params['breadcrumbs'][] = ['label' => 'Cards', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ID, 'url' => ['view', 'id' => $model->ID]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="cards-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
