<?php
namespace backend\controllers;

use backend\models\Product;
use yii\filters\AccessControl;
use Yii;
use yii\web\UploadedFile;
use backend\models\Parser;
use yii\web\Controller;
use backend\models\Company;
use backend\models\Customer;
use backend\models\Order;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class UploadController extends Controller
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
                        'roles' => ['admin'],
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

    public function actionIndex()
    {
        $uploader = new Parser();
        return $this->render('index', [
            'model' => $uploader,
        ]);
    }

    public function actionOrder() {
        $model = new Parser();

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            $data = $model->Upload();
            if ($data) {
                foreach ($data as $record){
                    /*!!!!!!!!!!!!!CUSTOMER!!!!!!!!!!!!!!!
                     * $customer = new Customer();

                    $customer->pubtoken = $record['token'];
                    $customer->email = $record['email'];

                    $customer->save();*/

                    /*!!!!!!!!!!!!!!!ORDER!!!!!!!!!!!!!!
                     $order = new Order();

                    $order->email = $record['mail'];

                    $order->save();*/
                    $company = Company::findOne(['name' => $record['partner_company'],]);
                    if(empty($company->id)) continue;

                    $product = new Product();

                    $product->price = str_replace(',', '.', $record['price']);
                    $product->name = $record['name'];
                    $product->crdproduct = $record['gps_crdproduct'];
                    $product->designref = $record['gps_designref'];
                    $product->currcode = $record['gps_currcode'];
                    $product->amtload = $record['gps_amtload'];
                    $product->imageid = $record['gps_imageid'];
                    $product->limitsgroup = $record['gps_limitsgroup'];
                    $product->permsgroup = $record['gps_permsgroup'];
                    $product->feesgroup = $record['gps_feesgroup'];
                    $product->carrierref = $record['gps_carrierref'];
                    $product->action = $record['gps_action'];
                    $product->company_id = $company->id;

                    $product->save();
                }
            }
        }

        return $this->redirect('index');
    }

    private function normalize($word){
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

        $word = trim($word);
        $word = strtr($word, $chars);
        return $word;
        $word = trim($word);
        $word = htmlspecialchars($word);

        return $word;
    }

    function generateInvoiceId($length = 8) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        if (Invoices::findOne(['INVOICE_ID' => $randomString])) $this->generateInvoiceId();

        return $randomString;
    }

    function createInvoicePdf($pdfData) {
        $pdf = Yii::$app->pdf;
        $pdf->content = $this->renderPartial('pdf', ['pdfData' => $pdfData]);
        $pdf->cssInline = 'button,hr,input{overflow:visible}audio,canvas,progress,video{display:inline-block}progress,sub,sup{vertical-align:baseline}[type=checkbox],[type=radio],legend{box-sizing:border-box;padding:0}html{line-height:1.15;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}article,aside,details,figcaption,figure,footer,header,main,menu,nav,section{display:block}h1{font-size:2em}figure{margin:1em 40px}hr{box-sizing:content-box;height:0}code,kbd,pre,samp{font-family:monospace,monospace;font-size:1em}a{background-color:transparent;-webkit-text-decoration-skip:objects}abbr[title]{border-bottom:none;text-decoration:underline;text-decoration:underline dotted}dfn{font-style:italic}mark{background-color:#ff0;color:#000}.table__bottom td,.table__top{background:#77BB41 }small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative}sub{bottom:-.25em}sup{top:-.5em}audio:not([controls]){display:none;height:0}img{border-style:none}svg:not(:root){overflow:hidden}button,input,optgroup,select,textarea{font-family:sans-serif;font-size:100%;line-height:1.15;margin:0}button,select{text-transform:none}[type=reset],[type=submit],button,html [type=button]{-webkit-appearance:button}[type=button]::-moz-focus-inner,[type=reset]::-moz-focus-inner,[type=submit]::-moz-focus-inner,button::-moz-focus-inner{border-style:none;padding:0}[type=button]:-moz-focusring,[type=reset]:-moz-focusring,[type=submit]:-moz-focusring,button:-moz-focusring{outline:ButtonText dotted 1px}fieldset{padding:.35em .75em .625em}legend{color:inherit;display:table;max-width:100%;white-space:normal}textarea{overflow:auto}[type=number]::-webkit-inner-spin-button,[type=number]::-webkit-outer-spin-button{height:auto}[type=search]{-webkit-appearance:textfield;outline-offset:-2px}[type=search]::-webkit-search-cancel-button,[type=search]::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}body,h1,h2,h3,h4,h5,h6,p{font-family:Arial,Helvetica;margin:0}summary{display:list-item}[hidden],template{display:none}strong{font-weight:900}.header{margin-top:20px;margin-bottom:65px;height:85px}.header__title{font-size:50px;font-weight:100}.header__logo{width:260px}.left-column{color:grey}.table__bottom td,.table__top td{color:#fff;text-align:center}.left-column__logo{width:150px;margin-bottom:120px}.left-column__content__block__content,.left-column__content__block__link,.left-column__content__block__title{font-size:12px}.table{margin-bottom:20px}.right-column__title{font-size:16px;font-weight:400}.right-column__content_date__text{font-weight:900}.right-column__content_customizer__title{font-size:16px;font-weight:400}table{border-spacing:0}.table td{border-bottom:2px solid #000;border-right:2px solid #000;padding:2px 17px;min-width:95px}tr td:last-child{border-right:none}.register{font-size:10px;margin-top:50px}';
        $pdf->filename = 'tmp/' . $pdfData['invoiceId'] . '.pdf';
        $pdf->render();
    }

    private function csvValidation($data) {
        $rules = [
            'sex' => '/^(m|f)$/', //Sex Pattern
            'title' => '/^(herr|frau|dr|prof|mr|mrs|ms)$/', //Title Pattern
            'firstname' => '/^[a-z \'-]+$/', //FirstName Pattern
            'lastname' => '/^[a-z \'-]+$/', //LastName Pattern
            'telephone' => '/^\d{7,}$/', //Phone number pattern
            'dob' => '/^\d{2}\.\d{2}\.\d{4}$/', //Date of birth pattern
            'mail' => '/^[a-z0-9_\-\.]{2,}@[a-z0-9_\-\.]{2,}\.[a-z]{2,}$/', //Email pattern
            'corporatecard' => '/^[01]$/', //Corporate Card Pattern
            'country' => '/^\d{3}$/', //Country Code Name
            //'cardname' => '/^[a-z \'-]+$/', //CardName Pattern
            'address' => '/^.{3,}$/', //Address pattern
            'zipcode' => '/^.{3,}$/', //Zipcode pattern
            'city' => '/^.{3,}$/', //City pattern
        ];

        $tips = [
            'sex' => 'Value should be m or f',
            'title' => 'Value should be herr, frau, dr, prof, mr, mrs, ms',
            'firstname' => 'Value should contain only letters',
            'lastname' => 'Value should contain only letters',
            'telephone' => 'Value should contain only digits',
            'dob' => 'Allowed value format is dd.mm.yyyy',
            'mail' => 'Value should be correct email',
            'corporatecard' => 'Value should be 0 or 1',
            'country' => 'Value should contain only 3 digits',
            //'cardname' => 'Value should contain only letters',
            'address' => 'Value should contain only letters',
            'zipcode' => 'Value should contain minimum 3 symbols',
            'city' => 'Value should contain only letters',
        ];

        return Yii::$app->validation->csv($data, $rules, $tips);
    }
}