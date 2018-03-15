<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Payments */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payments-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'bill_amount')->textInput() ?>

    <?= $form->field($model, 'paid_amount')->textInput() ?>

    <div class="modal_footer_box clearfix">
		<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary edit']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>