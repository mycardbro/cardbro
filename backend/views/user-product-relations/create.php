<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Products */

$this->title = 'Create User Product Relation';
$this->params['breadcrumbs'][] = ['label' => 'User Product Relation', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-wrapper">
    <div class="container-fluid content">

        <?= $this->render('_form', [
            'model' => $model,
            'users' => $users,
            'products' => $products,
        ]) ?>
    </div>
</div>