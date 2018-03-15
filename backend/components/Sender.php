<?php
namespace backend\components;

use yii\base\Component;
use yii;
use Mandrill;
use backend\models\EmailLog;

class Sender extends Component
{
    public function mandrill($data) {
        $mandrill = new Mandrill('k7dQwst_tKPZC4e0osA1AA');
        $html = Yii::$app->view->render('@app/views/mails/' . $data['view'] . '.php');

        $message = [
            'html' => $html,
            'subject' => $data['subject'],
            'from_email' => $data['from'],
            'to' => [
                ['email' => $data['to'],],
            ]
        ];

        if (!empty($data['file'])) {
            $attachment = file_get_contents('tmp/' . $data['file'] . '.pdf');
            $attachment_encoded = base64_encode($attachment);
            $message['attachments'] = ['type' => 'application/pdf', 'name' => $data['file'] . '.pdf', 'content' => $attachment_encoded];
        }

        $async = false;
        $ip_pool = 'Main Pool';
        $send_at = '2000-01-01 00:00:00';
        $result = $mandrill->messages->send($message, $async, $ip_pool, $send_at);

        if ($result) {
            $emailLog = new EmailLog();

            $emailLog->type = 'Invoice';
            $emailLog->sender = $data['from'];
            $emailLog->recipient = $data['to'];

            $emailLog->save();
        }

        return $result;
    }

    public function mandrillNewUser($user, $password) {
        $mandrill = new Mandrill('k7dQwst_tKPZC4e0osA1AA');
        $html = '<p>Hello ' . $user->username . ',</p>';
        $html .= '<p>We have created a CardCompact account for you.<br>';
        $html .= 'Here is the link: https://cardbro.cardcompact.uk</p>';
        $html .= '<p>Please log on to CardBro by using the following login credentials:</p><br>';
        $html .= '<p>Email: ' . $user->email . '</p>';
        $html .= '<p>Password: ' . $password . '</p><br>';
        $html .= '<p>You can change your password in your account profile at any time. If you have any questions, please email us at support@cardcompact.co.uk.</p><br>';
        $html .= '<p>Kind regards</p><br>';
        $html .= '<p>Card Compact Limited</p>';
        $html .= '<p>29th Floor, One Canada Square, Canary Wharf | London, E14 5DY, UK, Telephone +44 (0) 207 7121488</p>';

        $message = [
            'html' => $html,
            'subject' => 'CardCompact Registration',
            'from_email' => 'no-reply@cardcompact.uk',
            'to' => [
                ['email' => $user->email,],
            ]
        ];

        $async = false;
        $ip_pool = 'Main Pool';
        $send_at = '2000-01-01 00:00:00';
        $result = $mandrill->messages->send($message, $async, $ip_pool, $send_at);

        if ($result) {
            $emailLog = new EmailLog();

            $emailLog->type = 'Registration';
            $emailLog->sender = 'no-reply@cardcompact.uk';
            $emailLog->recipient = $user->email;

            $emailLog->save();
        }

        return $result;
    }
}