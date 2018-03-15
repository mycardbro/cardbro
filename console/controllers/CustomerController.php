<?php
/**
 * Created by PhpStorm.
 * User: phileo
 * Date: 11/11/17
 * Time: 8:20 PM
 */

namespace console\controllers;


use yii\console\Controller;
use backend\models\Orders;
use backend\models\CardSending;
use Mandrill;

class CustomerController extends Controller
{
public function actionSend()
    {
        $sendLetters = 0;
        $letters = CardSending::find()->where(['and', 'created_at <= (CURDATE() - INTERVAL 7 DAY)', 'second_at is null'])->all();

        foreach ($letters as $letter) {
            $data = [];
            $data['first_name'] = $letter->first_name;
            $data['brand_name'] = $letter->brand_name;
            $text = $this->sendThirdLetter($data);
            $email = $letter->email;

            $res = $this->mandrill(
                'no-reply@cardcompact.uk',
                $email,
                $data['first_name'] . ', so aktivieren Sie Ihre ' . $data['brand_name'] . ', how to activate your ' . $data['brand_name'],
                $this->sendThirdLetter($data)
            );

            $letter->second_at = gmdate('Y-m-d H:i:s', time() + 7200);
            $letter->save();
            
            $sendLetters++;
        }

        $letters = CardSending::find()->where(['and', 'created_at <= (CURDATE() - INTERVAL 14 DAY)', 'third_at is null'])->all();

        foreach ($letters as $letter) {
            $data = [];
            $data['first_name'] = $letter->first_name;
            $data['brand_name'] = $letter->brand_name;
            $data['token'] = $letter->token;
            $text = $this->sendFourthLetter($data);
            $email = $letter->email;

            $res = $this->mandrill(
                'no-reply@cardcompact.uk',
                $email,
                $data['first_name'] . ', Ihre ' . $data['brand_name'] . ' wurde aufgeladen, your ' . $data['brand_name'] . ' has been loaded',
                $this->sendFourthLetter($data)
            );

            $letter->third_at = gmdate('Y-m-d H:i:s', time() + 7200);
            $letter->save();
            
            $sendLetters++;
        }

        return var_dump($sendLetters);  
    }  

    protected function sendThirdLetter($data)
    {
        return '<p>Hallo ' . $data["first_name"] . ',</p>
        <p>Vielen Dank, dass Sie sich für eine ' . $data["brand_name"] . ' von Card Compact entschieden haben. Falls Sie ihre Karte noch nicht aktiviert haben, gehen Sie wie folgt vor:<br />
        Klicken Sie auf folgenden Link: https://www.cardcompact.cards/#/card-activation/step1<br />
        Geben Sie nun folgenden Daten ein:</p>
        <ol>
            <li>Ihre 9stellige Kundennummer, die Sie links unten auf der Karte finden,</li>
            <li>Ihre 3stellige Kartenprüfnummer, die sich hinten auf der Karte im Unterschriftenfeld befindet</li>
            <li>Ihren 6stelligen Sicherheitscode, welche aus den den letzten 6 Ziffern Ihrer Mobilfunknummer bestehen</li>
        </ol>
        Klicken Sie nun auf "Aktivieren", vergeben im Anschluss Ihr persönliches Kennwort und bestätigen Sie die Eingaben.<br />
        Geschafft! Ihre Karte sollte nun aktiviert sein. Starte, sie zu verwenden.</p>
        Sollten Sie weiterhin Hilfe benötigen, sehen Sie sich unser Aktivierungsvideo auf https://vimeo.com/245634092 an Sie erreichen Sie uns auch per Telefon unter +49 (0) 1807 667766 oder per Email unter support@cardcompact.cards.</p>

        <p>Mit freundlichen Grüßen<br />
        Card Compact Limited<br />
        www.cardcompact.com<br />
        www.cardcompact.cards (Kartenportal)<br />
        www.facebook.com/cardcompact<br />
        www.twitter.com/cardcompact</p>

        <p>-------------------------------------------------------------------------------------------------------</p>

        <p>Dear ' . $data["first_name"] . ',</p>
        <p>Thank you for choosing a ' . $data["brand_name"] . ' from Card Compact. If you have not activated your card yet, please follow the instructions:</p>
        <p>Click on the following link: https://www.cardcompact.cards/<br />
            Now please enter the following details:<br />
            <ol>
                <li>your 9digit token bottom left of your card</li>
                <li>the last 3digits of the number on the back of your card</li>
                <li>and the last 6 digits of your mobile number</li>
            </ol>
        Please click now on ACTIVATE, create your own secure password and confirm your entries.<br />
        Done! Your card should now be activated. Start to use it.</p>
        <p>If you need any further help, please click on the following link and watch our activation video at https://vimeo.com/245634147 or call our customer support at +49 1807 667766 or email us at support@cardcompact.cards.</p>
        <p>Kind regards<br />
        Card Compact Limited<br />
        www.cardcompact.com<br />
        www.cardcompact.cards (card portal)<br />
        www.facebook.com/cardcompact<br />
        www.twitter.com/cardcompact</p>';
    }

    protected function sendFourthLetter($data)
    {
        return '<p>Hallo ' . $data["first_name"] . ',</p>
            <p>Vielen Dank, dass Sie sich für eine ' . $data["brand_name"] . ' von Card Compact entschieden haben. Es ist nun Zeit, Ihre Karte aufzuladen.
            <ul>
                <li>Klicken Sie auf folgenden Link: https://www.cardcompact.cards</li>
                <li>Nun klicken Sie bitte auf AUSWAHL auf der virtuellen Karte und wählen KARTENAUFLADUNG.</li>
                <li>Es stehen Ihnen im Moment folgende Aufladekanäle zur Verfügung: Banküberweisung und Sofortüberweisung. Folgen Sie den weiteren Anweisungen. Sobald Sie Ihre Karte erfolgreichen aufgeladen haben, können Sie sie verwenden. Es ist einfach und sicher.</li>
            </ul>
            Sollten Sie weiterhin Hilfe benötigen, sehen Sie sich unser Aufladevideo auf https://vimeo.com/245634224 an Sie erreichen Sie uns auch per Telefon unter +49 (0) 1807 667766 oder per Email unter support@cardcompact.cards.</p>

            <p>Mit freundlichen Grüßen<br />
            Card Compact Limited<br />
            www.cardcompact.com<br />
            www.cardcompact.cards (Kartenportal)<br />
            www.facebook.com/cardcompact<br />
            www.twitter.com/cardcompact</p>
            <p>-------------------------------------------------------------------------------------------------------</p>
            <p>Dear ' . $data["first_name"] . ',</p>
            <p>Thank you for choosing a ' . $data["brand_name"] . ' from Card Compact. It is time to load your card.
            <ul>
            <li>Click on the following link: https://www.cardcompact.cards</li>
            <li>Now please click on ACTION on the virtual card and select CARD LOAD</li>
            <li>Please chose from the following load channels: Currently you can load your card by bank transfer or Sofortbanking. Please follow the instructions. Once you have successfully loaded your card, you can start using it. It is safe and easy.</li>
            </ul>
            <p>If you need any further help, please click on the following link and watch our load video at https://vimeo.com/245634281 or call our customer support at +49 1807 667766 or email us at support@cardcompact.cards.</p>
            <p>Kind regards<br />
            Card Compact Limited<br />
            www.cardcompact.com<br />
            www.cardcompact.cards (card portal)<br />
            www.facebook.com/cardcompact<br />
            www.twitter.com/cardcompact</p>';
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
}