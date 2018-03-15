<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Products */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="products-form">
    <?php
        $companiesList = [];
        foreach ($companies as $company) {
            $companiesList[$company->id] = $company->name;
        }
    ?>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'company_id')->dropDownList($companiesList) ?>

    <?= $form->field($model, 'crdproduct')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'designref')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'currcode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lang')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'amtload')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'create_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'imageid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'limitsgroup')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'permsgroup')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'feesgroup')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'carrierref')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sms_required')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mail_or_sms')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'delv_method')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'action')->textInput(['maxlength' => true]) ?>

    <div class="modal_footer_box clearfix">
		<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary edit']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
