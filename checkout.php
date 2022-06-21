<?php
$items = $_POST;
array_pop($items);
if (empty($items)) {
    header("location: index.php");
}

use App\CartItem\Database;
use App\CartItem\SpecialOffer;
use App\CartItem\CalculateItemPrice;

include_once("src/CartItem/DBInterface.php");
include_once("src/CartItem/Database.php");
include_once("src/CartItem/SpecialOffer.php");
include_once("src/CartItem/CalculateItemPrice.php");

$db = new Database();
$specialOffer = new SpecialOffer($db);
$calculateItemPrice = new CalculateItemPrice($db, $specialOffer);
$calculateItemPrice->setItems($items);
?>
<html>
<head>
    <title>Item price calculation</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">
    <h3>&nbsp;</h3>
    <h3 class="center-text"><a  style="float: left" href="index.php" class="btn btn-secondary active" role="button" aria-pressed="true">Back</a> Cart Items</h3>

    <table class="table">
        <thead class="thead-dark">
        <tr>

            <th scope="col">Item</th>
            <th scope="col">Qty.</th>
            <th scope="col">Total Price</th>
        </tr>
        </thead>
        <tbody>
        <form name="addItem" action="" method="post">
            <?php
            $grandTotal = 0;
            foreach ($items as $item) {
                /*if (count($item) <= 2) {
                    continue;
                }*/
                $price = $calculateItemPrice->calculatePrice($item['id'], $item['qty']);
                $grandTotal += $price;
                ?>
                <tr class="">
                    <td><?php echo $item['item'] ?></td>
                    <td><?php echo $item['qty']; ?></td>
                    <td><?php echo $price ?></td>
                </tr>
            <?php } ?>

        </form>
        </tbody>
    </table>
    <div class="row total-height">
        <span class="right-text">Grand Total: <?php echo $grandTotal; ?></span>
    </div>

</div>
</body>
</html>
