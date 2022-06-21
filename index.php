<?php
include_once("src/CartItem/DBInterface.php");
include_once("src/CartItem/Database.php");
include_once("src/Item/Item.php");
use App\CartItem\Database;
use App\Item\Item;

$db = new Database();
$item = new Item($db);
$items = $item->getItems();
?>
<html>
<head>
    <title>Item price calculation</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">
    <h3 class="center-text">Select Items</h3>
    <table class="table">
        <thead class="thead-dark">
        <tr>
            <th scope="col">Item</th>
            <th scope="col">Select Item</th>
            <th scope="col">Select Qty.</th>
        </tr>
        </thead>
        <tbody>
        <form name="addItem" action="checkout.php" method="post">
        <?php foreach ($items as $item) { ?>
        <tr>
            <td><?php echo $item['item']?></td>
            <td><input type="checkbox" name=""></td>
            <input type="hidden" name="items_<?php echo $item['id']?>[item]" value="<?php echo $item['item']?>" disabled>
            <input type="hidden" name="items_<?php echo $item['id']?>[id]" value="<?php echo $item['id']?>" disabled>
            <td><select class="form-select col-sm-4" name="items_<?php echo $item['id']?>[qty]" aria-label="Default select" required disabled>
                    <option selected value="">Select Qty.</option>
                    <?php for ($i = 1; $i<= 10; $i++) {?>
                    <option value="<?php echo $i;?>"><?php echo $i;?></option>
                    <?php }?>
                    </select>
                </td>

        </tr>
        <?php } ?>
            <tr >
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><input class="btn btn-secondary" type="submit" id="submit" name="submit" value="Checkout" disabled></td>
            </tr>
        </form>
        </tbody>
    </table>
</div>
</body>

</html>
<script>
    /**
     * Enable the row and qty input when select item checkbox is checked.
     * If anyone item is selected then enable the Checkbox button.
     * If item is not selected then checkbox will remain disabled.
     */
    $('input:checkbox').change(function(){
        if ($(this).is(':checked')) {
            $(this).parents("tr").addClass('table-active');
            $(this).closest('tr').find('input').prop('disabled', false);
            $(this).closest('tr').find('select').prop('disabled', false);
            $(this).closest('tr').find('input[type="hidden"]').prop('disabled', false);
            $(this).closest('tr').find('input[type="checkbox"]').prop('disabled', false);
        } else {
            $(this).parents("tr").removeClass('table-active');
            $(this).closest('tr').find('select').val('');
            $(this).closest('tr').find('select').prop('disabled', true);
            $(this).closest('tr').find('input[type="hidden"]').prop('disabled', true);
            $(this).closest('tr').find('input[type="checkbox"]').prop('disabled', false);
        }
        if ($('input[type=checkbox]').is(":checked")) {
           $("#submit").prop('disabled', false);
            $("#submit").removeClass('btn btn-secondary');
            $("#submit").addClass('btn btn-primary');
        } else {
            $("#submit").addClass('btn btn-secondary');
            $("#submit").prop('disabled', true);
        }
    });
</script>
