<html>
<body>
<table>
    <tr>
        <td style="width: 200px;">
            <h1 class="header__title">Invoice</h1>
        </td>
        <td style="padding-left: 250px; margin-top: -50px;">
            <img src="http://cardbro.cardcompact.uk/backend/web/images/card_compact_logo.jpg" alt="CardCompact" class="header__logo" align="right">
        </td>
    </tr>
    <tr>
        <td style="display: inline-block; width: 30%; margin-top: -115px;" class="left-column">
            <img src="http://cardbro.cardcompact.uk/backend/web/images/MasterCard-logo.jpg" alt="CardCompact" class="left-column__logo">
            <div class="left-column__content">
                <strong class="left-column__content__block__title">CARD COMPACT LIMITED</strong>
                <br>
                <br>
                <strong class="left-column__content__block__title">OFFICE LONDON</strong>
                <p class="left-column__content__block__content">One Canada Square, Canary Wharf, London, E14 5DY, United Kingdom</p>
                <br>
                <strong class="left-column__content__block__title">TELEPHONE</strong>
                <p class="left-column__content__block__content">0044 845 5649404</p>
                <br>
                <div class="left-column__content__block">
                    <strong class="left-column__content__block__title">TELEFAX</strong>
                    <p class="left-column__content__block__content">0044 844 7743593</p>
                    <br>
                    <strong class="left-column__content__block__title">MAIL</strong>
                    <a href="invoice@cardcompact.com" class="left-column__content__block__link">invoice@cardcompact.com</a>
                    <br>
                    <br>
                    <strong class="left-column__content__block__title">INTERNET</strong>
                    <a href="www.cardcompact.com" class="left-column__content__block__link">www.cardcompact.com</a>
                    <br>
                    <br>
                    <strong class="left-column__content__block__title">COMPANY NO</strong>
                    <p class="left-column__content__block__content">7703826</p>
                    <br>
                    <strong class="left-column__content__block__title">VAT Reg. No.</strong>
                    <p class="left-column__content__block__content">GB119702813</p>
                    <br>
                    <strong class="left-column__content__block__title">ACCOUNT NUMBER</strong>
                    <p class="left-column__content__block__content">30236251</p>
                    <br>
                    <strong class="left-column__content__block__title">SORT-CODE/BLZ</strong>
                    <p class="left-column__content__block__content">30236251</p>
                    <br>
                    <strong class="left-column__content__block__title">IBAN</strong>
                    <p class="left-column__content__block__content">DE47 7405 0000 0030 2362 51</p>
                    <br>
                    <strong class="left-column__content__block__title">BIC/SWIFT</strong>
                    <p class="left-column__content__block__content">BYLADEM1PAS</p>
                    <br>
                </div>
            </div>
        </td>
        <td style="display: inline-block; width: 69%;" class="right-column">
            <div class="right-column__content_top">
                <h2 class="right-column__title">
                    <?= $pdfData['company'] ?>
                </h2>
                <h2 class="right-column__title">
                    <?= $pdfData['username'] ?>
                </h2>
                <h2 class="right-column__title">
                    <?= $pdfData['address'] ?>
                    </h2>
                <h2 class="right-column__title">
                    <?= $pdfData['postal_code'] ?>
                    <?= $pdfData['city'] ?>
                </h2>
                <h2 class="right-column__title">
                   <?= $pdfData['country'] ?>
                </h2>
                </div>
            <br>
            <br>
            <div class="right-column__content_date">
                <strong classs="right-column__content_date__text"><?= date('Y-m-d') ?></strong>
            </div>
            <br>
            <br>
            <div class="right-column__content_customizer">
                <h2 class="right-column__content_customizer__title">INVOICE
                    <?= $pdfData['invoiceId'] ?>
                </h2>
            </div>
            <br>
            <br>
            <table class="table">
                <tr class="table__top">
                    <td style="background: #77BB41; color: #fff;">Description</td>
                    <td style="background: #77BB41; color: #fff;">piece/s</td>
                    <td style="background: #77BB41; color: #fff;">basic price</td>
                    <td style="background: #77BB41; color: #fff;">price</td>
                </tr>
                <tr>
                    <td>sold cards
                        <?= $pdfData['productId'] ?>
                    </td>
                    <td>
                        <?= $pdfData['cardNum'] ?>
                    </td>
                    <td>€
                        <?= money_format('%.2n', $pdfData['price']) ?>
                    </td>
                    <td>€
                        <?= money_format('%.2n', $pdfData['price']*$pdfData['cardNum']) ?>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>VAT <?= $pdfData['vatPerc'] ?> %</td>
                    <td>€
                        <?= money_format('%.2n', $pdfData['price']*$pdfData['cardNum']*($pdfData['vatPerc']/100)) ?>
                    </td>
                </tr>
                <tr class="table__bottom">
                    <td></td>
                    <td></td>
                    <td>TOTAL</td>
                    <td>€
                        <?= money_format('%.2n', $pdfData['price']*$pdfData['cardNum']*(1 + $pdfData['vatPerc']/100)) ?>
                    </td>
                </tr>
            </table>
            <br>
            <br>
            <div class="right-column__content_thnks">
                <p>Thank you for your business. This paper is valid without signature.</p>
                <br>
                <p>Our VAT Reg. No. GB119702813</p>
                <br>
                <p>Your VAT Reg. No. <?= $pdfData['vatId'] ?></p>
                <br>
                <p>The amount invoiced should be wired to Card Compact within 5 days of receipt as follows:</p>
                <br>
                <p>Account number: 30236251</p>
                <br>
                <p>Sort Code/BLZ: 74050000, Sparkasse Passau</p>
                <br>
                <p><strong>IBAN</strong> DE47 7405 0000 0030 2362 51 - <strong>BIC/SWIFT</strong> BYLADEM1PAS </p>
            </div>
        </td>
    </tr>
</table>
<br>
<br>
<br>
<br>
<br>
<br>
<h2 class="register" style="text-align: center;">Card Compact is MSP ISO registered at MasterCard International under No 14275.</h2>
</body>
</html>