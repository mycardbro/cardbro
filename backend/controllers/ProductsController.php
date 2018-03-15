<?php

namespace backend\controllers;

use yii\filters\AccessControl;
use Yii;
use backend\models\Product;
use backend\models\ProductSearch;
use backend\models\Company;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductsController implements the CRUD actions for Products model.
 */
class ProductsController extends Controller
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
				        'roles' => ['admin', 'manager'],
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
     * Lists all Products models.
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
		
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
	        'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Products model.
     * @param integer $id
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
     * Creates a new Products model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Product();
		$model->created_at = gmdate('Y-m-d H:i:s', time() + 7200);
		$model->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			\Yii::$app->session->setFlash('changed', $model->id);

			return $this->redirect('index');
        } else {
            return $this->render('create', [
                'model' => $model,
                'companies' => Company::find()->all(),
            ]);
        }
    }

    /**
     * Updates an existing Products model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
	public function actionUpdate($id)
	{
		$model = $this->findModel($id);
        $model->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash('changed', $model->id);

            return $this->redirect('index');
		}

		if (Yii::$app->request->isAjax) {
			return $this->renderAjax('update', [
				'model' => $model,
                'companies' => Company::find()->all(),
			]);
		} else {
			return $this->render('update', [
				'model' => $model,
                'companies' => Company::find()->all(),
			]);
		}
	}

    /**
     * Deletes an existing Products model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Products model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Products the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
