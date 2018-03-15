<?php

namespace backend\controllers;

use backend\models\Customers;
use backend\models\Invoice;
use backend\models\Product;
use backend\models\Products;
use yii\filters\AccessControl;
use Yii;
use backend\models\Payments;
use backend\models\PaymentsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\Parser;
use yii\web\UploadedFile;
use backend\models\User;
use backend\models\Orders;
use backend\models\SiteConfig;
use Mandrill;
use yii\db\Expression;

/**
 * PaymentsController implements the CRUD actions for Payments model.
 */
class PaymentsController extends Controller
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
     * Lists all Payments models.
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

        $searchModel = new PaymentsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $uploader = new Parser();
        $waitForCollector = Payments::waitForCollector();

        return $this->render('index', [
            'uploader' => $uploader,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'waitForCollector' => $waitForCollector,
        ]);
    }

    /**
     * Displays a single Payments model.
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
     * Creates a new Payments model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Payments();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Payments model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($model->bill_amount <= $model->paid_amount) {
                if ($model->type_id == 1) {
                    $this->sendSuccessLetter($model);
                }
                $model->status_id = 13;
                $model->save();

                $order = Orders::find()->where(['token' => $model->token])->orderBy(['created_at' => SORT_DESC,])->one();
                $order->status_id = 20;
                $order->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);
                $order->save();
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
     * Deletes an existing Payments model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionMdelete()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $deleted = \Yii::$app
                ->db
                ->createCommand()
                ->delete('reminder', ['in', 'reminder.id', $data['data']])
                ->execute();

            if ($deleted > 0) {
                return [
                    'code' => 200,
                    'text' => $deleted . ' reminder(s) deleted',
                ];
            } else {
                return [
                    'code' => 400,
                    'text' => 'No reminders was deleted!'
                ];
            }
        }

        return false;
    }

    /**
     * Finds the Payments model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Payments the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Payments::findOne($id)) !== null) {
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
                    $columns = ['pubtoken', 'annualfee', 'feetaken', 'sex', 'title', 'firstname', 'lastname', 'address', 'zipcode', 'city',
                        'country', 'telephone', 'dob', 'mail', 'date', 'brandname'];

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
                        if (empty($record['pubtoken'])) {
                            $emptyRows++;
                            continue;
                        }

                        $order = Orders::find()->where(['token' => $record['pubtoken']])->one();

                        if (empty($order)) {
                            $customer = Customers::find()->where(['email' => $record['mail']])->one();

                            if (!empty($customer)) {
                                $order = new Orders();
                                //CREATE INVOICE
                                $invoice = new Invoice();

                                $invoice->user_id = Yii::$app->user->id;
                                $invoice->bill_amount = 0;
                                $invoice->id = $this->generateInvoiceId();
                                $invoice->paid_amount = 0;
                                $invoice->product_id = Product::getIdByName($record['brandname']);
                                $invoice->created_at = gmdate('Y-m-d H:i:s', time() + 7200);
                                $invoice->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);
                                $invoice->save();

                                $order->token = $record['pubtoken'];
                                $order->invoice_id = $invoice->id;
                                $order->status_id = 23;
                                $order->activation_date = $record['date'];
                                $order->updated_at =  gmdate('Y-m-d H:i:s', time() + 7200);
                                $order->created_at =  gmdate('Y-m-d H:i:s', time() + 7200);

                                //$order->recid = $invoice->product_id . date("ymd") . sprintf('%04d', $order->getPrimaryKey());
                                $order->card_name = Parser::generateCardName($record['firstname'], $record['lastname']);

                                $record['nationality'] = '';
                                $record['ipaddress'] = '';
                                $record['company'] = '';

                                $order->customer_id = $customer->id;

                                $order->save();
                            } else {
                                //Create old data
                                $order = new Orders();
                                //CREATE INVOICE
                                $invoice = new Invoice();

                                $invoice->user_id = Yii::$app->user->id;
                                $invoice->bill_amount = 0;
                                $invoice->id = $this->generateInvoiceId();
                                $invoice->paid_amount = 0;
                                $invoice->product_id = Product::getIdByName($record['brandname']);

                                $invoice->created_at = gmdate('Y-m-d H:i:s', time() + 7200);
                                $invoice->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);

                                $invoice->save();

                                $order->token = $record['pubtoken'];
                                $order->invoice_id = $invoice->id;
                                $order->status_id = 23;
                                $order->activation_date = $record['date'];
                                $order->updated_at =  gmdate('Y-m-d H:i:s', time() + 7200);
                                $order->created_at =  gmdate('Y-m-d H:i:s', time() + 7200);

                                //$order->recid = $invoice->product_id . date("ymd") . sprintf('%04d', $order->getPrimaryKey());
                                $order->card_name = Parser::generateCardName($record['firstname'], $record['lastname']);

                                $record['nationality'] = '';
                                $record['ipaddress'] = '';
                                $record['company'] = '';

                                $order->customer_id = Customers::getCustomerId($record, true);

                                $order->save();

                            }
                        } else {
                            $order->token = $record['pubtoken'];
                            $order->activation_date = $record['date'];
                            $order->save();
                        }


                        $payment = new Payments();

                        $payment->token = $record['pubtoken'];
                        $payment->bill_amount = $record['annualfee'];
                        $payment->paid_amount = $record['feetaken'];
                        //$payment->VALUTA = $record['date'];
                        $payment->status_id = 8;
                        $payment->created_at = gmdate('Y-m-d H:i:s', time() + 7200);
                        $payment->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);

                        if (!$payment->save(false)) {
                            return [
                                'code' => 400,
                                'text' => 'Error!<br>Cannot write data.'
                            ];
                        }
                    }

                    $paymentsNumber = count($data) - $emptyRows;
                    $text = $paymentsNumber . ' records imported successfully.
                        <br>' . $emptyRows . ' empty rows declined';
                    \Yii::$app->session->setFlash('success', 'Imported ' . $paymentsNumber . 'records.');
                    /*\Yii::$app->mailer->compose()
                                     ->setFrom([SiteConfig::option('contact_email') => SiteConfig::option('site_name')])
                                     ->setTo(User::findIdentity(Yii::$app->user->id)->email)
                                     ->setSubject('Bulk payment reminders upload result')
                                     ->setHtmlBody('Successfully imported ' . $paymentsNumber . 'records.')
                                     ->send();*/
                    return [
                        'code' => 200,
                        'text' => $text,
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

    public function actionDownload(){
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $orders = Payments::find()
                ->select([
                    'customer.email as `Email Address`',
                    'customer.firstname as `First Name`',
                    'customer.lastname as `Last Name`',
                    'reminder.token as `Token`',
                    'customer.title as `Title`',
                    'customer.address as `Address`',
                    'customer.city as `City`',
                    'customer.zipcode as `Postcode`',
                    'customer.country as `Country`',
                    'customer.dob as `DOB`',
                    'customer.telephone as `Telephone`',
                    'product.name as `Brand`',
                    'reminder.bill_amount as `Annual Fee`',
                    'reminder.first_at as Bestelldatum',
                    'max(case when card.activation_date is null then DATE_SUB(first_at, INTERVAL 365 DAY) else card.activation_date end) Rechnungsdatum'
                ])
                ->distinct()
                ->join('LEFT JOIN', 'card', 'reminder.token = card.token')
                ->join('LEFT JOIN', 'customer', 'card.customer_id = customer.id')
                ->join('LEFT JOIN', 'invoice', 'card.invoice_id = invoice.id')
                ->join('LEFT JOIN', 'product', 'invoice.product_id = product.id')
                ->where(['in', 'reminder.id', $data['data']])
                ->groupBy([
                    'customer.email',
                    'customer.firstname',
                    'customer.lastname',
                    'reminder.token',
                    'customer.title',
                    'customer.address',
                    'customer.city',
                    'customer.zipcode',
                    'customer.country',
                    'customer.dob',
                    'customer.telephone',
                    'product.name',
                    'reminder.bill_amount',
                    'reminder.first_at' 
                ])->orderBy([
                    'reminder.updated_at' => SORT_DESC,
                    'reminder.token' => SORT_DESC,
                ])->asArray()
                ->all();
            $folder = Yii::$app->controller->id;
            $name = $folder . '-list-' . date('Y-M-d-H-i-s', time());
            if (Parser::generateCSV($orders, $folder, $name)){
                return $name . '.csv';
            }
        }
        return false;
    }

    public function actionReport()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $orders = Payments::find()
                ->select([
                    'customer.email as `Email Address`',
                    'customer.firstname as `First Name`',
                    'customer.lastname as `Last Name`',
                    'reminder.token as `Token`',
                    'customer.title as `Title`',
                    'customer.address as `Address`',
                    'customer.city as `City`',
                    'customer.zipcode as `Postcode`',
                    'customer.country as `Country`',
                    'customer.dob as `DOB`',
                    'customer.telephone as `Telephone`',
                    'product.name as `Brand`',
                    'reminder.bill_amount as `Annual Fee`',
                    'reminder.first_at as Bestelldatum',
                    'max(case when card.activation_date is null then DATE_SUB(first_at, INTERVAL 365 DAY) else card.activation_date end) Rechnungsdatum'
                ])
                ->distinct()
                ->join('LEFT JOIN', 'card', 'reminder.token = card.token')
                ->join('LEFT JOIN', 'customer', 'card.customer_id = customer.id')
                ->join('LEFT JOIN', 'invoice', 'card.invoice_id = invoice.id')
                ->join('LEFT JOIN', 'product', 'invoice.product_id = product.id')
                ->where(['reminder.status_id' => 15])
                ->groupBy([
                    'customer.email',
                    'customer.firstname',
                    'customer.lastname',
                    'reminder.token',
                    'customer.title',
                    'customer.address',
                    'customer.city',
                    'customer.zipcode',
                    'customer.country',
                    'customer.dob',
                    'customer.telephone',
                    'product.name',
                    'reminder.bill_amount',
                    'reminder.first_at' 
                ])->asArray()
                ->all();

            if (empty($orders)) return false;
            $folder = Yii::$app->controller->id;
            $name = 'Report-to-CA-' . gmdate('Y-m-d-H-i-s', time() + 7200);
            if (Parser::generateCSV($orders, $folder, $name)) {
                Payments::updateAll(['status_id' => 16,
                    'updated_at' => gmdate('Y-m-d H:i:s', time() + 7200),
                ], ['reminder.status_id' => 15]);
                  
                return $name . '.csv';
            }
        }
        return false;
    }

    public function actionSend() {
        if (Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $reminders = Payments::updateAll(['status_id' => 24], [
                'type_id' => 0, 'status_id' => 8
            ]);

            if ($reminders > 0) {
                return [
                    'code' => 200,
                    'text' => $reminders . ' reminders sent successfully',
                ];
            } else {
                return [
                    'code' => 400,
                    'text' => 'No mails was send!'
                ];
            }
        }

//        if (Yii::$app->request->isAjax) {
//            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//            $ids = Yii::$app->request->post();
//
//            $successLetters = 0;
//
//            $payments = Payments::find()->where(['type_id' => 0])/***/
//            ->andWhere(['status_id' => 8])/***/
//            ->andWhere(['>', 'created_at', '2018-01-01'])/***/
//            ->each();
//
//            foreach ($payments as $id) {/***/
//                $payment = Payments::findOne($id->id);/***/
//                if (empty($payment)) continue;/***/
//                /*foreach ($ids['data'] as $id) {
//                    $payment = Payments::findOne($id);*/
//                if ($payment->type_id != 0 || $payment->status_id != 8) continue;
//
//                $order = Orders::find()->where(['token' => $payment->token])->one();
//                if (empty($order->customer->email)|| empty($order->invoice_id)) continue;
//
//                $payment->status_id = 24;
//                $payment->first_at = gmdate('Y-m-d H:i:s', time() + 7200);
//                $payment->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);
//                $payment->save();
//
//                $fee = $payment->bill_amount - $payment->paid_amount;
//                $activationDate = \Yii::$app->formatter->asDatetime($order->activation_date, "php:d-m-Y");
//
//                if (empty($order->invoice->product->name)) continue;
//
//                $subject = '1. Zahlungserinnerung - First Payment Reminder';
//                $letter = '
//	                <p style=\'color: #000;\'>Hallo ' . $order->customer->firstname . ',</p>
//                    <p style=\'color: #000;\'>Wir möchten Sie höflich daran erinnern, dass die Jahresgebühr für Ihre ' . $order->invoice->product->name . ' von €' . money_format('%.2n', $payment->bill_amount) . ' bereits fällig war.</p>
//                    <p style=\'color: #000;\'>Ihre Karte wurde am ' . $activationDate . ' aktiviert bzw. verschickt. Von Ihrem Kartenkonto konnten wir folgenden Betrag abbuchen: €' . money_format('%.2n', $payment->paid_amount) . '</p>
//                    <p style=\'color: #000;\'>Bitte bringen Sie daher in den nächsten 7 Tagen den offenen Betrag in Höhe von €' . money_format('%.2n', $fee) . '
//                    zur Einzahlung. Nach Verstreichen dieser Frist müssen wir diese Forderung leider an unser
//                    Inkassobüro übergeben. Dies führt eventuell zu einem negativen Schufa- oder KSV-Eintrag.</p>
//                    <p style=\'color: #000;\'>Vermeiden Sie Nachteile und überweisen Sie jetzt!</p>
//                    <p style=\'color: #000;\'>Konto-Empfänger: Card Compact Limited<br>
//                    IBAN: DE43 7507 0024 0516 3498 00<br>
//                    BIC/SWIFT: DEUTDEDB750<br>
//                    Verwendungszweck: Jahresgebühr ' . $order->customer->firstname . ' ' . $order->customer->lastname . ', ' . $order->token . '<br>
//                    Betrag: €' . money_format('%.2n', $fee) . '</p>
//                    <p style=\'color: #000;\'>Sollten Sie die Zahlung der Jahresgebühr zwischenzeitlich bereits durchgeführt haben, betrachten Sie diese E-Mail als gegenstandslos.</p>
//                    <p style=\'color: #000;\'>Für Rückfragen stehen wir Ihnen gerne auch telefonisch zur Verfügung unter +49 1807 667766 oder senden Sie uns eine Email an support@cardcompact.co.uk.</p>
//                    <p style=\'color: #000;\'>Mit freundlichen Grüßen,</p>
//                    <p style=\'color: #000;\'>Kundendienst</p>
//                    <p style=\'color: #000;\'>Card Compact Ltd.<br>29th Floor, One Canada Square, Canary Wharf, London E14 5DY, UK, VAT Reg. No.: GB119702813</p>
//                    <br>
//                    <br>
//                    <p style=\'color: #000;\'>Dear ' . $order->customer->firstname . ',</p>
//                    <p style=\'color: #000;\'>We would like to point out that the annual fee for your ' . $order->invoice->product->name . ' in the amount of €' . money_format('%.2n', $payment->bill_amount) . ' has already been overdue.</p>
//                    <p style=\'color: #000;\'>Your prepaid card was activated/shipped on ' . $activationDate . '.<br>
//                    We could charge your card account in the amount of €' . money_format('%.2n', $payment->paid_amount) . '</p>
//                    <p style=\'color: #000;\'>Please pay the outstanding amount of €' . money_format('%.2n', $fee) . ' within the next seven days. After
//                    the end of the time allowed for payment we will transfer the claim to a debt collecting agency for collection.
//                    This may cause additional costs and a negative entry in your credit report.</p>
//                    <p style=\'color: #000;\'>Avoid these disadvantages and pay now:</p>
//                    <p style=\'color: #000;\'>Payee: Card Compact Limited<br>
//                    IBAN: DE43 7507 0024 0516 3498 00<br>
//                    BIC/SWIFT: DEUTDEDB750<br>
//                    Reference code: annual fee ' . $order->customer->firstname . ' ' . $order->customer->lastname . ', ' . $order->token . '<br>
//                    Amount: €' . money_format('%.2n', $fee) . '</p>
//                    <p style=\'color: #000;\'>If you have any queries, please call our customer support in Germany at +49 1807 667766 or email us at support@cardcompact.co.uk.</p>
//                    <p style=\'color: #000;\'>Kind regards</p>
//                    <p style=\'color: #000;\'>Card Compact Ltd.<br>
//                    29th Floor, One Canada Square, Canary Wharf, London E14 5DY, UK, VAT Reg. No.: GB119702813</p>';
//
//                $sendResult = $this->mandrill('no-reply@cardcompact.uk', $order->customer->email, $subject, $letter);
//                $email = $order->customer->email;
//                if ($sendResult) $successLetters++;
//            }
//
//            $reminders = count($ids['data']);
//            $reminders = count($payments);/***/
//
//            $failLetters = $reminders - $successLetters;
//
//            if ($reminders > 0) {
//                return [
//                    'code' => 200,
//                    'text' => $successLetters . ' reminders sent successfully.<br>' . $failLetters . ' reminders declined.',
//                ];
//            } else {
//                return [
//                    'code' => 400,
//                    'text' => 'No mails was send!'
//                ];
//            }
//        }
    }

    public function mandrill($from, $to, $subject, $html, $file = null) {
        $mandrill = new Mandrill('k7dQwst_tKPZC4e0osA1AA');

        $message = [
            'html' => $html,
            'subject' => $subject,
            'from_email' => $from,
            'to' => [
                ['email' => $to,],
            ],
        ];
        $async = false;
        $ip_pool = 'Main Pool';
        $send_at = '2000-01-01 00:00:00';
        $result = $mandrill->messages->send($message, $async, $ip_pool, $send_at);

        return $result;
    }

    private function csvValidation($data) {
        $rules = [
            'pubtoken' => '/^\d{9}$/',
            'annualfee' => '/^\d{1,}.\d{2}$/',
            'feetaken' => '/^\d{1,}.\d{2}$/',
            'sex' => '/^(m|f)$/', //Sex Pattern
            'title' => '/^(herr|frau|dr|prof|mr|mrs|ms)$/', //Title Pattern
            'firstname' => '/^[a-z \'-äöüÄÖÜß]+$/', //FirstName Pattern
            'lastname' => '/^[a-z \'-äöüÄÖÜß]+$/', //LastName Pattern
            'telephone' => '/^0\d{6,}$/', //Phone number pattern
            'dob' => '/^\d{4}\-\d{2}\-\d{2}$/', //Date of birth pattern
            'mail' => '/^[a-z0-9_\-\.]{2,}@[a-z0-9_\-\.]{2,}\.[a-z]{2,}$/', //Email pattern
            'country' => '/^\d{3}$/', //Country Code Name
            'address' => '/^.{3,}$/', //Address pattern
            'zipcode' => '/^.{3,}$/', //Zipcode pattern
            'city' => '/^.{3,}$/', //City pattern
            'date' => '/^\d{4}\-\d{2}\-\d{2}$/', //Date
            'brandname' => '/^.{3,}$/', //Brandname pattern
        ];

        $tips = [
            'pubtoken' => 'Value should contain 9 digits',
            'annualfee' => 'Allowed value format is 0.00',
            'feetaken' => 'Allowed value format is 0.00',
            'sex' => 'Value should be m or f',
            'title' => 'Value should be herr, frau, dr, prof, mr, mrs, ms',
            'firstname' => 'Value should contain only letters',
            'lastname' => 'Value should contain only letters',
            'telephone' => 'Value should contain digits only and start with "0"',
            'dob' => 'Allowed value format is yyyy-mm-dd',
            'mail' => 'Value should be correct email',
            'country' => 'Value should contain only 3 digits',
            'address' => 'Value should contain minimum 3 symbols',
            'zipcode' => 'Value should contain minimum 3 symbols',
            'city' => 'Value should contain minimum 3 symbols',
            'date' => 'Allowed value format is yyyy-mm-dd',
            'brandname' => 'Brand name should contains minimum 3 symbols',
        ];

        return Yii::$app->validation->csv($data, $rules, $tips);
    }

    private function csvValidationPayments($data) {
        $rules = [
            'token' => '/^\d{9}$/',
            'amount' => '/^\d{1,}.\d{2}$/',
            'date' => '/^\d{4}\.\d{2}\.\d{2}$/',
        ];

        $tips = [
            'token' => 'Value should contain 9 digits',
            'amount' => 'Allowed value format is 0.00',
            'date' => 'Allowed value format is yyyy.mm.dd',
        ];

        return Yii::$app->validation->csv($data, $rules, $tips);
    }

    public function actionUploadcards()
    {
        if (Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $file = new Parser;
            $file->file = UploadedFile::getInstance($file, 'file');
            $data = $file->upload();
            if ($data) { // file is uploaded successfully
                try {
                    $columns = ['token', 'amount', 'date'];

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
                    $validationErrors = $this->csvValidationPayments($data);
                    if (!empty($validationErrors)) {
                        return [
                            'code' => 400,
                            'text' => $validationErrors,
                        ];
                    }

                    $emptyRows = 0;
                    foreach ($data as $record){
                        $payment = Payments::find()->where(['token' => $record['token']])->orderBy(['created_at' => SORT_DESC])->one();

                        if (!empty($payment)) {
                            $payment->updated_at = $record['date'];
                            $payment->paid_amount += $record['amount'];
                            if ($payment->bill_amount <= $payment->paid_amount) {
                                $payment->status_id = 2;
                                if ($payment->type_id == 2) {
                                    $order = Orders::find()->where(['token' => $payment->token])->orderBy(['created_at' => SORT_DESC,])->one();
                                    $order->status_id = 20;
                                    $order->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);
                                    $order->save();
                                } elseif ($payment->type_id == 1) {
                                    $this->sendSuccessLetter($payment);
                                    $payment->status_id = 13;
                                }
                            }
                            $payment->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);


                            if (!$payment->save(false)) {
                                return [
                                    'code' => 400,
                                    'text' => 'Error!<br>Cannot write data.'
                                ];
                            }
                        }
                    }
                    $paymentsNumber = count($data) - $emptyRows;
                    $text = $paymentsNumber . ' records imported successfully.
                        <br>' . $emptyRows . ' empty rows declined';
                    \Yii::$app->session->setFlash('success', 'Imported ' . $paymentsNumber . 'records.');

                    return [
                        'code' => 200,
                        'text' => $text,
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

    public function sendSuccessLetter($payment)
    {
        $order = Orders::find()->where(['token' => $payment->token])->one();
        $order->status_id = 6;
        $order->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);
        $order->save();

        $data = [
            'first_name' => $order->customer->firstname,
            'brand_name' => $order->invoice->product->name,
            'token' => $payment->token,
        ];

        $letter =  $this->renderPartial('successPaymentLetter', [
            'data' => $data,
        ]);

        $this->mandrill(
            'no-reply@cardcompact.uk',
            $order->customer->email,
            'Kündigungsbestätigung - Cancellation Confirmation',
            $letter
        );
    }

    function generateInvoiceId($length = 8)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        if (Invoice::findOne(['id' => $randomString])) $this->generateInvoiceId();

        return $randomString;
    }
}
