<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\SiteConfig */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="site-config-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if (Yii::$app->controller->action->id == 'update'){
        echo $form->field($model, 'key')->textInput(['maxlength' => true, 'readonly' => true]);
    } else {
	    echo $form->field($model, 'key')->textInput(['maxlength' => true]);
    }
    ?>

    <?= $form->field($model, 'value')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
