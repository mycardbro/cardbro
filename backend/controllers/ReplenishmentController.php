<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 04.03.18
 * Time: 15:21
 */

namespace backend\controllers;

use yii\web\Controller;
use Yii;
use backend\models\Parser;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use backend\models\Replenishment;
use backend\models\ReplenishmentSearch;

class ReplenishmentController extends Controller
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
     * Lists all Company models.
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

        $uploader = new Parser();
        $searchModel = new ReplenishmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'uploader' => $uploader,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpload(){
        if (Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $file = new Parser;
            $file->file = UploadedFile::getInstance($file, 'file');
            $data = $file->upload();

            if ($data) { // file is uploaded successfully
                try {
                    $columns = ['date', 'token', 'amount'];

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
                    $newRecords = [];

                    foreach ($data as $record){
                        if (empty($record['token'])) {
                            $emptyRows++;
                            continue;
                        }

                        $model = new Replenishment();

                        $model->token = $record['token'];
                        $model->replenishment_at = $record['date'];
                        $model->amount = $record['amount'];

                        if (!$model->save()) {                           
                            return [
                                'code' => 400,
                                'text' => 'Error!<br>Cannot write data.'
                            ];
                        }

                        $newRecords[] = $model->getPrimaryKey();
                    }

                    \Yii::$app->session->setFlash('new_records', $newRecords);
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

    private function csvValidation($data)
    {
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
}