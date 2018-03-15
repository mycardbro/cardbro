<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Customers */

$this->title = $model->firstname . ' ' . $model->lastname;
$this->params['breadcrumbs'][] = ['label' => 'Customers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customers-view">
	<?= DetailView::widget([
		    'model' => $model,
		    'attributes' => $model->getOrderInfo(),
	]) ?>


	<div class="modal_footer_box clearfix">
	    <?php
	    if (Yii::$app->user->can('manage_customers')){
		    if (empty($model->getOrders()->count())) {
                        echo '<div style="float:right" class="btn btn-success blue replace" href="../orders/new?id=' . $model->id . '">Replace Card</div>';
                    }
		    echo '<div class="btn btn-primary edit update" href="update?id=' . $model->id . '">Edit</div>';
	    }

	    if (Yii::$app->user->can('delete_customers')){
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
