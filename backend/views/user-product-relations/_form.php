<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Products */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="products-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    $userList = [];
    foreach ($users as $user) {
        $userList[$user->id] = $user->email;
    }

    $productList = [];
    foreach ($products as $product) {
        $productList[$product->ID] = $product->NAME;
    }
    ?>

    <?= $form->field($model, 'USER_ID')->dropDownList($userList) ?>

    <?= $form->field($model, 'PRODUCT_ID')->dropDownList($productList) ?>

    <div class="modal_footer_box clearfix">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary edit']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
