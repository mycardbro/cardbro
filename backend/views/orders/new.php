<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\models\User;

/* @var $this yii\web\View */
/* @var $model backend\models\Orders */

$this->title = 'Replace Order: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Replace';
?>
<div class="orders-update">

    <div class="orders-form">

		<?php $form = ActiveForm::begin([
			'options' => [
				'id' => 'edit_order_form'
			]
		]); ?>
<!------------------------------------------------------------------------
<!DOCTYPE html>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>
<div class="col-12 col-md-4 pull-md-3 bd-content">
<form>
<div class="form-group">
<label for="exampleSelect1">Ammount Replacement</label>
<div class="input-group">
<div class="input-group-btn">
<button type="button" class="btn btn-default">Free</button>
<button type="button" class="btn btn-default">9 Euro</button>
</div>
<input type="text" class="form-control" aria-label="Custom Ammount" placeholder="or Custom Ammount">
</div>
</div>
<div class="form-group">
<label for="exampleSelect1">Choose Reason</label>
<select class="form-control" id="exampleSelect1">
<option selected>No Reason</option>
<option value="1">Lost</option>
<option value="2">Stolen</option>
<option value="3">Other Reason</option>
</select>
</div>
<div class="form-group">
<label for="exampleTextarea">Comment</label>
<textarea class="form-control" id="exampleTextarea" rows="3" disabled></textarea>
</div>
<button type="submit" class="btn btn-primary">Submit</button>
</form>
</div>
</body>
</html>
------------------------------------------------------------------------->
        <?php if (User::getRoleName(Yii::$app->user->id) == 'Administrator' || User::getRoleName(Yii::$app->user->id) == 'Manager') { ?>
            <div class="form-group field-orders-payment">
            <label class="control-label" for="orders-token">Enter Token</label>
            <input id="orders-payment" class="form-control" name="token" maxlength="9" type="text" required="required" >
            <div class="help-block"></div>
        </div>
            <div class="form-group">
            <label for="exampleSelect1">Select Product</label>
            <select class="form-control" id="replace-reason" name="product_id">
                <?php foreach ($products as $product) {
                            echo '<option value=' . $product->id . '>' . $product->name . '</option>';
                } ?>
            </select>
            </div>
            <div class="form-group">
            <label for="exampleSelect1">Select Nationality</label>
            <select class="form-control" id="replace-reason" name="country_code">
                <?php foreach ($countries as $country) {
                            echo '<option value=' . $country->NAME . '>' . $country->NAME . '</option>';
                } ?>
            </select>
            </div>
        <?php } ?>
        <!--<div class="btn-group" data-toggle="buttons">
            <label class="btn btn-default active">
                <input type="radio" name="options" id="option1" autocomplete="off" checked value="0"> Free
            </label>
            <label class="btn btn-default">
                <input type="radio" name="options" id="option2" autocomplete="off" value="1">€9
            </label>
        </div>-->
        <label for="exampleSelect1">Amount Replacement</label>
        <br>
        <div class="btn-group" data-toggle="buttons">
        <!--<label class="btn btn-default active">-->
            <input type="radio" name="options" id="option1" autocomplete="off" checked value="0"> Free
        <!--</label>-->
        <!--<label class="btn btn-default">-->
            <input type="radio" name="options" id="option2" autocomplete="off" value="1"> €9
        <!--</label>-->
        </div>
        <br>
        <br>
        <?php if (User::getRoleName(Yii::$app->user->id) == 'Administrator' || User::getRoleName(Yii::$app->user->id) == 'Manager') { ?>
            <div class="form-group field-orders-payment">
            <label class="control-label" for="orders-mail">Add Amount</label>
            <input id="orders-payment" class="form-control" name="payment" maxlength="32" type="text" value="0">
            <div class="help-block"></div>
        </div>
        <?php } ?>
        <div class="form-group">
            <label for="exampleSelect1">Choose Reason</label>
            <select class="form-control" id="replace-reason-new" name="replacement_id">
                <option selected value=0>No reason</option>
                <option class="hidecomment" value=1>First card not received</option>
                <?php if (User::getRoleName(Yii::$app->user->id) == 'Administrator' || User::getRoleName(Yii::$app->user->id) == 'Manager') { ?>
                    <option class="hidecomment" value=2>Second card not received</option>
                <?php } ?>
                <option class="hidecomment" value=3>Card stolen</option>
                <option class="hidecomment" value=4>Card lost</option>
                <option class="hidecomment" value=5>Card does not work anymore</option>
                <option class="showcomment" value=6>Other reason</option>
            </select>
        </div>
        <br>
        <label id="ptext-label-new" style="display:none;">Card Comments</label>
        <?= $form->field($model, 'comment')->textInput(['maxlength' => true, 'style' => 'display:none;', 'id' => 'ptext-new'])->label(false) ?>

        <div class="modal_footer_box clearfix">
			<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Order Card Replacement', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success blue replace']) ?>
        </div>

        <script>
            $(document).ready(function(){
                $("#replace-reason-new").change(function(){
                    if ($('#replace-reason-new').find(":selected").attr('class') == 'showcomment') {
                        $("#ptext-new").show();
                        $("#ptext-label-new").show();
                    } else {
                        $("#ptext-new").hide();
                        $("#ptext-label-new").hide();
                    }
                });
            });
        </script>
        <!--<select id="replace-reason">
            <option class="hidecomment" value="one">Stole</option>
            <option class="hidecomment" value="two">Lose</option>
            <option class="showcomment" value="other">Other</option>
        </select>-->

        <?php ActiveForm::end(); ?>

    </div>

</div>
