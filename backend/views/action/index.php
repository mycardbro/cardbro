<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'CardCompact - Actions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-wrapper">
    <div class="container-fluid grey">
        <div class="row">
            <div class="col-md-4">
                <?= $this->render('_search', ['model' => $searchModel]); ?>
            </div>
        </div>
    </div>
    <div class="container-fluid content">
        <?php echo GridView::widget([
            'options' => ['class' => 'table-responsive'],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => [
                'class' => 'table table-hover table-responsive borderless data'
            ],
            'rowOptions'   => function ($model, $key, $index, $grid) {
                if (Yii::$app->session->hasFlash('changed')) {
                    $customer_changed = \Yii::$app->session->getFlash('changed');
                    $changed_row = ($customer_changed == $model->id) ? 'new_row' : '';
                    \Yii::$app->session->removeFlash('changed');
                } else {
                    $changed_row = '';
                }

                return [
                    'title' => 'Action ' . $model->id,
                    'class' => 'row_click ' . $changed_row,
                    'href' => 'view?id=' . $model->id
                ];
            },
            'columns' => [
                [
                    'format' => 'raw',
                    'value' => function(){
                        return '<input type="checkbox" class="row_check">';
                    }
                ],
                [
                    'attribute' => 'email',
                    'label' => 'email',
                    'value' => 'user.email',
                ],
                'ip',
                [
                    'attribute' => 'type',
                    'label' => 'Type',
                    'value' => 'type.name',
                ],
                [
                    'attribute' => 'product',
                    'label' => 'Product',
                    'value' => 'product.name',
                ],
                'amount',
                'created_at',
            ],
        ]); ?>
        <?php $form = ActiveForm::begin(); ?>
        Rows on page
        <select id="pager" name="rows">
            <?php
            $pagers = [10,25,50,100];

            foreach ($pagers as $pager) {
                $selected = ($pager == Yii::$app->session->get('rows')) ? 'selected ' : '';
                echo "<option $selected value=$pager>$pager</option>";
            }
            ?>
        </select>
        <?= Html::submitButton('Apply', ['class' => 'btn-xs']) ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>