<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\User;

/* @var $this yii\web\View */
/* @var $model backend\models\User */

$this->title = 'View User ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-wrapper">
    <div class="container-fluid content">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'username',
            'email',
            'created_at',
        ],
    ]) ?>

    <div class="modal_footer_box clearfix">
        <?php
        if (Yii::$app->user->can('manage_invoices')){
            echo '<div class="btn btn-primary edit update" href="update?id=' . $model->id . '">Edit</div>';
        }

        if (Yii::$app->user->can('delete_invoices')){
            echo Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger delete',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]);
        }
        ?>
    </div>
    <button type="button" class="btn btn-success blue" data-toggle="modal" data-target="#myModal">Change password</div>
</div>
</div>
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
                        <button class="btn btn-success">Confirm password</button>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>

    </div>
</div>
