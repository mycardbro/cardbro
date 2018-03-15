<?php
namespace console\controllers;

use yii\console\Controller;
use backend\models\CardSending;
use Mandrill;

class TestController extends Controller
{
    public function actionIndex()
    {
        return 'Ordinary';
    }

    public function actionPhp()
    {
        return 'Php only'; 
    }

    public function actionCli()
    {
        return 'Php client';
    }  

    public function actionSend()
    {
        $sendLetters = 0;
        $letters = CardSending::find()->where(['first_at' => null])->all();

        foreach ($letters as $letter) {
            $data = [];
            $data['firstName'] = $letter->first_name;
            $data['brandName'] = $letter->brand_name;
            $text = $this->sendUploadLetter($data);
            $email = $letter->email;

            $res = $this->mandrill(
                'no-reply@cardcompact.uk',
                $email,
                $letter->first_name . ', Ihre ' . $letter->brand_name . ' ist unterwegs - your ' . $letter->brand_name . ' is coming soon',
                $text
            );

            $letter->first_at = gmdate('Y-m-d H:i:s', time() + 7200);
            $letter->save();
            
            $sendLetters++;
        }

        $letters = CardSending::find()->where(['created_at' => '1970-01-01'])->all();

        foreach ($letters as $letter) {
            $data = [];
            $data['firstName'] = $letter->first_name;
            $data['brandName'] = $letter->brand_name;
            $data['token'] = $letter->token;
            $text = $this->sendDownloadLetter($data);
            $email = $letter->email;

            $res = $this->mandrill(
                'no-reply@cardcompact.uk',
                $email,
                $data['firstName'] . ', deine ' . $data['brandName'] . ' wurde produziert - your ' . $data['brandName'] . '  was produced.',
                $text
            );

            $letter->created_at = gmdate('Y-m-d H:i:s', time() + 7200);
            $letter->save();
            
            $sendLetters++;
        }

        return var_dump($sendLetters);  
    }  

public function sendUploadLetter($data) {
        return
            '<p>Hallo ' . $data['firstName'] . ',<p>
            <p>Vielen Dank, dass Sie sich für eine ' . $data['brandName'] . ' von Card Compact entschieden haben. Ihre Karte wird in den nächsten 48 Stunden produziert und sollte in den nächsten 7 - 10 Werktagen bei Ihnen eintreffen.</p>
            <p>Für Rückfragen erreichen Sie uns per Telefon unter +49 (0) 1807 667766 oder per Email unter support@cardcompact.cards.</p>
            <p>Mit freundlichen Grüßen<br>
            Card Compact Limited<br>
            www.cardcompact.com<br>
            www.cardcompact.cards (Kartenportal)<br>
            www.facebook.com/cardcompact<br>
            www.twitter.com/cardcompact</p>
            <p>-------------------------------------------------------------------------------------------------------</p>
            <p>Dear ' . $data['firstName'] . ',</p>
            <p>Thank you for choosing a ' . $data['brandName'] . ' from Card Compact. Your card will be produced within 48 hours and you should receive it within 7 to 10 business days.</p>
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
        <p>Vielen Dank, dass Sie sich für eine ' . $data['brandName'] . ' von Card Compact entschieden haben. Ihre Karte wurde produziert und wird nun an Sie verschickt. Sie sollte in den nächsten 7 Werktagen bei Ihnen eintreffen.</p>
        <p>Ihre 9stellige Kundennummer, die Sie auch links unten auf Ihrer neuen ' . $data['brandName'] . ' finden lautet: ' . $data['token'] . '. Sie benötigen diese Kundennummer für alle Anfragen an Card Compact, wenn Sie sich im Kartenportal einloggen oder z. B. wenn Sie Ihre Karte aufladen möchten.</p>
        <p>Für Rückfragen erreichen Sie uns per Telefon unter +49 (0) 1807 667766 oder per Email unter support@cardcompact.cards.</p>
        <p>Mit freundlichen Grüßen<br>
        Card Compact Limited<br>
        www.cardcompact.com
        www.cardcompact.cards (Kartenportal)<br>
        www.facebook.com/cardcompact<br>
        www.twitter.com/cardcompact</p>
        <p>-------------------------------------------------------------------------------------------------------
        <p>Dear ' . $data['firstName'] . ',</p>
        <p>Thank you for choosing a ' . $data['brandName'] . ' from Card Compact. Your card has already been produced and will be shipped now. You should receive it within the next 7 business days.</p>
        <p>Your 9digit token, which you will also find bottom left of your new ' . $data['brandName'] . ' is the following: ' . $data['token'] . '. Please note, you need this number for all your queries to Card Compact, when you log on to the card portal or for example when you load your card.</p>
        <p>If you have any queries, please call our customer support in Germany at +49 1807 667766 or email us at support@cardcompact.cards.</p>
        <p>Kind regards<br>
        Card Compact Limited<br>
        www.cardcompact.com<br>
        www.cardcompact.cards (card portal)<br>
        www.facebook.com/cardcompact<br>
        www.twitter.com/cardcompact</p>';
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
    }
}