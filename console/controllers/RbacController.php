<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
	public function actionInit()
	{
		//roles--------------------
		$admin = Yii::$app->authManager->createRole('admin');
		$admin->description = 'Administrator';
		Yii::$app->authManager->add($admin);

		$manager = Yii::$app->authManager->createRole('manager');
		$manager->description = 'Manager';
		Yii::$app->authManager->add($manager);

		$support = Yii::$app->authManager->createRole('support');
		$support->description = 'Customer Support';
		Yii::$app->authManager->add($support);

		$partner = Yii::$app->authManager->createRole('partner');
		$partner->description = 'Sales Partner';
		Yii::$app->authManager->add($partner);

		//permissions------------------
		$manage_site = Yii::$app->authManager->createPermission('manage_site');
		$manage_site->description = 'Permission to manage site';
		Yii::$app->authManager->add($manage_site);

		$manage_users = Yii::$app->authManager->createPermission('manage_users');
		$manage_users->description = 'Permission to manage users';
		Yii::$app->authManager->add($manage_users);

		$manage_products = Yii::$app->authManager->createPermission('manage_products');
		$manage_products->description = 'Permission to manage products';
		Yii::$app->authManager->add($manage_products);
		$manage_invoices = Yii::$app->authManager->createPermission('manage_invoices');
		$manage_invoices->description = 'Permission to manage invoices';
		Yii::$app->authManager->add($manage_invoices);
		$manage_payment_reminders = Yii::$app->authManager->createPermission('manage_payment_reminders');
		$manage_payment_reminders->description = 'Permission to manage manage_payment_reminders';
		Yii::$app->authManager->add($manage_payment_reminders);

		$create_orders = Yii::$app->authManager->createPermission('create_orders');
		$create_orders->description = 'Permission to create orders';
		Yii::$app->authManager->add($create_orders);

		$create_orders = Yii::$app->authManager->createPermission('create_orders');
		$create_orders->description = 'Permission to create orders';
		Yii::$app->authManager->add($create_orders);

		$view_orders = Yii::$app->authManager->createPermission('view_orders');
		$view_orders->description = 'Permission to view orders';
		Yii::$app->authManager->add($view_orders);

		//inheritance
		//admin inherits all other roles
		Yii::$app->authManager->addChild($admin, $manager);
		Yii::$app->authManager->addChild($admin, $support);
		Yii::$app->authManager->addChild($admin, $partner);

		//other in database

		//assign default permissions to roles
		Yii::$app->authManager->addChild($admin, $manage_site);
		Yii::$app->authManager->addChild($admin, $manage_users);

		Yii::$app->authManager->addChild($support, $create_orders);
		Yii::$app->authManager->addChild($support, $view_orders);

		// Assign roles to users. 1 is IDs returned by IdentityInterface::getId()
		// usually implemented in your User model.
		Yii::$app->authManager->assign($admin, 1);
	}
}