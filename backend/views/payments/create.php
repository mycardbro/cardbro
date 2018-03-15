<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Payments */

$this->title = 'Create Payments';
$this->params['breadcrumbs'][] = ['label' => 'Payments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-wrapper">
    <div class="container-fluid content">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
    </div>
</div>