<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\CardsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cards-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'ID') ?>

    <?= $form->field($model, 'PRODUCTID') ?>

    <?= $form->field($model, 'TOKEN') ?>

    <?= $form->field($model, 'CREATED_DATE') ?>

    <?= $form->field($model, 'ACTIVATION_DATE') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
