<?php
namespace console\controllers;

use backend\models\Customers;
use backend\models\Cards;
use backend\models\Invoice;
use backend\models\Parser;
use backend\models\Product;
use backend\models\Products;
use backend\models\Orders;
use backend\models\AuthAssignment;
use yii;
use backend\models\User;
use backend\models\Company;
use yii\base\ErrorException;
use yii\console\Controller;
use backend\models\Invoices;
use backend\models\Subscriber;
use backend\models\SiteConfig;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;
use Mandrill;
use yii\helpers\ArrayHelper;
use backend\models\Payments;

class ReportController extends Controller
{
    public function actionTest()
    {
        $orders = Payments::find()
            ->select(['card.recid as re', 'reminder.token'])
            ->join('LEFT JOIN', 'card', 'reminder.token = card.token')
            ->where(['in', 'reminder.id', [41051, 41052]])
            ->andWhere(['not in', 'card.status_id', [4, 6]])
            ->asArray()
            ->all();

        $folder = Yii::$app->controller->id;
        return var_dump($folder);
        $name = $folder . '-list-' . date('Y-M-d-H-i-s', time());
        if (Parser::generateCSV($orders, $folder, $name)){
            return var_dump($name . '.csv');
        }
    }

    public function actionIndex() {
        $thirdLetterOrders = Orders::find()
            ->where(['and', 'pull_date >= (CURDATE() - INTERVAL 7 DAY)', 'pull_date <= (CURDATE() - INTERVAL 6 DAY)'])
            ->all();

        $fourthLetterOrders = Orders::find()
            ->where(['and', 'pull_date >= (CURDATE() - INTERVAL 7 DAY)', 'pull_date <= (CURDATE() - INTERVAL 6 DAY)'])
            ->all();

        return var_dump($thirdLetterOrders);
    }

    /*Create report for collectors*/
    public function actionCollectorReport() {
        /*Get result using model method*/
        $result = [
            ['a', 'b', 'c',],
            [1, 2, 3,],
        ];

        $fileName = 'debtorList' . date('Y-m-d');
        $this->createCSV($result, $fileName);

        //$subscribers = Subscriber::getSubscribers('collector');
        //$this->actionSendReport($fileName, $subscribers);

    }

    private function createCSV(array $data, $fileName) {
        //For .xslx http://www.yiiframework.com/extension/yii2-phpexcel/
        $file = fopen("tmp/" . $fileName . ".csv","w");

        foreach ($data as $row) {
            fputcsv($file, $row);
        }

        fclose($file);
    }

    public function actionSendReport($filename = '', array $subscribers = []) {
        $res = Yii::$app->mailer->compose()
            ->setFrom([SiteConfig::option('contact_email') => SiteConfig::option('site_name')])
            ->setTo('prudnikov@outsoft.com')
            ->setSubject('Test')
            ->setTextBody('Текст сообщения')
            ->send();

        var_dump($res);
    }

    public function mandrill($from, $to, $subject, $html) {
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

        return var_dump($result);
    }

    public function actionSend() {
        $this->mandrill('no-reply@cardcompact.uk', 'prudnikov0805@gmail.com', 'Test', "<h1>Test body</h1>");
    }

    public function actionErrors() {
        try {
            fopen('dfsf.dfs', 'r');
        } catch (ErrorException $e) {
            Yii::warning("Division by zero. Phileo");
        }

        echo "Finish";
    }
}