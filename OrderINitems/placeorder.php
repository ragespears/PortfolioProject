<?php
/* I certify that this is my own work Danny Gee R01810967 */
// Prevent direct access to file
defined('orderinsystem') or exit;
// Remove all the items in cart, no longer needed as the order has been processed
unset($_SESSION['cart']);
?>
<?=template_header('Place Order')?>

<?php if ($error): ?>
<p class="content-wrapper error"><?=$error?></p>
<?php else: ?>
<div class="placeorder content-wrapper">
    <h1>Your Order Has Been Placed</h1>
    <p>Thank you for ordering with us, we'll contact you by email with your order details.</p>
</div>
<?php endif; ?>

<?=template_footer()?>
