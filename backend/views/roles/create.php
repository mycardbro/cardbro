<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Roles */

$this->title = 'Create Roles';
$this->params['breadcrumbs'][] = ['label' => 'Roles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-wrapper">
    <div class="container-fluid grey">
        <div class="row">
            <div class="col-md-8">
				<?= Html::a('Create role', ['create'], ['class' => 'btn btn-success blue']) ?>
            </div>
            <div class="col-md-4">
                <div class="input-group search">
                    <input type="text" class="form-control" placeholder="Search for...">
                    <span class="input-group-btn">
                        <button class="btn btn-success blue" type="button">Search</button>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid content">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
    </div>

</div>
