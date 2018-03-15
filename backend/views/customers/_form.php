<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\models\Country;

/* @var $this yii\web\View */
/* @var $model backend\models\Customers */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customers-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'firstname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lastname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'zipcode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nationality')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'telephone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'dob')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <label for="exampleSelect1">Country</label>
        <select class="form-control" id="replace-reason" name="country">
                <?php foreach (Country::getAll() as $country) {
                            $selected = ($model->country == $country->CODE_ID) ? 'selected' : ''; 
                            echo '<option value=' . $country->CODE_ID . ' ' . $selected . '>' . $country->NAME . '</option>';
                } ?>
        </select>
    </div>

    <?= $form->field($model, 'comments')->textInput(['maxlength' => true]) ?>

    <!--<div class="btn btn-primary edit update" href="update?id=<?= $model->id ?>">Update</div>-->
    <div class="modal_footer_box clearfix">
	    <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary update edit']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
