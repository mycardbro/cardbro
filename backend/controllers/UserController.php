<?php

namespace backend\controllers;

use yii\filters\AccessControl;
use Yii;
use backend\models\User;
use backend\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\web\Response;


/**
 * UserController implements the CRUD actions for Admin model.
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
	        'access' => [
		        'class' => AccessControl::className(),
		        'rules' => [
			        [
				        'allow' => true,
				        'roles' => ['@'],
			        ],
		        ],
	        ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Admin models.
     * @return mixed
     */
    public function actionIndex()
    {
        $data = Yii::$app->request->post();
        $session = Yii::$app->session;

        if (!empty($data['rows'])) {
            $session->set('rows', $data['rows']);
        } else {
            $rows = ($session->get('rows')) ?? 10;
            $session->set('rows', $rows);
        }
        
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Admin model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', [
                'model' => $this->findModel($id),
            ]);
        } else {
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    /**
     * Creates a new Admin model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        $model->created_at = gmdate('Y-m-d H:i:s', time() + 7200);
        $model->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);

        if ($model->load(Yii::$app->request->post())) {
	        //$model->password_hash = Yii::$app->getSecurity()->generatePasswordHash($model->password);
            $password = $this->generatePassword();
            $model->password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
	        $post_user = Yii::$app->request->post('User');
            if ($model->save()) {
                Yii::$app->sender->mandrillNewUser($model, $password);
                Yii::$app->authManager->assign(Yii::$app->authManager->getRole($post_user['role']), $model->id);
            }

            \Yii::$app->session->setFlash('changed', $model->id);

            return $this->redirect('index');
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Creates a new Admin model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionPartner()
    {
        $model = new User();
        //$model->scenario = 'create';

        if ($model->load(Yii::$app->request->post())) {
            $password = $this->generatePassword();
            $model->password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
            $post_user = Yii::$app->request->post('User');
            if ($model->save()) {
                Yii::$app->sender->mandrillNewUser($model, $password);
                Yii::$app->authManager->assign(Yii::$app->authManager->getRole($post_user['role']), $model->id);
            }

            \Yii::$app->session->setFlash('changed', $model->id);

            return $this->redirect('index');
        } else {
            return $this->render('partner', [
                'model' => $model,
            ]);
        }
    }

    public function actionValidation()
    {
        $model = new User();
        $request = \Yii::$app->getRequest();
        if ($request->isPost && $model->load($request->post())) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    /**
     * Updates an existing Admin model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->updated_at = time() + 7200;

        if ($model->load(Yii::$app->request->post())) {
	        $post_user = Yii::$app->request->post('User');
            $model->save();
            \Yii::$app->session->setFlash('changed', $model->id);
            /*Yii::$app->authManager->revoke(Yii::$app->authManager->getRole(User::getRole($model->id)), $model->id);
	        Yii::$app->authManager->assign(Yii::$app->authManager->getRole($post_user['role']), $model->id);*/
            return $this->redirect('index');
        } else {
            return $this->renderPartial('update', [
                'model' => $model,
            ]);
        }
    }
	public function actionChangepassword()
	{
		if (Yii::$app->request->post('id')) {
			$id = Yii::$app->request->post('id');
			$admin = User::findOne($id);
			$admin->password_hash = Yii::$app->getSecurity()->generatePasswordHash(Yii::$app->request->post('new_password'));
			$admin->save();
			return $this->redirect(['view', 'id' => $id]);
		}
		return false;
	}

    /**
     * Deletes an existing Admin model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Admin model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if ( ($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function generatePassword($length = 8) {
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                    $randomString .= $characters[rand(0, $charactersLength - 1)];
                }

        return $randomString;
    }

}
