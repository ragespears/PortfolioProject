<?php
/* I certify that this is my own work Danny Gee R01810967 */
// Prevent direct access to file
defined('orderinsystem') or exit;
// If the user clicked the add to cart button on the item page we can check for the form data
if (isset($_POST['item_id'], $_POST['quantity']) && is_numeric($_POST['item_id']) && is_numeric($_POST['quantity'])) {
    // Set the post variables so we easily identify them, also make sure they are integer
    $item_id = (int)$_POST['item_id'];
    // abs() function will prevent minus quantity and (int) will make sure the value is an integer
    $quantity = abs((int)$_POST['quantity']);
    // Get item options
    $options = '';
    $options_price = 0.00;
    foreach ($_POST as $k => $v) {
        if (strpos($k, 'option-') !== false) {
            $options .= str_replace('option-', '', $k) . '-' . $v . ',';
            $stmt = $pdo->prepare('SELECT * FROM items_options WHERE title = ? AND name = ? AND item_id = ?');
            $stmt->execute([ str_replace('option-', '', $k), $v, $item_id, $time ]);
            $option = $stmt->fetch(PDO::FETCH_ASSOC);
            $options_price += $option['price'];
        }
    }
    $options = rtrim($options, ',');
    // Prepare the SQL statement, we basically are checking if the item exists in our database
    $stmt = $pdo->prepare('SELECT * FROM items WHERE id = ?');
    $stmt->execute([ $_POST['item_id'] ]);
    // Fetch the item from the database and return the result as an Array
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if the item exists (array is not empty)
    if ($item && $quantity > 0) {
        // Item exists in database, now we can create/update the session variable for the cart
        if (!isset($_SESSION['cart'])) {
            // Shopping cart session variable doesnt exist, create it
            $_SESSION['cart'] = array();
        }
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            $cart_item = &get_cart_item($item_id, $options);
            if ($cart_item) {
                // Item exists in cart so just update the quanity
                $cart_item['quantity'] += $quantity;
            } else {
                // Item is not in cart so add it*****
                $_SESSION['cart'][] = array(
                    'id' => $item_id,
                    'quantity' => $quantity,
                    'options' => $options,
                    'options_price' => $options_price
                );
            }
        }
    }
    // Prevent form resubmission...
    header('location: index.php?page=cart');
    exit;
}
// Remove item from cart, check for the URL param "remove", this is the item id, make sure it's a number and check if it's in the cart
if (isset($_GET['remove']) && is_numeric($_GET['remove']) && isset($_SESSION['cart']) && isset($_SESSION['cart'][$_GET['remove']])) {
    // Remove the item from the shopping cart
    unset($_SESSION['cart'][$_GET['remove']]);
    header('location: index.php?page=cart');
    exit;
}
// Empty the cart
if (isset($_POST['emptycart']) && isset($_SESSION['cart'])) {
    // Remove all items from the shopping cart
    unset($_SESSION['cart']);
    header('location: index.php?page=cart');
    exit;
}
// Update item quantities in cart if the user clicks the "Update" button on the shopping cart page
if (isset($_POST['update']) && isset($_SESSION['cart'])) {
    // Loop through the post data so we can update the quantities for every item in cart
    foreach ($_POST as $k => $v) {
        if (strpos($k, 'quantity') !== false && is_numeric($v)) {
            $id = str_replace('quantity-', '', $k);
            // abs() function will prevent minus quantity and (int) will make sure the number is an integer
            $quantity = abs((int)$v);
            // Always do checks and validation
            if (is_numeric($id) && isset($_SESSION['cart'][$id]) && $quantity > 0) {
                // Update new quantity
                $_SESSION['cart'][$id]['quantity'] = $quantity;
            }
        }
    }
    header('location: index.php?page=cart');
    exit;
}
// Send the user to the place order page if they click the Place Order button, also the cart should not be empty
if (isset($_POST['checkout']) && isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    header('Location: index.php?page=checkout');
    exit;
}
// Check the session variable for items in cart
$items_in_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$subtotal = 0.00;
// If there are items in cart
if ($items_in_cart) {
    // There are items in the cart so we need to select those items from the database
    // Items in cart array to question mark string array, we need the SQL statement to include: IN (?,?,?,...etc)
    $array_to_question_marks = implode(',', array_fill(0, count($items_in_cart), '?'));
    $stmt = $pdo->prepare('SELECT * FROM items WHERE id IN (' . $array_to_question_marks . ')');
    // We use the array_column to retrieve only the id's of the items
    $stmt->execute(array_column($items_in_cart, 'id'));
    // Fetch the items from the database and return the result as an Array
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Iterate the items in cart and add the meta data (item name, desc, etc)
    foreach ($items_in_cart as &$cart_item) {
        foreach ($items as $item) {
            if ($cart_item['id'] == $item['id']) {
                $cart_item['meta'] = $item;
                // Calculate the subtotal
                if ($cart_item['options_price'] > 0) {
                    $subtotal += (float)$cart_item['options_price'] * (int)$cart_item['quantity'];
                } else {
                    $subtotal += (float)$item['price'] * (int)$cart_item['quantity'];
                }
            }
        }
    }
}
?>

<?=template_header('Order IN')?>

<div class="cart content-wrapper">

    <h1>Order IN</h1>

    <form action="index.php?page=cart" method="post">
        <table>
            <thead>
                <tr>
                    <td colspan="2">Item</td>
                    <td></td>
                    <td class="rhide">Price</td>
                    <td>Quantity</td>
                    <td>Total</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items_in_cart)): ?>
                <tr>
                    <td colspan="6" style="text-align:center;">You have no items added in your Shopping Cart</td>
                </tr>
                <?php else: ?>
                <?php foreach ($items_in_cart as $num => $item): ?>
                <tr>
                    <td class="img">
                        <?php if (!empty($item['meta']['img']) && file_exists('imgs/' . $item['meta']['img'])): ?>
                        <a href="index.php?page=item&id=<?=$item['id']?>">
                            <img src="imgs/<?=$item['meta']['img']?>" width="50" height="50" alt="<?=$item['meta']['name']?>">
                        </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="index.php?page=item&id=<?=$item['id']?>"><?=$item['meta']['name']?></a>
                        <br>
                        <a href="index.php?page=cart&remove=<?=$num?>" class="remove">Remove</a>
                    </td>
                    <td class="price">
                        <?=$item['options']?>
                        <input type="hidden" name="options" value="<?=$item['options']?>">
                    </td>
                    <?php if ($item['options_price'] > 0): ?>
                    <td class="price rhide"><?=currency_code?><?=number_format($item['options_price'],2)?></td>
                    <?php else: ?>
                    <td class="price rhide"><?=currency_code?><?=number_format($item['meta']['price'],2)?></td>
                    <?php endif; ?>
                    <td class="quantity">
                        <input type="number" name="quantity-<?=$num?>" value="<?=$item['quantity']?>" min="1" <?php if ($item['meta']['quantity'] != -1): ?>max="<?=$item['meta']['quantity']?>"<?php endif; ?> placeholder="Quantity" required>
                    </td>

                    <?php if ($item['options_price'] > 0): ?>
                    <td class="price"><?=currency_code?><?=number_format($item['options_price'] * $item['quantity'],2)?></td>
                    <?php else: ?>
                    <td class="price"><?=currency_code?><?=number_format($item['meta']['price'] * $item['quantity'],2)?></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <?php $time = 0; ?>
        <?php if (!empty($items_in_cart)): ?>
            <?php foreach ($items as $i): ?> 
                <?php if ($i['time'] >= $time): ?>
                    <?php $time = $i['time']; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
        <div class="subtotal">
            <span class="text">Subtotal</span>
            <span class="price"><?=currency_code?><?=number_format($subtotal,2)?></span>
            <span class="text"></span>
            <span class="text">ETA</span>
            <td class="time"><?=$time . ' min'?></td>
        </div>

        <div class="buttons">
            <input type="submit" value="Empty Cart" name="emptycart">
            <input type="submit" value="Update" name="update">
            <input type="submit" value="Checkout" name="checkout">
        </div>
    </form>
</div>

<?=template_footer()?>
