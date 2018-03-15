<?php
namespace console\controllers;

use backend\models\Payments;
use Yii;
use yii\console\Controller;
use backend\models\Orders;
use backend\models\Invoices;
use console\models\OrderLog;
use backend\models\SiteConfig;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;
use Mandrill;

class StatusController extends Controller
{
    public function actionNew()
    {
        $this->sendReminders();
        $this->sendCardCancelation();
        $this->sendFirst();
    }

    public function sendReminders()
    {
        $reminders = Payments::find()
            ->where(['and', ['in', 'status_id', [9, 14, 24]], 'type_id = 0', 'updated_at >= (CURDATE() - INTERVAL 14 DAY)', 'updated_at <= (CURDATE() - INTERVAL 6 DAY)'])
            ->each();

        $letters = 0;
        foreach ($reminders as $reminder) {
            if ($letters > 1000) return true;
            if (empty($reminder->order) || empty($reminder->order->invoice->product->name)) continue;
            $data = [];
            $data['first_name'] = $reminder->order->customer->firstname;
            $data['brand_name'] = $reminder->order->invoice->product->name;
            $data['last_name'] = $reminder->order->customer->lastname;
            $data['annual_fee'] = $reminder->bill_amount;
            $data['fee_taken'] = $reminder->paid_amount;
            $data['fee'] = $reminder->bill_amount - $reminder->paid_amount;
            $data['creation_date'] = \Yii::$app->formatter->asDatetime($reminder->order->activation_date, "php:d-m-Y");
            $data['token'] = $reminder->order->token;

            $newStatus = 0;
            $subject = '';
            $body = '';

            if ($reminder->status_id == 9) {
                $subject = '2. Zahlungserinnerung -  Second Payment Reminder';
                $body = $this->sendSecondReminder($data);
                $reminder->second_at = gmdate('Y-m-d H:i:s', time() + 7200);
                $newStatus = 14;
            } elseif ($reminder->status_id == 14) {
                $subject = 'Übergabe an Inkasso droht - Transfer to Collection Agency Is Imminent';
                $body = $this->sendThirdReminder($data);
                $reminder->third_at = gmdate('Y-m-d H:i:s', time() + 7200);
                $newStatus = 15;
            } elseif ($reminder->status_id == 24) {
                $subject = '1. Zahlungserinnerung - First Payment Reminder';
                $body = $this->sendFirstReminder($data);
                $reminder->first_at = gmdate('Y-m-d H:i:s', time() + 7200);
                $newStatus = 9;
            }

            $reminder->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);
            $reminder->status_id = $newStatus;
            $reminder->save();

            $this->mandrill(
                'no-reply@cardcompact.uk',
                $reminder->order->customer->email,
                $subject,
                $body
            );

            $letters++;
        }
    }

    public function sendCardCancelation()
    {
        $reminders = Payments::find()
            ->where(['and', ['in', 'status_id', [19, 9, 14,]], 'type_id = 1', 'updated_at >= (CURDATE() - INTERVAL 14 DAY)', 'updated_at <= (CURDATE() - INTERVAL 6 DAY)'])
            ->each();

        foreach ($reminders as $reminder) {
            if (empty($reminder->order) || empty($reminder->order->invoice->product->name)) continue;
            $data = [];
            $data['first_name'] = $reminder->order->customer->firstname;
            $data['brand_name'] = $reminder->order->invoice->product->name;
            $data['last_name'] = $reminder->order->customer->lastname;
            $data['annual_fee'] = $reminder->bill_amount;
            $data['creation_date'] = \Yii::$app->formatter->asDatetime($reminder->order->activation_date, "php:d-m-Y");
            $data['token'] = $reminder->order->token;

            $newStatus = 0;
            if ($reminder->status_id == 19) {
                $subject = 'Erste Zahlungserinnerung - First Payment Reminder';
                $body = $this->firstCancellationReminder($data);
                $newStatus = 9;
            } elseif ($reminder->status_id == 9) {
                $subject = 'Letzte Zahlungserinnerung - Last Payment Reminder';
                $body = $this->secondCancellationReminder($data);
                $newStatus = 14;
            } elseif ($reminder->status_id == 14) {
                $subject = 'Übergabe an Inkasso droht - Transfer to collection agency is imminent';
                $body = $this->thirdCancellationReminder($data);
                $newStatus = 15;
            }

            $reminder->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);
            $reminder->status_id = $newStatus;
            $reminder->save();

            $this->mandrill(
                'no-reply@cardcompact.uk',
                $reminder->order->customer->email,
                $subject,
                $body
            );
        }
    }

    public function sendFirst()
    {
        $reminders = Payments::find()
            ->where(['and', ['in', 'status_id', [24]], 'type_id = 0',])
            ->each();

        $letters = 0;
        foreach ($reminders as $reminder) {
            if ($letters > 1000) return true;
            if (empty($reminder->order) || empty($reminder->order->invoice->product->name)) continue;
            $data = [];
            $data['first_name'] = $reminder->order->customer->firstname;
            $data['brand_name'] = $reminder->order->invoice->product->name;
            $data['last_name'] = $reminder->order->customer->lastname;
            $data['annual_fee'] = $reminder->bill_amount;
            $data['fee_taken'] = $reminder->paid_amount;
            $data['fee'] = $reminder->bill_amount - $reminder->paid_amount;
            $data['creation_date'] = \Yii::$app->formatter->asDatetime($reminder->order->activation_date, "php:d-m-Y");
            $data['token'] = $reminder->order->token;

            $newStatus = 0;
            $subject = '';
            $body = '';

            if ($reminder->status_id == 9) {
                $subject = '2. Zahlungserinnerung -  Second Payment Reminder';
                $body = $this->sendSecondReminder($data);
                $reminder->second_at = gmdate('Y-m-d H:i:s', time() + 7200);
                $newStatus = 14;
            } elseif ($reminder->status_id == 14) {
                $subject = 'Übergabe an Inkasso droht - Transfer to Collection Agency Is Imminent';
                $body = $this->sendThirdReminder($data);
                $reminder->third_at = gmdate('Y-m-d H:i:s', time() + 7200);
                $newStatus = 15;
            } elseif ($reminder->status_id == 24) {
                $subject = '1. Zahlungserinnerung - First Payment Reminder';
                $body = $this->sendFirstReminder($data);
                $reminder->first_at = gmdate('Y-m-d H:i:s', time() + 7200);
                $newStatus = 9;
            }

            $reminder->updated_at = gmdate('Y-m-d H:i:s', time() + 7200);
            $reminder->status_id = $newStatus;
            $reminder->save();

            $this->mandrill(
                'no-reply@cardcompact.uk',
                $reminder->order->customer->email,
                $subject,
                $body
            );

            $letters++;
        }
    }

    public function log($orders, $from, $to) {
        $invoices = [];

        foreach ($orders as $order) {
            $invoices[] = $order->INVOICE_ID;
            $log = new OrderLog;

            $log->ORDER_ID      = $order->ID;
            $log->FROM          = $from;
            $log->TO            = $to;
            $log->ACTION_DATE   = date('Y-m-d');

            $log->save();
        }

        $this->createReminder(array_unique($invoices), $from);
    }

    public function sendEmail($email, $letter, $subject) {
        Yii::$app->mailer->compose()
            ->setFrom([SiteConfig::option('contact_email') => SiteConfig::option('site_name')])
            ->setTo($email)
            ->setSubject($subject)
            ->setHtmlBody($letter)
            ->send();
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

    public function sendFirstReminder($data)
    {
        return '
	                <p style=\'color: #000;\'>Hallo ' . $data['first_name'] . ',</p>
                    <p style=\'color: #000;\'>Wir möchten Sie höflich daran erinnern, dass die Jahresgebühr für Ihre ' . $data['brand_name'] . ' von €' . money_format('%.2n', $data['annual_fee']) . ' bereits fällig war.</p>
                    <p style=\'color: #000;\'>Ihre Karte wurde am ' . $data['creation_date'] . ' aktiviert bzw. verschickt. Von Ihrem Kartenkonto konnten wir folgenden Betrag abbuchen: €' . money_format('%.2n', $data['fee_taken']) . '</p>
                    <p style=\'color: #000;\'>Bitte bringen Sie daher in den nächsten 7 Tagen den offenen Betrag in Höhe von €' . money_format('%.2n', $data['fee']) . '
                    zur Einzahlung. Nach Verstreichen dieser Frist müssen wir diese Forderung leider an unser
                    Inkassobüro übergeben. Dies führt eventuell zu einem negativen Schufa- oder KSV-Eintrag.</p>
                    <p style=\'color: #000;\'>Vermeiden Sie Nachteile und überweisen Sie jetzt!</p>
                    <p style=\'color: #000;\'>Konto-Empfänger: Card Compact Limited<br>
                    IBAN: DE43 7507 0024 0516 3498 00<br>
                    BIC/SWIFT: DEUTDEDB750<br>
                    Verwendungszweck: Jahresgebühr ' . $data['first_name'] . ' ' . $data['last_name'] . ', ' . $data['token'] . '<br>
                    Betrag: €' . money_format('%.2n', $data['fee']) . '</p>
                    <p style=\'color: #000;\'>Sollten Sie die Zahlung der Jahresgebühr zwischenzeitlich bereits durchgeführt haben, betrachten Sie diese E-Mail als gegenstandslos.</p>
                    <p style=\'color: #000;\'>Für Rückfragen stehen wir Ihnen gerne auch telefonisch zur Verfügung unter +49 1807 667766 oder senden Sie uns eine Email an support@cardcompact.co.uk.</p>
                    <p style=\'color: #000;\'>Mit freundlichen Grüßen,</p>
                    <p style=\'color: #000;\'>Kundendienst</p>
                    <p style=\'color: #000;\'>Card Compact Ltd.<br>29th Floor, One Canada Square, Canary Wharf, London E14 5DY, UK, VAT Reg. No.: GB119702813</p>
                    <br>
                    <br>
                    <p style=\'color: #000;\'>Dear ' . $data['first_name'] . ',</p>
                    <p style=\'color: #000;\'>We would like to point out that the annual fee for your ' . $data['brand_name'] . ' in the amount of €' . money_format('%.2n', $data['annual_fee']) . ' has already been overdue.</p>
                    <p style=\'color: #000;\'>Your prepaid card was activated/shipped on ' . $data['creation_date'] . '.<br>
                    We could charge your card account in the amount of €' . money_format('%.2n', $data['fee_taken']) . '</p>
                    <p style=\'color: #000;\'>Please pay the outstanding amount of €' . money_format('%.2n', $data['fee']) . ' within the next seven days. After
                    the end of the time allowed for payment we will transfer the claim to a debt collecting agency for collection.
                    This may cause additional costs and a negative entry in your credit report.</p>
                    <p style=\'color: #000;\'>Avoid these disadvantages and pay now:</p>
                    <p style=\'color: #000;\'>Payee: Card Compact Limited<br>
                    IBAN: DE43 7507 0024 0516 3498 00<br>
                    BIC/SWIFT: DEUTDEDB750<br>
                    Reference code: annual fee ' . $data['first_name'] . ' ' . $data['last_name'] . ', ' . $data['token'] . '<br>
                    Amount: €' . money_format('%.2n', $data['fee']) . '</p>
                    <p style=\'color: #000;\'>If you have any queries, please call our customer support in Germany at +49 1807 667766 or email us at support@cardcompact.co.uk.</p>
                    <p style=\'color: #000;\'>Kind regards</p>
                    <p style=\'color: #000;\'>Card Compact Ltd.<br>
                    29th Floor, One Canada Square, Canary Wharf, London E14 5DY, UK, VAT Reg. No.: GB119702813</p>';
    }

    public function sendSecondReminder($data)
    {
        /*
         * $data['first_name']
         * $data['last_name']
         * $data['brand_name']
         * $data['annual_fee']
         * $data['creation_date']
         * $data['token']
         */

        return '
            <p>Hallo ' . $data['first_name'] . '</p>
            <p>Wir möchten Sie nochmals höflich daran erinnern, dass die Jahresgebühr für Ihre ' . $data['brand_name'] . ' in Höhe von €' . $data['annual_fee'] . ' bereits fällig war.</p>
            <p>Ihre Karte wurde am  ' . $data['creation_date'] . ' aktiviert bzw. verschickt.<br>
            Von Ihrem Kartenkonto konnten wir folgenden Betrag abbuchen: €' . $data['annual_fee'] . '</p>
            <p>Bitte bringen Sie daher den offenen Betrag in Höhe von €' . $data['annual_fee'] . ' in spätestens drei Tagen zur Einzahlung. Nach Verstreichen dieser Frist müssen wir diese Forderung leider an unser Inkassobüro übergeben. Dies führt zu weiteren Kosten und eventuell zu einem negativen Schufa- oder KSV-Eintrag.</p>
            <p>Vermeiden Sie Nachteile und überweisen Sie jetzt!</p>
            <p>Konto-Empfänger: Card Compact Limited<br>
            IBAN: DE43 7507 0024 0516 3498 00<br>
            BIC/SWIFT: DEUTDEDB750<br>
            Verwendungszweck: Jahresgebühr ' . $data['first_name'] . ' ' . $data['last_name'] . ' - ' . $data['token'] . '<br>
            Betrag: €' . $data['annual_fee'] . '</p>
            <p>Sollten Sie die Zahlung der Jahresgebühr zwischenzeitlich bereits durchgeführt haben, betrachten Sie diese E-Mail als gegenstandslos.</p>
            <p>Für Rückfragen stehen wir Ihnen gerne auch telefonisch zur Verfügung unter +49 180 766 7766 oder per Email unter support@cardcompact.co.uk.</p>
            <p>Mit freundlichen Grüßen,</p>
            <p>Kundendienst</p>
            <p>Card Compact Ltd.<br>
            29th Floor, One Canada Square, Canary Wharf, London E14 5DY, UK, VAT Reg. No.: GB119702813</p>


            <p>Dear ' . $data['first_name'] . ',</p>
            <p>We would like to point out that the annual fee for your ' . $data['brand_name'] . ' in the amount of €' . $data['annual_fee'] . ' has already been overdue.</p>
            <p>Your prepaid card was activated/shipped on ' . $data['creation_date'] . '.<br>
            We could charge your card account in the amount of €' . $data['annual_fee'] . '</p>
            <p>Please pay the outstanding amount of €' . $data['annual_fee'] . ' within the next three days. After the end of the time allowed for payment we will transfer the claim to a debt collecting agency for collection. This may cause additional costs and a negative entry in your credit report.</p>
            <p>Avoid these disadvantages and pay now:</p>
            <p>Payee: Card Compact Limited<br>
            IBAN: DE43 7507 0024 0516 3498 00<br>
            BIC/SWIFT: DEUTDEDB750<br>
            Reference code: annual fee ' . $data['first_name'] . ' ' . $data['last_name'] . ' - ' . $data['token'] . '<br>
            Amount: €' . $data['annual_fee'] . '</p>
            <p>If you have already paid the outstanding amount, please consider this matter as closed. If you have any queries, please call our customer support  at +49 1807 667766 or email us at support@cardcompact.co.uk.</p>
            <p>Kind regards</p>
            <p>Card Compact Ltd.<br>
            29th Floor, One Canada Square, Canary Wharf, London E14 5DY, UK, VAT Reg. No.: GB119702813</p>';
    }

    public function sendThirdReminder($data)
    {
        /*
         * $data['first_name']
         * $data['last_name']
         * $data['brand_name']
         * $data['annual_fee']
         * $data['creation_date']
         * $data['token']
         */

        return '
            <p>Hallo ' . $data['first_name'] . ',</p>
            <p>Das ist eine allerletzte Zahlungserinnerung bezüglich Ihrer ' . $data['brand_name'] . '. In Kürze werden wir Ihre Zahlungsrückstände an ein Inkassobüro übertragen. Zahlen Sie jetzt, um Nachteile, wie höhere Kosten, einen neg. Schufa-Eintrag, Pfändungen etc. zu vermeiden.</p>
            <p>Ihre Karte wurde am ' . $data['creation_date'] . ' aktiviert bzw. verschickt.</p>
            <p>Bitte bringen Sie daher den offenen Betrag in Höhe von €' . $data['annual_fee'] . ' in spätestens drei Tagen zur Einzahlung.<br>
            Vermeiden Sie Nachteile und überweisen Sie jetzt!</p>
            <p>Konto-Empfänger: Card Compact Limited<br>
            IBAN: DE43 7507 0024 0516 3498 00<br>
            BIC/SWIFT: DEUTDEDB750<br>
            Verwendungszweck: Jahresgebühr ' . $data['first_name'] . ' ' . $data['last_name'] . ' - ' . $data['token'] . '<br>
            Betrag: €' . $data['annual_fee'] . '</p>
            <p>Sollten Sie die Zahlung der Jahresgebühr zwischenzeitlich bereits durchgeführt haben, betrachten Sie diese E-Mail als gegenstandslos.</p>
            <p>Für Rückfragen stehen wir Ihnen gerne auch telefonisch zur Verfügung unter +49 180 766 7766 oder per Email unter support@cardcompact.co.uk.</p>
            <p>Mit freundlichen Grüßen,</p>
            <p>Card Compact<br>
            Kundendienst</p>
            <p>Card Compact Ltd.<br>
            29th Floor, One Canada Square, Canary Wharf, London E14 5DY, UK, VAT Reg. No.: GB119702813</p>
            <p>-----------------------------------------------------------------------------------------------------</p>
            <p>Dear ' . $data['first_name'] . ',</p>
            <p>This is a final reminder regarding your ' . $data['brand_name'] . '. In event of non-payment we will soon hand over your outstanding fees to a debt collection agency. This could cause further costs and a negative entry in your credit report. Pay now to avoid disadvantages.</p>
            <p>Your prepaid card was activated/shipped on ' . $data['creation_date'] . '.</p>
            <p>Please pay the outstanding amount of €' . $data['annual_fee'] . ' within the next three days.<br>
            Avoid these disadvantages and pay now:</p>
            <p>Payee: Card Compact Limited<br>
            IBAN: DE43 7507 0024 0516 3498 00<br>
            BIC/SWIFT: DEUTDEDB750<br>
            Reference code: annual fee ' . $data['first_name'] . ' ' . $data['last_name'] . ' - ' . $data['token'] . '<br>
            Amount: €' . $data['annual_fee'] . '</p>
            <p>If you have already paid the outstanding amount, please consider this matter as closed. If you have any queries, please call our customer support at +49 1807 667766 or email us at support@cardcompact.co.uk.</p>
            <p>Kind regards</p>
            <p>Card Compact<br>
            Customer Care</p>
            <p>Card Compact Ltd.<br>
            29th Floor, One Canada Square, Canary Wharf, London E14 5DY, UK, VAT Reg. No.: GB119702813</p>
        ';
    }

    public function firstCancellationReminder($data)
    {
        return '
            <p>Hallo ' . $data['first_name'] . '</p>
            <p>Wir haben Ihren Kündigungswunsch erhalten und möchten Sie höflich daran erinnern, dass aus Ihrem Vertragsverhältnis für Ihre ' . $data['brand_name'] . ' Karte noch ein Betrag in Höhe von €' . $data['annual_fee'] . ' offen ist..</p>
            <p>Bitte bringen Sie diesen Betrag in spätestens fünf Tagen zur Einzahlung. Nach Verstreichen dieser Frist müssen wir diese Forderung leider an unser Inkassobüro übergeben. Dies führt zu weiteren Kosten und eventuell zu einem negativen Schufa- oder KSV-Eintrag.</p>
            <p>Vermeiden Sie Nachteile und überweisen Sie jetzt!</p>
            <p>Konto-Empfänger: Card Compact/Prepaid24<br>
            IBAN: DE43 7507 0024 0516 3498 00<br>
            BIC: DEUTDEDB750<br>
            Verwendungszweck: Offene Gebühren ' . $data['first_name'] . ' ' . $data['last_name'] . ' - ' . $data['token'] . '</p>
            Betrag: €' . $data['annual_fee'] . '</p>
            <p>Sollten Sie die Zahlung der Jahresgebühr zwischenzeitlich bereits durchgeführt haben, betrachten Sie diese E-Mail als gegenstandslos.</p>
            <p>Für Rückfragen stehen wir Ihnen gerne auch telefonisch zur Verfügung unter +49 180 766 7766 oder per Email unter support@cardcompact.co.uk.</p>
            <p>Mit freundlichen Grüßen,</p>
            <p>Card Compact<br>
            Kundendienst</p>
            <p>------------------------------------------------------------------------------------------------------</p>
            <p>Dear ' . $data['first_name'] . ',</p>
            <p>We have received you cancellation request and would like to point out that re your ' . $data['brand_name'] . ' Card there is still an outstanding amount of €' . $data['annual_fee'] . '.</p>
            <p>Please pay this amount within the next five days. After the end of the time allowed for payment we will transfer the claim to a debt collecting agency for collection. This may cause additional costs and a negative entry in your credit report.</p>
            <p>Avoid these disadvantages and pay now:</p>
            <p>Payee: Card Compact/Prepaid24<br>
            IBAN: DE43 7507 0024 0516 3498 00<br>
            BIC/SWIFT: DEUTDEDB750<br>
            Reference code: annual fee ' . $data['first_name'] . ' ' . $data['last_name'] . ' - ' . $data['token'] . '<br>
            Amount: €' . $data['annual_fee'] . '</p>
            <p>If you have already paid the outstanding amount, please consider this matter as closed. If you have any queries, please call our customer support  at +49 1807 667766 or email us at support@cardcompact.co.uk.</p>
            <p>Kind regards</p>
            <p>Card Compact<br>
            Customer Care</p>
        ';
    }

    public function secondCancellationReminder($data)
    {
        return '
            <p>Hallo ' . $data['first_name'] . '</p>
            <p>Wir haben Ihren Kündigungswunsch erhalten und möchten Sie höflich daran erinnern, dass aus Ihrem Vertragsverhältnis für Ihre ' . $data['brand_name'] . ' Karte noch ein Betrag in Höhe von €' . $data['annual_fee'] . ' offen ist..</p>
            <p>Bitte bringen Sie diesen Betrag in spätestens fünf Tagen zur Einzahlung. Nach Verstreichen dieser Frist müssen wir diese Forderung leider an unser Inkassobüro übergeben. Dies führt zu weiteren Kosten und eventuell zu einem negativen Schufa- oder KSV-Eintrag.</p>
            <p>Vermeiden Sie Nachteile und überweisen Sie jetzt!</p>
            <p>Konto-Empfänger: Card Compact/Prepaid24<br>
            IBAN: DE43 7507 0024 0516 3498 00<br>
            BIC: DEUTDEDB750<br>
            Verwendungszweck: Offene Gebühren ' . $data['first_name'] . ' ' . $data['last_name'] . ' - ' . $data['token'] . '</p>
            Betrag: €' . $data['annual_fee'] . '</p>
            <p>Sollten Sie die Zahlung der Jahresgebühr zwischenzeitlich bereits durchgeführt haben, betrachten Sie diese E-Mail als gegenstandslos.</p>
            <p>Für Rückfragen stehen wir Ihnen gerne auch telefonisch zur Verfügung unter +49 180 766 7766 oder per Email unter support@cardcompact.co.uk.</p>
            <p>Mit freundlichen Grüßen,</p>
            <p>Card Compact<br>
            Kundendienst</p>
            <p>------------------------------------------------------------------------------------------------------</p>
            <p>Dear ' . $data['first_name'] . ',</p>
            <p>We have received you cancellation request and would like to point out that re your ' . $data['brand_name'] . ' Card there is still an outstanding amount of €' . $data['annual_fee'] . '.</p>
            <p>Please pay this amount within the next five days. After the end of the time allowed for payment we will transfer the claim to a debt collecting agency for collection. This may cause additional costs and a negative entry in your credit report.</p>
            <p>Avoid these disadvantages and pay now:</p>
            <p>Payee: Card Compact/Prepaid24<br>
            IBAN: DE43 7507 0024 0516 3498 00<br>
            BIC/SWIFT: DEUTDEDB750<br>
            Reference code: annual fee ' . $data['first_name'] . ' ' . $data['last_name'] . ' - ' . $data['token'] . '<br>
            Amount: €' . $data['annual_fee'] . '</p>
            <p>If you have already paid the outstanding amount, please consider this matter as closed. If you have any queries, please call our customer support  at +49 1807 667766 or email us at support@cardcompact.co.uk.</p>
            <p>Kind regards</p>
            <p>Card Compact<br>
            Customer Care</p>
        ';
    }

    public function thirdCancellationReminder($data)
    {
        return '
        <p>Hallo ' . $data['first_name'] . ',</p>
        <p>In Kürze werden wir Ihre Zahlungsrückstände an ein Inkassobüro übertragen. Zahlen Sie jetzt um Nachteile, wie höhere Kosten, einen neg. Schufa-Eintrag, Pfändungen etc. zu vermeiden.</p>
        <p>Aus Ihrem Vertragsverhältnis für Ihre ' . $data['brand_name'] . ' ist noch ein Betrag in Höhe von €' . $data['annual_fee'] . ' offen..</p>
        <p>Bitte bringen Sie daher den offenen Betrag in Höhe von €' . $data['annual_fee'] . ' in spätestens drei Tagen zur Einzahlung. Nach Verstreichen dieser Frist müssen wir diese Forderung leider an unser Inkassobüro übergeben. Dies führt zu weiteren Kosten und eventuell zu einem negativen Schufa- oder KSV-Eintrag.</p>
        <p>Vermeiden Sie Nachteile und überweisen Sie jetzt!</p>
        <p>Konto-Empfänger: Card Compact/Prepaid24<br>
        IBAN: DE43 7507 0024 0516 3498 00<br>
        BIC: DEUTDEDB750<br>
        Betrag: €' . $data['annual_fee'] . '</p>
        Verwendungszweck: Offene Gebühren ' . $data['first_name'] . ' ' . $data['last_name'] . ' - ' . $data['token'] . '</p>
        <p>Sollten Sie die Zahlung der Jahresgebühr zwischenzeitlich bereits durchgeführt haben, betrachten Sie diese E-Mail als gegenstandslos.</p>
        <p>Für Rückfragen stehen wir Ihnen gerne auch telefonisch zur Verfügung unter +49 180 766 7766 oder per Email unter support@cardcompact.co.uk.</p>
        <p>Mit freundlichen Grüßen,</p>
        <p>Card Compact<br>
        Kundendienst</p>
        <p>-----------------------------------------------------------------------------------------------------------------</p>
        <p>Dear ' . $data['first_name'] . ',</p>
        <p>This is a final reminder regarding your ' . $data['brand_name'] . '. We will soon hand over your outstanding fees to a debt collection agency. This could cause further costs and a negative entry in your credit report.</p>
        <p>The overdue amount is €' . $data['annual_fee'] . '. Please pay this amount within the next three days.</p>
        <p>Avoid these disadvantages and pay now:</p>
        <p>Payee: Card Compact/Prepaid24<br>
        IBAN: DE43 7507 0024 0516 3498 00<br>
        BIC/SWIFT: DEUTDEDB750<br>
        Reference code: annual fee ' . $data['first_name'] . ' ' . $data['last_name'] . ' - ' . $data['token'] . '<br>
        Amount: €' . $data['annual_fee'] . '</p>
        <p>If you have already paid the outstanding amount, please consider this matter as closed. If you have any queries, please call our customer support  at +49 1807 667766 or email us at support@cardcompact.co.uk.</p>
        <p>Kind regards</p>
        <p>Card Compact<br>
        Customer Care</p>';
    }
}