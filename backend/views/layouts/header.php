<?php
use yii\helpers\Html;
use backend\models\User;
use backend\models\SiteConfig;
use yii\bootstrap\Nav;


/* @var $this \yii\web\View */
/* @var $content string */

?>

<header class="main-header">
    <?php
    $user = User::findIdentity(Yii::$app->user->id);
    ?>
    <nav class="navbar navbar-static-top">
        <div class="container-fluid">
            <div class="row">
                <div class="navbar-header">
		            <?= Html::a('<img src="../images/logo.png" alt="' . SiteConfig::option('site_name') . '">', Yii::$app->homeUrl, ['class' => 'logo']) ?>
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                        <i class="fa fa-bars"></i>
                    </button>
                </div>
                <div class="navbar-cardbro"><h4 style="float:left;margin-left: 10px;margin-top: 24px;">CardBro 2.0</h4></div>
                <div class="navbar-custom-menu" style="margin-top: 8px;">
                    <ul class="nav navbar-nav">
                        <!-- User Account Menu -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="<?= Yii::getAlias('@web') ?>/dist/img/default_user.svg" class="user-image" alt="admin Image"/>
                                <span class="hidden-xs"><?=$user->username != '' ? $user->username : $user->email;?></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header">
                                    <img src="<?= Yii::getAlias('@web');?>/dist/img/default_user_grey.svg" class="img-circle"
                                         alt="User Image"/>

                                    <p>
                                        <!--                                todo: add real admin credentials-->
							            <?=$user->username != '' ? $user->username : $user->email;?>
                                        <small><?=User::getRoleName(Yii::$app->user->id)?></small>
                                    </p>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
							            <?=Html::a('My Profile',
								            ['user/view?id=' . Yii::$app->user->id],
								            ['class' => 'btn btn-default btn-flat']
							            )?>
                                    </div>
                                    <div class="pull-right">
							            <?=Html::a(
								            'Log out',
								            ['/site/logout'],
								            ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
							            ) ?>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
		            <?= Nav::widget(
			            [
				            'options' => ['class' => 'nav navbar-nav'],
				            'items' => [
					            /*[
						            'label' => 'Settings',
						            'url' => ['#'],
						            'items' => [
							            ['label' => 'Companies', 'url' => ['company/index']],
                                        ['label' => 'Users', 'url' => ['user/index']],
							            ['label' => 'Permissions', 'url' => ['roles/index']],
						            ],
                                    'visible' => Yii::$app->user->can('manage_site')
					            ],*/
                                [
                                    'label' => 'Companies',
                                    'url' => ['company/index'],
                                    'visible' => Yii::$app->user->can('manage_site')
                                ],
                                [
                                    'label' => 'Users',
                                    'url' => ['user/index'],
                                    'visible' => Yii::$app->user->can('manage_site')
                                ],
					            [
						            'label' => 'Customers',
						            'url' => ['customers/index'],
						            'visible' => Yii::$app->user->can('view_customers')
					            ],
					            /*[
						            'label' => 'Cards',
						            'url' => ['cards/index'],
						            'visible' => Yii::$app->user->can('view_cards')
					            ],*/
					            [
						            'label' => 'Orders',
						            'url' => ['orders/index'],
						            'visible' => Yii::$app->user->can('view_orders')
					            ],
					            [
						            'label' => 'Products',
						            'url' => ['products/index'],
						            'visible' => Yii::$app->user->can('view_products')
					            ],
					            [
						            'label' => 'Invoices',
						            'url' => ['invoices/index'],
						            'visible' => Yii::$app->user->can('view_invoices')
					            ],
					            [
						            'label' => 'Payment reminders',
						            'url' => ['payments/index'],
						            'visible' => Yii::$app->user->can('view_payments')
					            ],
                                [
                                    'label' => 'SEPA check
',
                                    'url' => ['replenishment/index'],
                                    'visible' => Yii::$app->user->can('view_payments')
                                ],
                                [
                                    'label' => 'Logs',
                                    'url' => ['action/index'],
                                    'visible' => Yii::$app->user->can('manage_site')
                                ],
				            ],
			            ]
		            ) ?>
                </div>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->

            <!-- /.navbar-collapse -->
            <!-- Navbar Right Menu -->

            <!-- /.navbar-custom-menu -->
        </div>
        <!-- /.container-fluid -->
    </nav>
</header>
