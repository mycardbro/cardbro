<?php

namespace backend\controllers;

use yii\filters\AccessControl;
use backend\models\SiteConfig;
use Yii;
use backend\models\Cards;
use backend\models\Orders;
use backend\models\CardsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\Parser;
use yii\web\UploadedFile;

/**
 * CardsController implements the CRUD actions for Cards model.
 */
class CardsController extends Controller
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
     * Lists all Cards models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CardsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $uploader = new Parser();

        return $this->render('index', [
            'uploader' => $uploader,
	        'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Cards model.
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
     * Creates a new Cards model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Cards();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->ID]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Cards model.
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

	public function actionClose($id)
    {
        $model = $this->findModel($id);
        $postData = Yii::$app->request->post();
        $model->STATUS_NAME = (!empty($postData['payment'])) ? 'Wait for closing' : 'Closed';

        if ($model->load($postData) && $model->save()) {
            if ($model->STATUS_NAME == 'Wait for closing') {
                $payment = $postData['payment'];
                $this->mandrill(
                    'outsoft@cardcompact.uk',
                    //User::findIdentity(Yii::$app->user->id)->email,
                    'prudnikov@outsoft.com',
                    'Card compact',
                    "
                    <p>Hallo (placeholder first name),</p>
                    <p>Vielen Dank für Ihre Nachricht.</p>
                    <p>Wir bedauern, dass Sie uns verlassen wollen.</p>
                    <p>Da Ihr gesetzliches Widerrufsrecht bereits erloschen ist, ist Ihr Kartenvertrag frühestens nach drei Jahren kündbar. Der vorzeitigen Auflösung des Vertragsverhältnisses können wir nur dann zustimmen, wenn Sie Ihre vertraglichen Verpflichtungen bedient haben.</p>
                    <p>Bitte überweisen Sie daher die ausstehenden Gebühren wie folgt:</p>
                    <p>Zahlungsempfänger:  Card Compact Ltd.<br />
                        IBAN:  DE187001111011177740 03<br />
                        BIC: DEKT DE 7G XXX<br />
                        Verwendungszweck:  (placeholder 9digit token)<br />
                        Betrag: €" . $payment . " inkl. €10 Kontoschließungsgebühr</p>
                    <p>Nach Eingang der Jahres- und Kontoschließungsgebühr werden wir Ihr Konto schließen!</p>
                    <p>Für Rückfragen stehen wir Ihnen gerne auch telefonisch zur Verfügung unter +49 1807 667766 oder senden Sie uns eine Email an support@cardcompact.co.uk.</p>
                    <p>Mit freundlichen Grüßen</p>
                    <p>Kundendienst</p>
                    <p>Card Compact Ltd.<br />
                        29th Floor, One Canada Square, Canary Wharf, London E14 5DY, UK, VAT Reg. No.: GB119702813</p>
                    <p>Dear (placeholder first name),</p>
                    <p>Thank you for your message.</p>
                    <p>We are sorry to hear that you would like to cancel your card account.</p>
                    <p>As your right of cancellation has already expired, we can only agree to the premature termination if you pay all outstanding annual fees and costs according to your 3-years-contract.</p>
                    <p>Please transfer the outstanding annual fees below to our bank account:</p>
                    <p>Payee: Card Compact Ltd.<br />
                        IBAN: DE18 7001 1110 1117 7740 03<br />
                        BIC: DEKT DE 7G XXX<br />
                        Reference Code: (placeholder 9 digit card)<br />
                        Amount: €" . $payment . " including €10 account closure fee</p>
                    <p>Once all fees have been paid, we will be able to close your card account.</p>
                    <p>If you have any queries, please email us at support@cardcompact.co.uk or ring us at +49 1807 667766.</p>
                    <p>Kind regards</p>
                    <p>Card Compact Ltd.</p>
                    <p>29th Floor, One Canada Square, Canary Wharf, London E14 5DY, UK, VAT Reg. No.: GB119702813</p>
                ");
            }

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
     * Deletes an existing Cards model.
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
     * Finds the Cards model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Cards the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Cards::findOne($id)) !== null) {
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
