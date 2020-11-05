<?php if (!defined('APPLICATION')) {die('Access Denied');} ?>
<!DOCTYPE html>
<html>
<head>
    <title>Page Title</title>
    <link rel="stylesheet" href="assets/bootstrap.min.css"></head>
<body>
<div class="container p-5 text-center">
    <div class="col-md-4 offset-md-4 offset-sm-1 col-sm-10 col-lg-4 offset-lg-4">
        <div class="card">
            <img class="card-img-top" src="assets/img.jpg" alt="Credit Card Payment">
            <div class="card-body">
                <div class="text-center">
                    Pleas wait while we are processing your payment.
                </div>
                <form id="post-payment-form" action="{{redirectUrl}}">
                    <input type="hidden" name="transid" value="{{transId}}">
                    <input type="hidden" name="status" value="{{status}}">
                    <input type="hidden" name="rkey" value="{{rkey}}">
                    <input type="hidden" name="checksum" value="{{checksum}}">
                    <input type="hidden" name="sellingamount" value="{{sellingCurrencyAmount}}">
                    <input type="hidden" name="accountingamount" value="{{accountingCurrencyAmount}}">
                </form>
            </div>
        </div>
    </div>
    <p class="text-secondary">
        {{company}}
    </p>

</div>
</body>
<script>

    (function() {
        document.getElementById('post-payment-form').submit();
    })();

</script>
</html>