<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\User;
use yii\helpers\ArrayHelper;
use backend\models\Company;

/* @var $this yii\web\View */
/* @var $model backend\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="admin-form">
    <?php
    $companiesList = [];
    foreach (Company::find()->all() as $company) {
        $companiesList[$company->id] = $company->name;
    }
    ?>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'company_id')->dropDownList($companiesList)->label('Company Name') ?>
    <?= $form->field($model, 'role')->hiddenInput(['value'=> 'partner'])->label(false) ?>
    <div class="modal_footer_box clearfix">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success blue' : 'btn btn-primary edit']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<!-- Trigger the modal with a button -->
<hr>
<button type="button" class="btn btn-warning" data-toggle="modal" data-target="#myModal">Change password</button>
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Change Password</h4>
            </div>
            <div class="modal-body">
                <form action="changepassword" method="post">
                    <?=Html::hiddenInput(Yii::$app->getRequest()->csrfParam, Yii::$app->getRequest()->getCsrfToken(), []);?>
                    <input type="hidden" name="id" id="id" value="<?=$model->id;?>">
                    <div class="form-group">
                        <label for="new_password" class="control-label">New password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-success">Confirm change</button>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>

    </div>
</div>
