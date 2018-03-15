<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\models\Company;

/* @var $this yii\web\View */
/* @var $model backend\models\User */

$this->title = 'Create Sales partner';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-wrapper">
    <div class="container-fluid content">
        <?php $form = ActiveForm::begin([
            'id' => 'my-form-id',
            'action' => 'create',
            'enableAjaxValidation' => true,
            'validationUrl' => 'validation',
        ]); ?>

        <?php
        $companiesList = [];
        foreach (Company::find()->all() as $company) {
            $companiesList[$company->id] = $company->name;
        }
        ?>

        <?= $form->field($model, 'email', ['enableAjaxValidation' => true])->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'company_id')->dropDownList($companiesList)->label('Company Name') ?>
        <?= $form->field($model, 'role')->hiddenInput(['value'=> 'partner'])->label(false) ?>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success blue' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>