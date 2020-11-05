<?php if (!defined('APPLICATION')) {die('Access Denied');} ?>
<!DOCTYPE html>
<html>
<head>
    <title>Page Title</title>
    <link rel="stylesheet" href="assets/bootstrap.min.css">
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
<div class="container p-5 text-center">
    <div class="col-md-6 offset-md-3 offset-sm-1 col-sm-10 col-lg-4 offset-lg-4">
        <div class="card">
            <img class="card-img-top" src="assets/img.jpg" alt="Credit Card Payment">
            <div class="card-body">
                <div class="text-center">
                    Pleas wait while we redirect you.
                </div>
                <form id="payment-form">
                    <div id="card-errors" class="text-danger" role="alert">

                    </div>
                    <input type="hidden" id="stripe-session" value="{{sessionId}}">
                </form>
            </div>
        </div>
        <p class="text-secondary">
            {{company}}
        </p>
    </div>
</div>
</body>
<script>

    var stripe = Stripe('{{stripe_key}}');

    function payment(sessionId) {
        stripe.redirectToCheckout({
            "sessionId": sessionId
        }).then(function(result) {
            document.getElementById('card-errors').innerHTML = result.error.message
        });
    }

    (function() {
        var sessionId = document.getElementById('stripe-session').value;
        payment(sessionId);
    })();


</script>
</html>