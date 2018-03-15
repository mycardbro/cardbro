<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\SiteConfig */

$this->title = 'Update Site Config: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Site Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="content-wrapper">
    <div class="container-fluid content">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
    </div>
</div>
