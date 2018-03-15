<?php

namespace backend\controllers;

use backend\models\Payments;
use yii\filters\AccessControl;
use backend\models\Customers;
use backend\models\Invoices;
use backend\models\Invoice;
use backend\models\Action;
use yii;
use backend\models\Orders;
use backend\models\OrdersSearch;
use backend\models\Product;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\Parser;
use yii\web\UploadedFile;
use backend\models\User;
use backend\models\SiteConfig;
use Swift_Mailer;
use Swift_Message;
use Swift_Attachment;
use Swift_SendmailTransport;
use Mandrill;
use yii\db\Expression;
use backend\models\CardSending;
use backend\models\Country;

/**
 * OrdersController implements the CRUD actions for Orders model.
 */
class OrdersController extends Controller
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
                        'roles' => ['admin', 'manager', 'partner', 'support'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'upload' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Orders models.
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

        $searchModel = new OrdersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $uploader = new Parser();
        $uploaderCard = new Parser();
        $products = Product::getAvailableProducts(Yii::$app->user->id);

        return $this->render('index', [
            'uploader' => $uploader,
            'uploaderCard' => $uploaderCard,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'products' => $products,
        ]);
    }

    /**
     * Displays a single Orders model.
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
     * Creates a new Orders model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Orders();
        //$model->OWNER_ID = Yii::$app->user->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Orders model.
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
            ]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionReplace($id)
    {
        $ip = Yii::$app->request->userIP;
        $postData = Yii::$app->request->post();

        $model = $this->findModel($id);
        $model_new = new Orders();
        $quantityCards = $model->getOrderQuantityByToken();
        $roleName = User::getRoleName(Yii::$app->user->id);
        
        //replacement card has ID +1
        $model->status_id = 4;
        $model->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);
        //$productId = $model->product_id + 1;

        $status = (!empty($postData['options'])) ? 11 : 22;
        if (!empty($postData['options'])) {
            $price =  ($postData['payment']) ?? 0;
            $model_new->invoice_id = Invoice::createReplacementInvoice($price, $model->invoice->product_id);

            //Data for mail
            $totalPrice = $price + 9;
            $productName = $model->invoice->product->name;
            $firstName =  $model->customer->firstname;
            $lastName =  $model->customer->lastname;
            $address =  $model->customer->address;
            $city =  $model->customer->city;
            $email = $model->customer->email;
            $telephone = $model->customer->telephone;

            $reminder = new Payments();
            $reminder->token = $model->token;
            $reminder->bill_amount = $totalPrice;
            $reminder->type_id = 2;
            $reminder->status_id = 21;
            $reminder->created_at = gmdate('Y-m-d H:i:s', time() + 7200);
            $reminder->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);

            $reminder->save();

            $this->mandrill(
                'no-reply@cardcompact.uk',
                //$model->customer->email,
                $email,
                'Ersatzkartenbestellung - Replacement Card Order',
                "
                <p>Hallo $firstName,</p>
                <p>Gerne bestellen wir Ihnen eine Ersatzkarte.</p>
                <p>Bitte überweisen Sie Ihre anteilige Jahresgebühr in Höhe von € $price sowie die vertraglich vereinbarte Ersatzkartengebühr von €9 auf folgendes Konto:
                <p>Zahlungsempfänger: Card Compact Ltd./Prepaid24<br>
                IBAN: DE43 7507 0024 0516 3498 00<br>
                BIC: DEUTDEDB750<br>
                Verwendungszweck: RC $model->token-00$quantityCards<br>
                Betrag: € $totalPrice </p>
                <p>Ihre neue  $productName von Card Compact wird an folgende Anschrift geschickt.</p>
                <p>Vorname:                    $firstName<br>
                Nachname:                      $lastName<br>
                Anschrift, Hausnummer:         $address<br>
                PLZ, Ort:                      $city<br>
                eMail-Adresse:                 $email<br>
                Mobilfunknummer:               $telephone</p>
                <p>Bitte kontrollieren Sie Ihre Daten sorgfältig und teilen Sie uns mit, falls sich diese geändert haben.</p>
                <p>Sobald der Rechnungsbetrag auf unserem Konto eingegangen ist, werden wir die Ersatzkarte für Sie in Auftrag geben. Der Versand dauert im Anschluss ca. 5 - 7 Werktage.</p>
                <p>Für Rückfragen stehen wir Ihnen gerne per Telefon unter +49 (0)1807 667766 oder per Email unter support@cardcompact.cards zur Verfügung.</p>
                <p>Mit freundlichen Grüßen</p>
                <p>Card Compact Limited<br>
                www.cardcompact.com<br>
                www.cardcompact.cards<br>
                www.facebook.com/cardcompact<br>
                www.twitter.com/cardcompact</p>


                <p>Hello $firstName,</p>
                <p>We are glad to order a replacement card for you.</p>
                <p>Please transfer the partial annual fee in the amount of € $price and as contracted the fee for the replacement card of €9 to the following bank account:</p>
                <p>Payee Card Compact Ltd./Prepaid24 GmbH<br>
                IBAN: DE43 7507 0024 0516 3498 00<br>
                BIC: DEUTDEDB750<br>
                Reference: RC $model->token-00$quantityCards<br>
                Amount: € $totalPrice</p>
                <p>Your new $productName from Card Compact will be shipped to the following address:</p>
                <p>First name:                 $firstName<br>
                Last name:                     $lastName<br>
                Address:                       $address<br>
                Postcode, city:                $city<br>
                Email address:                 $email<br>
                Mobile phone:                  $telephone</p>
                <p>Please check your personal data carefully and tell us if there has anything changed.</p>
                <p>As soon as the payment has come through on our bank account, we will produce the replacement card for you. Shipping takes 5 - 7 business days.</p>
                <p>If you have any queries, please call us at +49 (0)1807 667766 or email us at support@cardcompact.cards.</p>
                <p>Kind regards</p>
                <p>Card Compact Limited<br>
                www.cardcompact.com<br>
                www.cardcompact.cards<br>
                www.facebook.com/cardcompact<br>
                www.twitter.com/cardcompact</p>
                ");
        }

        $model_new->attributes = $model->attributes;
        $model_new->id = null;
        $model_new->updated_at = gmdate('Y-m-d H:i:s', time() + 7201);
        $model_new->created_at = gmdate('Y-m-d H:i:s', time() + 7200);

        $model_new->status_id = $status;


        if ($model->load(Yii::$app->request->post()) && $model_new->save() && $model->save()) {
            $model->replacement_id = intval($postData['replacement_id']);
            $model->save();
            \Yii::$app->session->setFlash('changed', $model_new->id);

            $lastYesterdayOrder = Orders::find('id')->where(['<', 'created_at', gmdate("Y-m-d")])->orderBy(['id' => SORT_DESC])->one();
            $lastYesterdayId = $lastYesterdayOrder->id;
            $recid = $model_new->getPrimaryKey() - $lastYesterdayId;
            $model_new->recid = $model->invoice->product->crdproduct . date("ymd") . sprintf('%04d', $recid);
            $model_new->save();

                    $this->saveAction(
                        $invoice->user->id,
                        $ip,
                        2,
                        $invoice->product->id,
                        $amount
                    );

            return $this->redirect('index');
        }

        if (Yii::$app->request->isAjax) {
            if ($roleName == 'Administrator' || $roleName == 'Manager') {
                return $this->renderAjax('replace', [
                    'model' => $model,
                ]);
            } else {
                if ($model->getOrderQuantityByToken() > 1) {
                    return $this->renderAjax('access', [
                        'model' => $model,
                        'role'  => $roleName,
                    ]);
                } else {
                    return $this->renderAjax('replace', [
                        'model' => $model,
                        'role'  => $roleName,
                    ]);
                }
            }
        } else {
            if ($roleName == 'Administrator' || $roleName == 'Manager') {
                return $this->render('replace', [
                    'model' => $model,
                ]);
            } else {
                if ($model->getOrderQuantityByToken() > 1) {
                    return $this->render('access', [
                        'model' => $model,
                        'role'  => $roleName,
                    ]);
                } else {
                    return $this->render('replace', [
                        'model' => $model,
                        'role'  => $roleName,
                    ]);
                }
            }
        }
    }

    public function actionNew($id)
    {
        $ip = Yii::$app->request->userIP;
        $postData = Yii::$app->request->post();
        $model = new Orders();
        $products = Product::getAvailableProducts(Yii::$app->user->id);
        $countries = Country::getAll();

        if ($model->load(Yii::$app->request->post())) {
            $status = ($postData['options']) ? 11 : 22;
            $price =  ($postData['payment']) ?? 0;
            $customer = Customers::find()->where(['id' => $id])->one();
            $customer->nationality = $postData['country_code'];
            $customer->save();
            $model->token = $postData['token'];
            $model->invoice_id = Invoice::createReplacementInvoice(0, $postData['product_id']);;
            $model->status_id = $status;
            $model->updated_at =  gmdate('Y-m-d H:i:s', time() + 7200);
            $model->created_at =  gmdate('Y-m-d H:i:s', time() + 7200);
            $model->card_name = Parser::generateCardName($customer->firstname, $customer->lastname);
            $model->customer_id = $id;

            $lastYesterdayOrder = Orders::find('id')->where(['<', 'created_at', gmdate("Y-m-d")])->orderBy(['id' => SORT_DESC])->one();
            $lastYesterdayId = $lastYesterdayOrder->id;
            $model->save();
            $recid = $model->getPrimaryKey() - $lastYesterdayId;
            $model->recid = $model->invoice->product->crdproduct . date("ymd") . sprintf('%04d', $recid);
            $model->save();
            \Yii::$app->session->setFlash('changed', $model->getPrimaryKey());

            $this->saveAction(
                $model->invoice->user->id,
                $ip,
                2,
                $postData['product_id'],
                $price
            );

            if ($postData['options']) {
                //Data for mail
                $totalPrice = $price + 9;
                $productName = $model->invoice->product->name;
                $firstName =  $model->customer->firstname;
                $lastName =  $model->customer->lastname;
                $address =  $model->customer->address;
                $city =  $model->customer->city;
                $email = $model->customer->email;
                $telephone = $model->customer->telephone;

                $reminder = new Payments();
                $reminder->token = $model->token;
                $reminder->bill_amount = $totalPrice;
                $reminder->type_id = 2;
                $reminder->status_id = 21;
                $reminder->created_at = gmdate('Y-m-d H:i:s', time() + 7200);
                $reminder->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);

                $reminder->save();

                $this->mandrill(
                'no-reply@cardcompact.uk',
                //$model->customer->email,
                $email,
                'Ersatzkartenbestellung - Replacement Card Order',
                "
                <p>Hallo $firstName,</p>
                <p>Gerne bestellen wir Ihnen eine Ersatzkarte.</p>
                <p>Bitte überweisen Sie Ihre anteilige Jahresgebühr in Höhe von € $price sowie die vertraglich vereinbarte Ersatzkartengebühr von €9 auf folgendes Konto:
                <p>Zahlungsempfänger: Card Compact Ltd./Prepaid24<br>
                IBAN: DE43 7507 0024 0516 3498 00<br>
                BIC: DEUTDEDB750<br>
                Verwendungszweck: RC $model->token-001<br>
                Betrag: € $totalPrice </p>
                <p>Ihre neue  $productName von Card Compact wird an folgende Anschrift geschickt.</p>
                <p>Vorname:                    $firstName<br>
                Nachname:                      $lastName<br>
                Anschrift, Hausnummer:         $address<br>
                PLZ, Ort:                      $city<br>
                eMail-Adresse:                 $email<br>
                Mobilfunknummer:               $telephone</p>
                <p>Bitte kontrollieren Sie Ihre Daten sorgfältig und teilen Sie uns mit, falls sich diese geändert haben.</p>
                <p>Sobald der Rechnungsbetrag auf unserem Konto eingegangen ist, werden wir die Ersatzkarte für Sie in Auftrag geben. Der Versand dauert im Anschluss ca. 5 - 7 Werktage.</p>
                <p>Für Rückfragen stehen wir Ihnen gerne per Telefon unter +49 (0)1807 667766 oder per Email unter support@cardcompact.cards zur Verfügung.</p>
                <p>Mit freundlichen Grüßen</p>
                <p>Card Compact Limited<br>
                www.cardcompact.com<br>
                www.cardcompact.cards<br>
                www.facebook.com/cardcompact<br>
                www.twitter.com/cardcompact</p>


                <p>Hello $firstName,</p>
                <p>We are glad to order a replacement card for you.</p>
                <p>Please transfer the partial annual fee in the amount of € $price and as contracted the fee for the replacement card of €9 to the following bank account:</p>
                <p>Payee Card Compact Ltd./Prepaid24 GmbH<br>
                IBAN: DE43 7507 0024 0516 3498 00<br>
                BIC: DEUTDEDB750<br>
                Reference: RC $model->token-001<br>
                Amount: € $totalPrice</p>
                <p>Your new $productName from Card Compact will be shipped to the following address:</p>
                <p>First name:                 $firstName<br>
                Last name:                     $lastName<br>
                Address:                       $address<br>
                Postcode, city:                $city<br>
                Email address:                 $email<br>
                Mobile phone:                  $telephone</p>
                <p>Please check your personal data carefully and tell us if there has anything changed.</p>
                <p>As soon as the payment has come through on our bank account, we will produce the replacement card for you. Shipping takes 5 - 7 business days.</p>
                <p>If you have any queries, please call us at +49 (0)1807 667766 or email us at support@cardcompact.cards.</p>
                <p>Kind regards</p>
                <p>Card Compact Limited<br>
                www.cardcompact.com<br>
                www.cardcompact.cards<br>
                www.facebook.com/cardcompact<br>
                www.twitter.com/cardcompact</p>
                ");
            }

            return $this->redirect('index');
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('new', [
                'model' => $model,
                'products' => $products,
                'countries' => $countries,
            ]);
        } else {
            return $this->render('new', [
                'model' => $model,
                'products' => $products,
                'countries' => $countries,
            ]);
        }
    }

    /**
     * Deletes an existing Orders model.
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
     * Finds the Orders model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Orders the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Orders::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionUpload()
    {
        $ip = Yii::$app->request->userIP;
        $startTime = time();
        if (Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $postData = Yii::$app->request->post();
            if (!empty($postData[0]['track3'])) {
                $res = $this->actionUploadCards($postData);
                return [
                    'code' => $res['code'],
                    'text' => $res['text'],
                ];
            }

            if (empty($postData['card'])) {
                return [
                    'code' => 400,
                    'text' => 'You should choose product name',
                ];
            }


            $file = new Parser;
            $file->file = UploadedFile::getInstance($file, 'file');
            $data = $file->upload();
            if ($data) { // file is uploaded successfully
                try {
                    $columns = ['sex', 'title', 'firstname', 'lastname', 'company', 'address', 'zipcode', 'city',
                        'country', 'orderdate', 'telephone', 'dob', 'mail', 'nationality', 'ipaddress', 'corporatecard',];

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

                    //CALCULATE AMOUNT FOR INVOICE
                    $amount = 0;
                    foreach ($data as &$client) {
                        $amount += (float) Product::findOne(intval($postData['card']))->price;
                        $client['customer_id'] = Customers::getCustomerId($client, true);
                    }

                    $lastYesterdayOrder = Orders::find('id')->where(['<', 'created_at', gmdate("Y-m-d")])->orderBy(['id' => SORT_DESC])->one();
                    $lastYesterdayId = $lastYesterdayOrder->id;

                    //CREATE INVOICE
                    $invoice = new Invoice();

                    $invoice->user_id = Yii::$app->user->id;
                    $amount = money_format('%.2n', $amount * (1 + $invoice->user->company->vat / 100));
                    $invoice->bill_amount = $amount;
                    $invoice->id = $this->generateInvoiceId();
                    $invoice->paid_amount = 0;
                    $invoice->product_id = intval($postData['card']);
                    $invoice->created_at = gmdate('Y-m-d H:i:s', time() + 7200);
                    $invoice->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);

                    $username = $invoice->user->username;
                    //$amount = money_format('%.2n', $amount * (1 + $invoice->user->vat_perc/100));

                    $emptyRows = 0;
                    $cardsNumber = 0;

                    $new_records = [];
                    $num = 1;

                    foreach ($data as $record) {
                        if (empty($record['mail'])) {
                            $emptyRows++;
                            continue;
                        }

                        if (Customers::findOne(['email' => 'mail'])) {
                            $customer = Customers::findOne(['email' => 'mail']);
                        } else {
                            $customer = new Customers();
                        }

                        $order = new Orders();

                        $order->invoice_id = $invoice->id;
                        $order->status_id = 1;
                        $order->updated_at =  gmdate('Y-m-d H:i:s', time() + 7200);
                        $order->created_at =  gmdate('Y-m-d H:i:s', time() + 7200);

                        $order->recid = $invoice->product_id . date("ymd") . sprintf('%04d', $order->getPrimaryKey());
                        $order->card_name = Parser::generateCardName($record['firstname'], $record['lastname']);
                        $order->customer_id = $record['customer_id'];
                        $order->order_date = $record['orderdate'];

                        if ($order->save(false)) {
                            $new_records[] = $order->getPrimaryKey();
                            $cardsNumber++;
                            $recid = $order->getPrimaryKey() - $lastYesterdayId;
                            $order->recid = $invoice->product->crdproduct . date("ymd") . sprintf('%04d', $recid);
                            $order->save();

                            /*$data = [];
                            $data['firstName'] = $order->customer->firstname;
                            $data['brandName'] = $invoice->product->name;
                            $this->mandrill(
                                'no-reply@cardcompact.uk',
                                $order->customer->email,
                                $data['firstName'] . ', Ihre ' . $data['brandName'] . ' Mastercard ist unterwegs - your ' . $data['brandName'] . ' Mastercard is coming soon',
                                $this->sendUploadLetter($data)
                            );*/
                        } else {
                            return [
                                'code' => 400,
                                'text' => 'Error!<br>Cannot write data test.'
                            ];
                        }

                        $num++;
                    }


                    $pdfData = [];
                    $pdfData['invoiceId'] = $invoice->id;
                    $pdfData['productId'] = $postData['card'];
                    $pdfData['price'] = Product::findOne(intval($postData['card']))->price;
                    $pdfData['cardNum'] = $cardsNumber;
                    $pdfData['username'] = $invoice->user->username;
                    $pdfData['company'] = $invoice->user->company->name;
                    $pdfData['address'] = $invoice->user->company->address;
                    $pdfData['postal_code'] = $invoice->user->company->postal_code;
                    $pdfData['country'] = $invoice->user->company->country;
                    $pdfData['city'] = $invoice->user->company->city;
                    $pdfData['vatId'] = $invoice->user->company->vat_id;
                    $pdfData['vatPerc'] = $invoice->user->company->vat;
                    $pdfData['cardName'] = Product::findOne(intval($postData['card']))->name;

                    $this->createInvoicePdf($pdfData);          

                    \Yii::$app->session->setFlash('new_records', $new_records);

                    $invoice->save();

                    $finishTime = time();
                    $uploadTime = $finishTime - $startTime;
                    $text = $cardsNumber . ' records imported successfully.
                        <br>' . $emptyRows . ' empty rows declined
                        <br>Invoice_id: ' . $invoice->id . '<br>Amount: ' . $amount . ' Euro<br>Upload Time: ' . $uploadTime . ' sec';


                    $this->mandrill(
                        'invoice@cardcompact.uk',
                        User::findIdentity(Yii::$app->user->id)->email,
                        'Your Invoice ' . $invoice->id,
                        "<p style='color: #000;'>Dear $username,</p>
                             <p style='color: #000;'>Thank you for your order.</p>
                             <p style='color: #000;'>We have received the following card request order: $cardsNumber have been placed.</p>
                             <p style='color: #000;'>Please pay as follows within five days.</p>
                             <p style='color: #000;'>Payee: Card Compact Limited<br />
                                 IBAN: DE47 7405 0000 0030 2362 51<br />
                                 BIC: BYLADEM1PAS<br />
                                 Name of bank: Sparkasse Passau<br />
                                 Invoice ID: $order->invoice_id<br />
                                 Amount: € $amount
                             </p>
                             <p style='color: #000;'>If you have any questions, please email us at invoice@cardcompact.co.uk.</p>
                             <p style='color: #000;'>Kind regards</p>
                             <p style='color: #000;'>Card Compact Limited<br />
                                 29th Floor, One Canada Square, Canary Wharf | London,  E14 5DY, UK, Telephone +44 (0) 207 7121488</p>",
                        $invoice->id
                    );

                    \Yii::$app->db->createCommand("
                        INSERT INTO card_sending( email, first_name, brand_name, token ) SELECT u.email, u.firstname, p.name, c.token FROM card c LEFT JOIN customer u on 
 c.customer_id = u.id LEFT JOIN invoice i on c.invoice_id = i.id LEFT JOIN product p on i.product_id = p.id WHERE c.invoice_id =  '$invoice->id' AND c.status_id IN ( 1, 2, 3, 18 )")->execute();

                    $this->saveAction(
                        $invoice->user->id,
                        $ip,
                        1,
                        $invoice->product->id,
                        $amount
                    );

                    return [
                        'code' => 200,
                        'text' => $text,
                    ];
                } catch (\Exception $e) {
                    \Yii::$app->session->setFlash('error', $e->getMessage());
                    return [
                        'code' => 400,
                        'text' => 'Error!<br>No data loaded!' . $e->getMessage()
                    ];
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

    public function actionClose($id)
    {
        $model = $this->findModel($id);
        $model->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);
        $postData = Yii::$app->request->post();
        $model->status_id = (!empty($postData['payment'])) ? 7 : 6;

        if ($model->load($postData) && $model->save()) {
            \Yii::$app->session->setFlash('changed', $model->id);
            if ($model->status_id == 7) {
                $payment = $postData['payment'];
                $reminder = new Payments();
                $reminder->token = $model->token;
                $reminder->bill_amount = $postData['payment'];
                $reminder->type_id = 1;
                $reminder->status_id = 19;
                $reminder->created_at = gmdate('Y-m-d H:i:s', time() + 7200);
                $reminder->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);

                $reminder->save();

                $name = $model->customer->firstname;
                $this->mandrill(
                    'cancellation@cardcompact.uk',
                    //User::findIdentity(Yii::$app->user->id)->email,
                    $model->customer->email,
                    'Kontoschließung - Card Account Closure',
                    "
                    <p>Hallo $name,</p>
                    <p>Vielen Dank für Ihre Nachricht.</p>
                    <p>Wir bedauern, dass Sie uns verlassen wollen.</p>
                    <p>Da Ihr gesetzliches Widerrufsrecht bereits erloschen ist, ist Ihr Kartenvertrag frühestens nach drei Jahren kündbar. Der vorzeitigen Auflösung des Vertragsverhältnisses können wir nur dann zustimmen, wenn Sie Ihre vertraglichen Verpflichtungen bedient haben.</p>
                    <p>Bitte überweisen Sie daher die ausstehenden Gebühren wie folgt:</p>
                    <p>Zahlungsempfänger:  Card Compact Ltd.<br />
                        IBAN:  DE43 7507 0024 0516 3498 00<br />
                        BIC: DEUTDEDB750<br />
                        Verwendungszweck:  $model->token<br />
                        Betrag: €" . $payment . " inkl. €10 Kontoschließungsgebühr</p>
                    <p>Nach Eingang der Jahres- und Kontoschließungsgebühr werden wir Ihr Konto schließen!</p>
                    <p>Für Rückfragen stehen wir Ihnen gerne auch telefonisch zur Verfügung unter +49 1807 667766 oder senden Sie uns eine Email an support@cardcompact.co.uk.</p>
                    <p>Mit freundlichen Grüßen</p>
                    <p>Kundendienst</p>
                    <p>Card Compact Ltd.<br />
                        29th Floor, One Canada Square, Canary Wharf, London E14 5DY, UK, VAT Reg. No.: GB119702813</p>
                    <p>Dear $name,</p>
                    <p>Thank you for your message.</p>
                    <p>We are sorry to hear that you would like to cancel your card account.</p>
                    <p>As your right of cancellation has already expired, we can only agree to the premature termination if you pay all outstanding annual fees and costs according to your 3-years-contract.</p>
                    <p>Please transfer the outstanding annual fees below to our bank account:</p>
                    <p>Payee: Card Compact Ltd.<br />
                        IBAN:  DE43 7507 0024 0516 3498 00<br />
                        BIC: DEUTDEDB750<br />
                        Reference Code: $model->token<br />
                        Amount: €" . $payment . " including €10 account closure fee</p>
                    <p>Once all fees have been paid, we will be able to close your card account.</p>
                    <p>If you have any queries, please email us at support@cardcompact.co.uk or ring us at +49 1807 667766.</p>
                    <p>Kind regards</p>
                    <p>Card Compact Ltd.</p>
                    <p>29th Floor, One Canada Square, Canary Wharf, London E14 5DY, UK, VAT Reg. No.: GB119702813</p>
                ");
            }

                    $this->saveAction(
                        $invoice->user->id,
                        $ip,
                        3,
                        $invoice->product->id,
                        $amount
                    );

            return $this->redirect('index');
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('close', [
                'model' => $model,
            ]);
        } else {
            return $this->render('close', [
                'model' => $model,
            ]);
        }
    }

    public function actionDownload()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
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
                ->where(['in', 'card.id', $data['data']])
                ->asArray()
                ->all();
            $folder = Yii::$app->controller->id;
            $name = $folder . '-list-' . date('Y-M-d-H-i-s', time());
            if (Parser::generateCSV($orders, $folder, $name)) {
                //Orders::updateAll(['status_id' => 3, 'pull_date' => date('Y-m-d'),], ['id' => $data['data']]);
                return $name . '.csv';
            }
        }
        return false;
    }

    public function actionPartner()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $orders = Orders::find()
                ->select([
                    'token as PubToken',
                    'customer.title as Title',
                    'customer.lastname as LastName',
                    'customer.firstname as FirstName',
                    'customer.address as Addrl1',
                    'customer.city as City',
                    'customer.zipcode as PostCode',
                    'customer.country as Country',
                    'customer.email as Email',
                    'customer.telephone as Mobile',
                    'customer.dob as DOB',
                    'product.name as ProductName',
                    'card.created_at as CreationDate',
                    'DATE_ADD(card.pull_date, INTERVAL 10 DAY) ShippingDate',
                    'card.activation_date as ActivationDate',
                    'status.name as CardStatus',
                ])
                ->join('LEFT JOIN', 'invoice', 'card.invoice_id = invoice.id')
                ->join('LEFT JOIN', 'product', 'invoice.product_id = product.id')
                ->join('LEFT JOIN', 'customer', 'card.customer_id = customer.id')
                ->join('LEFT JOIN', 'status', 'card.status_id = status.id')
                ->where(['in', 'card.id', $data['data']])
                ->asArray()
                ->all();
            $folder = Yii::$app->controller->id;
            $name = $folder . '-list-' . date('Y-M-d-H-i-s', time());
            if (Parser::generateCSV($orders, $folder, $name)) {
                //Orders::updateAll(['status_id' => 3, 'pull_date' => date('Y-m-d'),], ['id' => $data['data']]);
                return $name . '.csv';
            }
        }
        return false;
    }

    function generateInvoiceId($length = 8)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        if (Invoices::findOne(['INVOICE_ID' => $randomString])) $this->generateInvoiceId();

        return $randomString;
    }

    function generateToken()
    {
        $token = rand(100000000, 999999999);
        if (Orders::findOne(['token' => $token])) $this->generateInvoiceId();

        return $token;
    }

    function createInvoicePdf($pdfData)
    {
        $pdf = \Yii::$app->pdf;
        $pdf->content = $this->renderPartial('pdf', ['pdfData' => $pdfData]);
        $pdf->cssInline = 'button,hr,input{overflow:visible}audio,canvas,progress,video{display:inline-block}progress,sub,sup{vertical-align:baseline}[type=checkbox],[type=radio],legend{box-sizing:border-box;padding:0}html{line-height:1.15;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}article,aside,details,figcaption,figure,footer,header,main,menu,nav,section{display:block}h1{font-size:2em}figure{margin:1em 40px}hr{box-sizing:content-box;height:0}code,kbd,pre,samp{font-family:monospace,monospace;font-size:1em}a{background-color:transparent;-webkit-text-decoration-skip:objects}abbr[title]{border-bottom:none;text-decoration:underline;text-decoration:underline dotted}dfn{font-style:italic}mark{background-color:#ff0;color:#000}.table__bottom td,.table__top{background:#77BB41 }small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative}sub{bottom:-.25em}sup{top:-.5em}audio:not([controls]){display:none;height:0}img{border-style:none}svg:not(:root){overflow:hidden}button,input,optgroup,select,textarea{font-family:sans-serif;font-size:100%;line-height:1.15;margin:0}button,select{text-transform:none}[type=reset],[type=submit],button,html [type=button]{-webkit-appearance:button}[type=button]::-moz-focus-inner,[type=reset]::-moz-focus-inner,[type=submit]::-moz-focus-inner,button::-moz-focus-inner{border-style:none;padding:0}[type=button]:-moz-focusring,[type=reset]:-moz-focusring,[type=submit]:-moz-focusring,button:-moz-focusring{outline:ButtonText dotted 1px}fieldset{padding:.35em .75em .625em}legend{color:inherit;display:table;max-width:100%;white-space:normal}textarea{overflow:auto}[type=number]::-webkit-inner-spin-button,[type=number]::-webkit-outer-spin-button{height:auto}[type=search]{-webkit-appearance:textfield;outline-offset:-2px}[type=search]::-webkit-search-cancel-button,[type=search]::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}body,h1,h2,h3,h4,h5,h6,p{font-family:Arial,Helvetica;margin:0}summary{display:list-item}[hidden],template{display:none}strong{font-weight:900}.header{margin-top:20px;margin-bottom:65px;height:85px}.header__title{font-size:50px;font-weight:100}.header__logo{width:260px}.left-column{color:grey}.table__bottom td,.table__top td{color:#fff;text-align:center}.left-column__logo{width:150px;margin-bottom:120px}.left-column__content__block__content,.left-column__content__block__link,.left-column__content__block__title{font-size:12px}.table{margin-bottom:20px}.right-column__title{font-size:16px;font-weight:400}.right-column__content_date__text{font-weight:900}.right-column__content_customizer__title{font-size:16px;font-weight:400}table{border-spacing:0}.table td{border-bottom:2px solid #000;border-right:2px solid #000;padding:2px 17px;min-width:95px}tr td:last-child{border-right:none}.register{font-size:10px;margin-top:50px}';
        $pdf->filename = 'tmp/' . $pdfData['invoiceId'] . '.pdf';
        $pdf->render();
    }

    private function csvValidation($data)
    {
        $rules = [
            'sex' => '/^(m|f)$/', //Sex Pattern
            'title' => '/^(herr|frau|dr|prof|mr|mrs|ms)$/', //Title Pattern
            'firstname' => '/^[a-z \'-äöüÄÖÜß]+$/', //FirstName Pattern
            'lastname' => '/^[a-z \'-äöüÄÖÜß]+$/', //LastName Pattern
            'telephone' => '/^0\d{6,}$/', //Phone number pattern
            'dob' => '/^\d{4}\-\d{2}\-\d{2}$/', //Date of birth pattern
            'mail' => '/^[a-z0-9_\-\.]{2,}@[a-z0-9_\-\.]{2,}\.[a-z]{2,}$/', //Email pattern
            'orderdate' => '/^\d{4}\-\d{2}\-\d{2}$/', //Date of birth pattern
            'nationality' => '/^[a-z \'-äöüÄÖÜß]+$/',
            'ipaddress' => '',
            'corporatecard' => '', //Corporate Card Pattern
            'country' => '/^\d{3}$/', //Country Code Name
            'address' => '/^.{3,}$/', //Address pattern
            'zipcode' => '/^.{3,}$/', //Zipcode pattern
            'city' => '/^.{3,}$/', //City pattern
        ];

        $tips = [
            'sex' => 'Value should be m or f',
            'title' => 'Value should be herr, frau, dr, prof, mr, mrs, ms',
            'firstname' => 'Value should contain only letters',
            'lastname' => 'Value should contain only letters',
            'telephone' => 'Value should contain digits only and start with "0"',
            'dob' => 'Allowed value format is yyyy-mm-dd',
            'mail' => 'Value should be correct email',
            'orderdate' => 'Allowed value format is yyyy-mm-dd',
            'nationality' => 'Value should contain only letters',
            'ipaddress' => 'No validation',
            'corporatecard' => 'No validation',
            'country' => 'Value should contain only 3 digits',
            'address' => 'Value should contain minimum 3 symbols',
            'zipcode' => 'Value should contain minimum 3 symbols',
            'city' => 'Value should contain minimum 3 symbols',
        ];

        return Yii::$app->validation->csv($data, $rules, $tips);
    }

    private function normalize($word)
    {
        $chars = [
            'Š' => 'S',
            'š' => 's',
            'Đ' => 'Dj',
            'đ' => 'dj',
            'Ž' => 'Z',
            'ž' => 'z',
            'Č' => 'C',
            'č' => 'c',
            'Ć' => 'C',
            'ć' => 'c',
            'À' => 'A',
            'Á' => 'A',
            'Ã' => 'A',
            'Ä' => 'Ae',
            'Å' => 'A',
            'Æ' => 'A',
            'Ç' => 'C',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ì' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'Ñ' => 'N',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ö' => 'Oe',
            'Ø' => 'O',
            'Ù' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Ü' => 'Ue',
            'Ý' => 'Y',
            'Ÿ' => 'Y',
            'Þ' => 'B',
            'ß' => 'ss',
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ä' => 'ae',
            'å' => 'a',
            'æ' => 'a',
            'ç' => 'c',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ð' => 'o',
            'ñ' => 'n',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'oe',
            'ø' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ü' => 'ue',
            'ý' => 'y',
            'þ' => 'b',
            'ÿ' => 'y',
            'Ŕ' => 'R',
            'ŕ' => 'r',
        ];

        $word = mb_convert_encoding($word, 'utf-8');
        $word = trim($word);
        $word = strtr($word, $chars);
        return $word;
    }


    public function mandrill($from, $to, $subject, $html, $file = null)
    {
        $mandrill = new Mandrill('k7dQwst_tKPZC4e0osA1AA');

        $message = [
            'html' => $html,
            'subject' => $subject,
            'from_email' => $from,
            'to' => [
                ['email' => $to,],
            ],
        ];

        if ($file) {
            $attachment = file_get_contents('tmp/' . $file . '.pdf');
            $attachment_encoded = base64_encode($attachment);
            $message['attachments'] = [['type' => 'application/pdf', 'name' => $file . '.pdf', 'content' => $attachment_encoded]];
        }

        $async = false;
        $ip_pool = 'Main Pool';
        $send_at = '2000-01-01 00:00:00';
        $result = $mandrill->messages->send($message, $async, $ip_pool, $send_at);

        return $result;
    }

    public function actionUploadcards() {
        if (Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $file = new Parser;
            $file->file = UploadedFile::getInstance($file, 'file');
            $file->file_card = UploadedFile::getInstance($file, 'file_card');
            $data = $file->upload();
            if ($data) { // file is uploaded successfully
                try {
                    if (empty($data[0]['track3'])) {
                        $res = $this->updateStatuses($data);
                        return [
                            'code' => $res['code'],
                            'text' => $res['text'],
                        ];
                    }

                    $columns = ['uid','track3'];

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
                    $validationErrors = $this->csvValidationCards($data);
                    if (!empty($validationErrors)) {
                        return [
                            'code' => 400,
                            'text' => 'Validation ' . $validationErrors,
                        ];
                    }

                    $emptyRows = 0;
                    $cardsNumber = 0;
                    $new_records = [];

                    foreach ($data as $record) {
                        if (empty($record['track3'])) {
                            $emptyRows++;
                            continue;
                        }

                        $order = Orders::find()->where(['recid' => $record['uid']])->one();
                        if (empty($order->id)) {
                            $emptyRows++;
                            continue;
                        }

                        $order->status_id = 18;
                        $order->token = $record['track3'];
                        $order->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);

                        if (!$order->save()) {
                            return [
                                'code' => 200,
                                'text' => $order->getErrors(),
                            ];
                        }

                        $data = [];
                        $data['firstName'] = $order->customer->firstname;
                        $data['brandName'] = $order->invoice->product->name;
                        $data['token'] = $order->token;

                        $cardSending = CardSending::find()->where(['email' => $order->customer->email, 'brand_name' => $order->invoice->product->name])->one();
                        $cardSending->token = $order->token;
                        $cardSending->created_at = '1970-01-01';
                        $cardSending->save();
 
                        /*$this->mandrill(
                            'no-reply@cardcompact.uk',
                            $order->customer->email,
                            $data['firstName'] . ', deine ' . $data['brandName'] . ' Mastercard wurde produziert - your ' . $data['brandName'] . '  Mastercard was produced.',
                            $this->sendDownloadLetter($data)
                        );*/
                        $new_records[] = $order->getPrimaryKey();
                        $cardsNumber++;
                    }

                    \Yii::$app->session->setFlash('new_records', $new_records);
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
            return false;

        }
    }

    public function actionMdelete()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $deleted = \Yii::$app
                ->db
                ->createCommand()
                ->delete('card', ['in', 'card.id', $data['data']])
                ->execute();

            if ($deleted > 0) {
                return [
                    'code' => 200,
                    'text' => $deleted . ' order(s) deleted',
                ];
            } else {
                return [
                    'code' => 400,
                    'text' => 'No orders was deleted!'
                ];
            }
        }

        return false;
    }

    public function updateStatuses($data) {
        $res = [];
        $columns = ['pubtoken', 'activationdate',];

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

        $new_records = [];

        foreach ($data as $record){
            $order = Orders::find()->where(['token' => $record['pubtoken']])->one();
            $status = (isset($record['status'])) ? $record['status'] : 0;

            $order->token = $record['pubtoken'];
            $order->activation_date = $record['activationdate'];
            $order->status_id = $status;
            $order->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);

            $order->save();

            $new_records[] = $order->getPrimaryKey();
        }

        \Yii::$app->session->setFlash('new_records', $new_records);
        $res['code'] = 200;
        $res['text'] = count($data) . 'rows were updated successfully';

        return $res;
    }

    private function csvValidationCards($data) {

        $rules = [
            'uid' => '/^\d{10,}$/',
            'track3' => '/^\d{9}$/',
        ];

        $tips = [
            'uid' => 'Value should contain at least 10 digits',
            'track3' => 'Value should contain only 9 digits',
        ];

        return Yii::$app->validation->csv($data, $rules, $tips);
    }

    public function sendUploadLetter($data) {
        return
            '<p>Hallo ' . $data['firstName'] . ',<p>
            <p>Vielen Dank, dass Sie sich für eine ' . $data['brandName'] . ' Mastercard von Card Compact entschieden haben. Ihre Karte wird in den nächsten 48 Stunden produziert und sollte in den nächsten 7 - 10 Werktagen bei Ihnen eintreffen.</p>
            <p>Für Rückfragen erreichen Sie uns per Telefon unter +49 (0) 1807 667766 oder per Email unter support@cardcompact.cards.</p>
            <p>Mit freundlichen Grüßen<br>
            Card Compact Limited<br>
            www.cardcompact.com<br>
            www.cardcompact.cards (Kartenportal)<br>
            www.facebook.com/cardcompact<br>
            www.twitter.com/cardcompact</p>
            <p>-------------------------------------------------------------------------------------------------------</p>
            <p>Dear ' . $data['firstName'] . ',</p>
            <p>Thank you for choosing a ' . $data['brandName'] . ' Mastercard from Card Compact. Your card will be produced within 48 hours and you should receive it within 7 to 10 business days.</p>
            <p>If you have any queries, please call our customer support in Germany at +49 1807 667766 or email us at support@cardcompact.cards.</p>
            <p>Kind regards<br>
            Card Compact Limited<br>
            www.cardcompact.com<br>
            www.cardcompact.cards (card portal)<br>
            www.facebook.com/cardcompact<br>
            www.twitter.com/cardcompact<p>
            ';
    }

    public function sendDownloadLetter($data) {
        return
        '<p>Hallo ' . $data['firstName'] . ',</p>
        <p>Vielen Dank, dass Sie sich für eine ' . $data['brandName'] . ' Mastercard von Card Compact entschieden haben. Ihre Karte wurde produziert und wird nun an Sie verschickt. Sie sollte in den nächsten 7 Werktagen bei Ihnen eintreffen.</p>
        <p>Ihre 9stellige Kundennummer, die Sie auch links unten auf Ihrer neuen ' . $data['brandName'] . ' Mastercard finden lautet: ' . $data['token'] . '. Sie benötigen diese Kundennummer für alle Anfragen an Card Compact, wenn Sie sich im Kartenportal einloggen oder z. B. wenn Sie Ihre Karte aufladen möchten.</p>
        <p>Für Rückfragen erreichen Sie uns per Telefon unter +49 (0) 1807 667766 oder per Email unter support@cardcompact.cards.</p>
        <p>Mit freundlichen Grüßen<br>
        Card Compact Limited<br>
        www.cardcompact.com
        www.cardcompact.cards (Kartenportal)<br>
        www.facebook.com/cardcompact<br>
        www.twitter.com/cardcompact</p>
        <p>-------------------------------------------------------------------------------------------------------
        <p>Dear ' . $data['firstName'] . ',</p>
        <p>Thank you for choosing a ' . $data['brandName'] . ' Mastercard from Card Compact. Your card has already been produced and will be shipped now. You should receive it within the next 7 business days.</p>
        <p>Your 9digit token, which you will also find bottom left of your new ' . $data['brandName'] . ' Mastercard is the following: ' . $data['token'] . '. Please note, you need this number for all your queries to Card Compact, when you log on to the card portal or for example when you load your card.</p>
        <p>If you have any queries, please call our customer support in Germany at +49 1807 667766 or email us at support@cardcompact.cards.</p>
        <p>Kind regards<br>
        Card Compact Limited<br>
        www.cardcompact.com<br>
        www.cardcompact.cards (card portal)<br>
        www.facebook.com/cardcompact<br>
        www.twitter.com/cardcompact</p>';
    }

    public function saveAction($userId, $ip, $type, $product, $amount) {
        $action = new Action();

        $action->user_id = $userId;
        $action->ip = $ip;
        $action->type_id = $type;
        $action->product_id = $product;
        $action->amount = $amount;
        $action->created_at = gmdate('Y-m-d H:i:s', time() + 7200);

        $action->save();
    }
}
