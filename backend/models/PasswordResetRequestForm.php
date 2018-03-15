<?php
namespace backend\models;

use Yii;
use yii\base\Model;
use backend\models\User;
use Mandrill;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\backend\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'There is no user with this email address.'
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if (!$user) {
            return false;
        }
        
        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                return false;
            }
        }

        $resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);

        $html = "
<div class='password-reset'>
    <p>Hello $user->username,</p>

    <p>Please click the link below to reset your password:</p>

    <p>$resetLink</p>

    <p>Regards,</p>
    <p>CardCompact</p>
</div>
";
        return $this->mandrill('no-reply@cardcompact.uk', $this->email, 'Reset password - CardCompact', $html);

        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                ['user' => $user]
            )
            ->setFrom('no-reply@outsoft.uk')
            ->setTo($this->email)
            ->setSubject('Reset password - CardCompact')
            ->send();
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
}
