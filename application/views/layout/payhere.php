<html>
    <head>
        <title>Payhere Checkout Page</title>
    </head>
    <body>
        <center>Please wait, connecting with Payhere....</center>
        <form method='post' action='https://payhere.lk/pay/checkout' name='f1'>
            <?php
                foreach($paytmParams as $name => $value) {
                    echo '<input type="hidden" name="' . $name .'" value="' . $value . '">';
                }
            ?>
        </form>
        <script type="text/javascript">
            document.f1.submit();
        </script>
    </body>
</html>