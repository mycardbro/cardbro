<?php

namespace backend\controllers;

use yii\filters\AccessControl;
use Yii;
use backend\models\Invoice;
use backend\models\InvoiceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\Parser;
use yii\web\UploadedFile;
use backend\models\SiteConfig;
use backend\models\User;
use backend\models\Orders;
use yii\db\Expression;
use backend\models\Customers;

/**
 * InvoicesController implements the CRUD actions for Invoices model.
 */
class InvoicesController extends Controller
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
				        'roles' => ['admin', 'manager', 'partner'],
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

    public function  actionTest() {
        return $this->render('test');
    }

    public function actionDownload(){
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();

            $invoices = Invoice::find('id')
                ->where(['in', 'id', $data['data']])
                ->asArray()
                ->all();
            $orders = Orders::find()
                ->select([
                    'recid as ExternalRef',
                    'product.crdproduct as CardDesign',
                    'recid as CustAccount',
                    'product.designref as ProductRef',
                    'customer.title as Title',
                    'customer.lastname as LastName',
                    'customer.firstname as FirstName',
                    'customer.dob as DOB',
                    'customer.email as Email',
                    'customer.telephone as Mobile',
                    'customer.address as Addrl1',
                    new Expression('null as Addrl2'),
                    new Expression('null as Addrl3'),
                    'customer.city as City',
                    'customer.zipcode as PostCode',
                    'customer.country as Country',
                    'product.currcode as CurCode',
                    'product.lang as Lang',
                    'product.amtload as LoadValue',
                    'right(customer.telephone, 6) as AccCode',
                    'product.create_type as CreateType',
                    'card.card_name as CardName',
                    'product.imageid as ImageID',
                    new Expression('null as ThermalLine1'),
                    new Expression('null as ThermalLine2'),
                    'product.limitsgroup as LimitsGroup',
                    new Expression('null as MCCGroup'),
                    'product.permsgroup as PERMSGroup',
                    'product.feesgroup as FeeGroup',
                    'product.carrierref as CarrierType',
                    new Expression('0 as Sms_Required'),
                    new Expression('0 as MailOrSms'),
                    new Expression('0 as DelvMethod'),
                    new Expression('null as Delv_Addrl1'),
                    new Expression('null as Delv_Addrl2'),
                    new Expression('null as Delv_Addrl3'),
                    new Expression('null as Delv_City'),
                    new Expression('null as Delv_County'),
                    new Expression('null as Delv_PostCode'),
                    new Expression('null as Delv_Country'),
                    new Expression('null as ExpDate'),
                    new Expression('null as PrimaryToken'),
                ])
                ->join('LEFT JOIN', 'invoice', 'card.invoice_id = invoice.id')
                ->join('LEFT JOIN', 'product', 'invoice.product_id = product.id')
                ->join('LEFT JOIN', 'customer', 'card.customer_id = customer.id')
                ->where(['in', 'invoice_id', $data['data']])
                ->asArray()
                ->all();

            $folder = Yii::$app->controller->id;
            $name = $folder . '-list-' . date('Y-M-d-H-i-s', time());
            if (Parser::generateCSV($orders, $folder, $name)){
                Orders::updateAll(['status_id' => 3,
                    'pull_date' => gmdate('Y-m-d H:i:s', time() + 7200),
                    'updated_at' => gmdate('Y-m-d H:i:s', time() + 7200),
                ], ['invoice_id'=> $data['data']]);
                return $name . '.csv';
            }
        }
        return false;
    }

    /**
     * Lists all Invoices models.
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
        
        $searchModel = new InvoiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $uploader = new Parser();

        return $this->render('index', [
            'uploader' => $uploader,
	        'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Invoices model.
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
     * Creates a new Invoices model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Invoice();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->ID]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Invoices model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
	public function actionUpdate($id)
	{
		$model = $this->findModel($id);
        $model->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($model->paid_amount >= $model->bill_amount) {
                Orders::updateAll(['status_id' => 2, 'updated_at' => gmdate('Y-m-d H:i:s', time() + 7200),], "invoice_id = '" . $model->id . "'");
            }

            \Yii::$app->session->setFlash('changed', $model->id);

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
     * Deletes an existing Invoices model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        //Delete related customers
        $deletedOrders = Orders::find()->where(['invoice_id' => $id])->all();

        $deletedOrderIds = [];
        foreach ($deletedOrders as $deletedOrder) {
            $deletedOrderIds[] = $deletedOrder->customer_id;
        }

        $orders = Orders::find()
            ->select(['customer_id, count(*) as cnt'])
            ->where(['in', 'customer_id', $deletedOrderIds])
            ->groupBy(['customer_id'])
            ->having(['<', 'cnt', 2])
            ->all();

        $customers = [];
        foreach ($orders as $order) {
            $customers[] = $order->customer_id;
        }

        if (!empty($customers)) Customers::deleteAll(['in', 'id', $customers]);
            //Delete related orders
        Orders::deleteAll(['invoice_id' => $id]);

        return $this->redirect(['index']);
    }

    public function actionUpload(){
        if (Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $file = new Parser;
            $file->file = UploadedFile::getInstance($file, 'file');
            $data = $file->upload();

            if ($data) { // file is uploaded successfully
                try {
                    $columns = ['invoiceid', 'paidamount',];

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

                    foreach ($data as $record){
                        $invoice = Invoices::findOne(['INVOICE_ID' => $record['invoiceid'],]);
                        if (empty($invoice)) continue;
                        $invoice->PAIDAMOUNT = $record['paidamount'];

                        if ($invoice->save(false)) {
                            if ($invoice->PAIDAMOUNT >= $invoice->BILLAMOUNT) {
                                Orders::updateAll(['STATUS_NAME' => 'Paid', 'PAID_DATE' => date('Y-m-d')], "INVOICE_ID = '" . $invoice->INVOICE_ID . "'");
                            }

                            continue;
                        } else {
	                        return [
		                        'code' => 400,
		                        'text' => 'Error!<br>Cannot write data.'
	                        ];
                        }
                    }

	                $text = count($data) . ' records imported successfully.';

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
        }
        return false;
    }

    /**
     * Finds the Invoices model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Invoices the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Invoice::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    private function csvValidation($data) {
        $rules = [
            'invoiceid' => '/^[A-Z0-9]{8}/',
            'paidamount' => '/^[0-9.,]{1,}/',
        ];

        $tips = [
            'invoiceid' => 'Value should contain only letters',
            'paidamount' => 'Allowed value format is 0.00',
        ];

        return Yii::$app->validation->csv($data, $rules, $tips);
    }
}
