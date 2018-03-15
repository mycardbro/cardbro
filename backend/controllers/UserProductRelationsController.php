<?php
namespace backend\controllers;

use Yii;
use backend\models\UserProductRelations;
use backend\models\UserProductRelationsSearch;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use backend\models\Parser;
use backend\models\User;
use backend\models\Products;

class UserProductRelationsController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new UserProductRelationsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $uploader = new Parser();

        return $this->render('index', [
            'uploader' => $uploader,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new UserProductRelations();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'users' => User::find()->all(),
                'products' => Products::find()->all(),
            ]);
        }
    }

    protected function findModel($id)
    {
        if (($model = UserProductRelations::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
}