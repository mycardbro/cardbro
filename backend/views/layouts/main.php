<?php
use yii\helpers\Html;
use dmstr\widgets\Alert;
use backend\models\SiteConfig;

/* @var $this \yii\web\View */
/* @var $content string */

//backend\assets\AdminAsset::register($this);
backend\assets\AppAsset::register($this);

$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
    ?>
    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
<!--        <link rel="icon" type="image/png" href="/frontend/web/images/qr_code.png">-->
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
        <script>
            (function(h,o,t,j,a,r){
                h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
                h._hjSettings={hjid:513717,hjsv:5};
                a=o.getElementsByTagName('head')[0];
                r=o.createElement('script');r.async=1;
                r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
                a.appendChild(r);
            })(window,document,'//static.hotjar.com/c/hotjar-','.js?sv=');
        </script>
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <?php echo SiteConfig::option('header_codes')?>
    </head>
<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
<body class="hold-transition skin-blue layout-top-nav">
<?php $this->beginBody() ?>
<div class="wrapper">
	<?= $this->render(
		'header.php',
		['directoryAsset' => $directoryAsset]
	) ?>
    <!-- Full Width Column -->
    <section class="content">
		<?php //echo Alert::widget();?>
		<?= $content ?>
    </section>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
        <div class="container" align="center">
            <img src="../images/Prepaid24.svg" />
        </div>
        <div class="container" align="center">
            <strong>Copyright &copy; 2017 PrePaid24 GmbH.</strong> All rights reserved.
        </div>
    </footer>
	<?php $this->endBody() ?>
    <script>
        $.widget.bridge('uibutton', $.ui.button);
    </script>
	<?php
	yii\bootstrap\Modal::begin([
		'headerOptions' => ['id' => 'modalHeader'],
		'id' => 'modal',
		'size' => 'modal-lg',
		//keeps from closing modal with esc key or by clicking out of the modal.
		// user must click cancel or X to close
//		'clientOptions' => ['backdrop' => 'static', 'keyboard' => FALSE]
	]);
	echo '<div id="modalContent"><div style="text-align:center"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></div></div>';
	yii\bootstrap\Modal::end();
	?>
	<?php echo SiteConfig::option('footer_code')?>
</body>
</html>
<?php $this->endPage() ?>

