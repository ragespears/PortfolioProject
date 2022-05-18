<?php
/* I certify that this is my own work Danny Gee R01810967 */
defined('admin') or exit;
// Default input restaurant values
$transactions = array(
    'payment_status' => ''
);
if (isset($_GET['id'])) {
    // ID param exists, edit an existing restaurant
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the restaurant
        $stmt = $pdo->prepare('UPDATE transactions SET payment_status = ? WHERE id = ?');
        $stmt->execute([ $_POST['status'], $_GET['id'] ]);
        header('Location: index.php?page=status');
        exit;
    }
    // Get the restaurant from the database
    $stmt = $pdo->prepare('SELECT * FROM transactions WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $restaurant = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<?=template_manager_header($page . ' Status')?>

<h2><?=$page?> Restaurant</h2>

<div class="content-block">
    <form action="" method="post" class="form responsive-width-100">
        <label for="status">Status</label>
        <input type="text" name="status" placeholder="Status" value="<?=$transactions['payment_status']?>" required>
        <div class="submit-btns">
            <input type="submit" name="submit" value="Submit">
        </div>
    </form>
</div>

<?=template_manager_footer()?>