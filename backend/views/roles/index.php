<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\RolesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Roles';
$this->params['breadcrumbs'][] = $this->title;
?>
<!--<div class="content-wrapper">
    <div class="container-fluid content">
        <?php foreach ($roles as $role) {
            echo "<p>" . $role->name . "</p>";
        } ?>
    </div>
</div>-->
<style>
    body {
        font-family: Arial;
        font-size: 14px;
    }
    .table {
        min-width: 768px;
        width: 100%;
        border-collapse: collapse;
    }
    .first_column {
        width: 30%;
        text-align: left;
    }
    .table__column {
        border: 1px solid #bdccb9;
        text-align: center;
        height: 20px;
    }
    .column_text_header {
        font-weight: 600;
    }
    .column_text_gray {
        background: #efefef;
    }
    .column_text_green {
        background: #d9ead4;
    }
    .column_text_red {
        background: #f3cccd;
    }
</style>
<table class="table">
    <tbody>
    <tr>
        <td class="table__column first_column"> </td>
        <td class="table__column column_text_header">Admin</td>
        <td class="table__column column_text_header">Manager</td>
        <td class="table__column column_text_header">Sales Partner</td>
        <td class="table__column column_text_header">Customer Support</td>
    </tr>
    <tr>
        <td class="table__column first_column column_text_header column_text_gray">Settings (Tab):</td>
        <td class="table__column column_text_gray">x</td>
        <td class="table__column column_text_gray">o</td>
        <td class="table__column column_text_gray">o</td>
        <td class="table__column column_text_gray">o</td>
    </tr>
    <tr>
        <td class="table__column first_column">Users</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_red">o</td>
        <td class="table__column column_text_red">o</td>
        <td class="table__column column_text_red">o</td>
    </tr>
    <tr>
        <td class="table__column first_column">Create User</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_red">o</td>
        <td class="table__column column_text_red">o</td>
        <td class="table__column column_text_red">o</td>
    </tr>
    <tr>
        <td class="table__column"></td>
        <td class="table__column"></td>
        <td class="table__column"></td>
        <td class="table__column"></td>
        <td class="table__column"></td>
    </tr>
    <tr>
        <td class="table__column first_column column_text_header column_text_gray">Customers (Tab):</td>
        <td class="table__column column_text_gray">x</td>
        <td class="table__column column_text_gray">x</td>
        <td class="table__column column_text_gray">o</td>
        <td class="table__column column_text_gray">x</td>
    </tr>
    <tr>
        <td class="table__column first_column">Create Customer</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_red">o</td>
        <td class="table__column column_text_green">x</td>
    </tr>
    <tr>
        <td class="table__column first_column">Update (Customers)</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_red">o</td>
        <td class="table__column column_text_green">x</td>
    </tr>
    <tr>
        <td class="table__column first_column">Delete (Customers)</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_red">o</td>
        <td class="table__column column_text_red">o</td>
    </tr>
    <tr>
        <td class="table__column"></td>
        <td class="table__column"></td>
        <td class="table__column"></td>
        <td class="table__column"></td>
        <td class="table__column"></td>
    </tr>
    <tr>
        <td class="table__column first_column column_text_header column_text_gray">Cards (Tab):</td>
        <td class="table__column column_text_gray">x</td>
        <td class="table__column column_text_gray">x</td>
        <td class="table__column column_text_gray">o</td>
        <td class="table__column column_text_gray">x</td>
    </tr>
    <tr>
        <td class="table__column first_column">Create Card</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_red">o</td>
        <td class="table__column column_text_green">x</td>
    </tr>
    <tr>
        <td class="table__column first_column">Update (Cards)</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_red">o</td>
        <td class="table__column column_text_green">x</td>
    </tr>
    <tr>
        <td class="table__column first_column">Delete (Orders)</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_red">o</td>
        <td class="table__column column_text_red">o</td>
    </tr>
    <tr>
        <td class="table__column"></td>
        <td class="table__column"></td>
        <td class="table__column"></td>
        <td class="table__column"></td>
        <td class="table__column"></td>
    </tr>
    <tr>
        <td class="table__column first_column column_text_header column_text_gray">Orders (Tab):</td>
        <td class="table__column column_text_gray">x</td>
        <td class="table__column column_text_gray">x</td>
        <td class="table__column column_text_gray">x</td>
        <td class="table__column column_text_gray">x</td>
    </tr>
    <tr>
        <td class="table__column first_column">Upload orders</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_red">o</td>
    </tr>
    <tr>
        <td class="table__column first_column">Pull orders for production</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_red">o</td>
        <td class="table__column column_text_red">o</td>
    </tr>
    <tr>
        <td class="table__column first_column">Order replacement</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
    </tr>
    <tr>
        <td class="table__column first_column">Pull orders for production</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_red">o</td>
        <td class="table__column column_text_red">o</td>
    </tr>
    <tr>
        <td class="table__column first_column">Pull orders for production</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_red">o</td>
        <td class="table__column column_text_red">o</td>
    </tr>
    <tr>
        <td class="table__column"></td>
        <td class="table__column"></td>
        <td class="table__column"></td>
        <td class="table__column"></td>
        <td class="table__column"></td>
    </tr>
    <tr>
        <td class="table__column first_column column_text_header column_text_gray">Products (Tab):</td>
        <td class="table__column column_text_gray">x</td>
        <td class="table__column column_text_gray">x</td>
        <td class="table__column column_text_gray">o</td>
        <td class="table__column column_text_gray">o</td>
    </tr>
    <tr>
        <td class="table__column first_column">Create Product</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_red">o</td>
        <td class="table__column column_text_red">o</td>
    </tr>
    <tr>
        <td class="table__column first_column">Update (Products)</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_red">o</td>
        <td class="table__column column_text_red">o</td>
    </tr>
    <tr>
        <td class="table__column first_column">Delete (Products)</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_red">o</td>
        <td class="table__column column_text_red">o</td>
    </tr>
    <tr>
        <td class="table__column"></td>
        <td class="table__column"></td>
        <td class="table__column"></td>
        <td class="table__column"></td>
        <td class="table__column"></td>
    </tr>
    <tr>
        <td class="table__column first_column column_text_header column_text_gray">Invoices (Tab):</td>
        <td class="table__column column_text_gray">x</td>
        <td class="table__column column_text_gray">x</td>
        <td class="table__column column_text_gray">o</td>
        <td class="table__column column_text_gray">o</td>
    </tr>
    <tr>
        <td class="table__column first_column">Update (Invoices)</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_red">o</td>
        <td class="table__column column_text_red">o</td>
    </tr>
    <tr>
        <td class="table__column first_column">Change Status (Invoices)</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_red">o</td>
        <td class="table__column column_text_red">o</td>
    </tr>
    <tr>
        <td class="table__column first_column">Delete (Invoices) </td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_red">o</td>
        <td class="table__column column_text_red">o</td>
    </tr>
    <tr>
        <td class="table__column"></td>
        <td class="table__column"></td>
        <td class="table__column"></td>
        <td class="table__column"></td>
        <td class="table__column"></td>
    </tr>
    <tr>
        <td class="table__column first_column column_text_header column_text_gray">Payment Reminders (Tab):</td>
        <td class="table__column column_text_gray">x</td>
        <td class="table__column column_text_gray">x</td>
        <td class="table__column column_text_gray">o</td>
        <td class="table__column column_text_gray">x</td>
    </tr>
    <tr>
        <td class="table__column first_column">Upload PR</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_red">o</td>
        <td class="table__column column_text_red">o</td>
    </tr>
    <tr>
        <td class="table__column first_column">Download Payments</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_red">o</td>
        <td class="table__column column_text_red">o</td>
    </tr>
    <tr>
        <td class="table__column first_column">Send Reminders</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_red">o</td>
        <td class="table__column column_text_red">o</td>
    </tr>
    <tr>
        <td class="table__column first_column">Update (Payment Reminders)</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_red">o</td>
        <td class="table__column column_text_red">o</td>
    </tr>
    <tr>
        <td class="table__column first_column">Delete (Payment Reminders)</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_red">o</td>
        <td class="table__column column_text_red">o</td>
    </tr>
    <tr>
        <td class="table__column first_column">Search (Payment Reminders)</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_green">x</td>
        <td class="table__column column_text_red">o</td>
        <td class="table__column column_text_green">x</td>
    </tr>
    </tbody>
</table>
