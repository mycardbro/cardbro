<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Cards */

$this->title = 'Create Cards';
$this->params['breadcrumbs'][] = ['label' => 'Cards', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-wrapper">
    <div class="container-fluid content">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
    </div>
</div>
