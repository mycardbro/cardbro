<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\SiteConfig */

$this->title = 'Create Site Config';
$this->params['breadcrumbs'][] = ['label' => 'Site Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-wrapper">
    <div class="container-fluid content">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
    </div>
</div>
