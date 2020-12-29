<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Paypal Button Implementation</title>

    <style>
        #container {
            width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        #paypal-button-container {
            width: 400px;
            margin-top: 100px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>
<div id="container">
    <h3>Paypal Button Implementation</h3>
    <ol>
        <li>Create order to paypal</li>
        <li>Get approval from customer</li>
        <li>Capture the payment from customer account and process the order in application</li>
    </ol>
    <div id="paypal-button-container"></div>
</div>

{{--<script src="https://www.paypal.com/sdk/js?client-id=Ae8-yztQj7zBSp5c59vd6yvJirYxr2Wen3J7Cv2hUvY5vGqFfYLMpgpKnRraUuCPmwISwYXuEOnOHkug&currency=AUD&disable-funding=credit,card"></script>--}}
<script src="https://www.paypal.com/sdk/js?client-id=Ae8-yztQj7zBSp5c59vd6yvJirYxr2Wen3J7Cv2hUvY5vGqFfYLMpgpKnRraUuCPmwISwYXuEOnOHkug&currency=AUD"></script>
<script>
    // Render the PayPal button into #paypal-button-container
    paypal.Buttons({
        // Call your server to set up the transaction
        createOrder: function (data, actions) {
            return fetch('/create-order', {
                method: 'post'
            }).then(function (res) {
                return res.json();
            }).then(function (orderData) {
                return orderData.id;
            });
        },
        // Call your server to finalize the transaction
        onApprove: function (data, actions) {
            return fetch('/demo/checkout/api/paypal/order/' + data.orderID + '/capture/', {
                method: 'post'
            }).then(function (res) {
                return res.json();
            }).then(function (orderData) {
                // Three cases to handle:
                //   (1) Recoverable INSTRUMENT_DECLINED -> call actions.restart()
                //   (2) Other non-recoverable errors -> Show a failure message
                //   (3) Successful transaction -> Show confirmation or thank you

                // This example reads a v2/checkout/orders capture response, propagated from the server
                // You could use a different API or structure for your 'orderData'
                var errorDetail = Array.isArray(orderData.details) && orderData.details[0];

                if (errorDetail && errorDetail.issue === 'INSTRUMENT_DECLINED') {
                    return actions.restart(); // Recoverable state, per:
                    // https://developer.paypal.com/docs/checkout/integration-features/funding-failure/
                }

                if (errorDetail) {
                    var msg = 'Sorry, your transaction could not be processed.';
                    if (errorDetail.description) msg += '\n\n' + errorDetail.description;
                    if (orderData.debug_id) msg += ' (' + orderData.debug_id + ')';
                    return alert(msg); // Show a failure message
                }

                // Show a success message
                alert('Transaction completed by ' + orderData.payer.name.given_name);
            });
        }

    }).render('#paypal-button-container');
</script>
</body>
</html>
