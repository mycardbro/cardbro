<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Products */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Product', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="products-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'company_id',
            'price',
            'crdproduct',
            'designref',
            'currcode',
			'lang',
            'amtload',
			'create_type',
            'imageid',
            'limitsgroup',
            'permsgroup',
            'feesgroup',
            'carrierref',
			'sms_required',
			'mail_or_sms',
			'action',
        ],
    ]) ?>

    <div class="modal_footer_box clearfix">
		<?php
		if (Yii::$app->user->can('manage_products')){
			echo '<div class="btn btn-primary edit update" href="update?id=' . $model->id . '">Edit</div>';
		}

		if (Yii::$app->user->can('delete_products')){
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

</div>
