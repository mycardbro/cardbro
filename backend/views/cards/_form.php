<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Cards */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cards-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'TOKEN')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'CREATED_DATE')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ACTIVATION_DATE')->textInput(['maxlength' => true]) ?>

    <div class="form-group field-cards-payment">
        <label class="control-label" for="cards-mail">Payment</label>
        <input id="cards-payment" class="form-control" name="payment" maxlength="32" type="text" value="0">
        <div class="help-block"></div>
    </div>

    <div class="modal_footer_box clearfix">
		<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary edit']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
