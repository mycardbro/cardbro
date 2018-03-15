<?php

namespace backend\controllers;

use yii\filters\AccessControl;
use Yii;
use backend\models\Customers;
use backend\models\CustomersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\Parser;
use backend\models\Cards;
use yii\web\UploadedFile;

/**
 * CustomersController implements the CRUD actions for Customers model.
 */
class CustomersController extends Controller
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
				        'roles' => ['admin', 'manager', 'support'],
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
     * Lists all Customers models.
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
		
        $searchModel = new CustomersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$uploader = new Parser();
		$uploaderCard = new Parser();

        return $this->render('index', [
			'uploader' => $uploader,
			'uploaderCard' => $uploaderCard,
	        'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Customers model.
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
     * Creates a new Customers model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Customers();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Customers model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
	public function actionUpdate($id)
	{
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect('index');
		}

		if (Yii::$app->request->isAjax) {
			return $this->renderAjax('update', [
				'model' => $model,
			]);
		} else {
			return $this->render('update', [
				'model' => $model,
			]);
		}
	}

    /**
     * Deletes an existing Customers model.
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
     * Finds the Customers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Customers the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Customers::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

	public function actionUpload(){
		if (Yii::$app->request->isAjax) {
			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			$file = new Parser;
			$file->file = UploadedFile::getInstance($file, 'file');
			$file->file_card = UploadedFile::getInstance($file, 'file_card');
			$data = $file->upload();
			if ($data) { // file is uploaded successfully
				try {
					if (!empty($data[0]['pubtoken'])) {
						$res = $this->updateStatuses($data);
						return [
							'code' => $res['code'],
							'text' => $res['text'],
						];
					}

					$columns = ['order_id', 'recid', 'token',];

					$csvColumns = array_map('strtolower', array_keys($data[0]));
					$neededColumns = array_diff($columns, $csvColumns);
					if (!empty($neededColumns)) {
						$text = 'Error!<br>';
						foreach ($neededColumns as $neededColumn) {
							$text .= "column $neededColumn not found<br>";
						}

						return [
							'code' => 400,
							'text' => $text,
						];
					}

					//Validation
					$validationErrors = $this->csvValidation($data);
					if (!empty($validationErrors)) {
						return [
							'code' => 400,
							'text' => $validationErrors,
						];
					}

					$emptyRows = 0;
					foreach ($data as $record){
						if (empty($record['token'])) {
							$emptyRows++;
							continue;
						}
						$card = new Cards;

						$order = Orders::find()->where(['ID' => $record['order_id']])->one();

						$card->ORDER_ID = $record['order_id'];
						$card->PRODUCTID = $order->PRODUCT_ID;
						$card->TOKEN = $record['token'];
						$card->ACTIVATION_DATE = '';
						$card->CREATED_DATE = '';
						$card->STATUS_NAME = 'Active';

						$order->STATUS_NAME = 'Done';
						$order->save();


						if (!$card->save()) {
							return [
								'code' => 200,
								'text' => $card->getErrors(),
							];
						}
					}
					$cardsNumber = count($data) - $emptyRows;
					$text = $cardsNumber . ' records imported successfully.
                    <br>' . $emptyRows . ' empty rows declined';
					/*\Yii::$app->mailer->compose()
                        ->setFrom([SiteConfig::option('contact_email') => SiteConfig::option('site_name')])
                        ->setTo(User::findIdentity(Yii::$app->user->id)->email)
                        ->setSubject('Invoices upload result')
                        ->setHtmlBody($text)
                        ->send();*/
					return [
						'code' => 200,
						'text' => $text
					];
				} catch (\Exception $e){
					\Yii::$app->session->setFlash('error', $e->getMessage());
				}
			} else {
				return [
					'code' => 400,
					'text' => 'Error!<br>Not CSV file!'
				];
			}
			return false;

		}
	}

	public function updateStatuses($data) {
		$res = [];
		$columns = ['pubtoken', 'activationdate', 'status',];

		$csvColumns = array_map('strtolower', array_keys($data[0]));
		$neededColumns = array_diff($columns, $csvColumns);
		if (!empty($neededColumns)) {
			$text = 'Error!<br>';
			foreach ($neededColumns as $neededColumn) {
				$text .= "column $neededColumn not found<br>";
			}

			$res['code'] = 400;
			$res['text'] = $text;

			return $res;
		}

		array_pop($data);

		$rules = [
			'pubtoken' => '/^\d{9}$/',
			'activationdate' => '/^\d{2}\.\d{2}\.\d{4}$/',
		];

		$tips = [
			'pubtoken' => 'Value should contain only letters',
			'activationdate' => 'Allowed value format is dd.mm.yyyy',
		];

		$validationErrors = Yii::$app->validation->csv($data, $rules, $tips);
		if (!empty($validationErrors)) {
			$res['code'] = 400;
			$res['text'] = $validationErrors;

			return $res;
		}

		foreach ($data as $record){
			$card = Cards::find()->where(['TOKEN' => $record['pubtoken']])->one();

			$card->ACTIVATION_DATE = $record['activationdate'];
			if (!empty($record['status'])) {
				$card->STATUS_NAME = $record['status'];
			}

			$card->save();
		}

		$res['code'] = 200;
		$res['text'] = count($data) . 'rows were updated successfully';

		return $res;
	}

	private function csvValidation($data) {

		$rules = [
			'order_id' => '/^\d{1,}$/',
			'recid' => '/^\d{1,}$/',
		];

		$tips = [
			'order_id' => 'Value should contain only digits',
			'recid' => 'Value should contain only digits',
		];

		return Yii::$app->validation->csv($data, $rules, $tips);
	}
}
