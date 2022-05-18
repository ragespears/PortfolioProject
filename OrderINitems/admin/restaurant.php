<?php
/* I certify that this is my own work Danny Gee R01810967 */
defined('admin') or exit;
// Default input restaurant values
$restaurant = array(
    'name' => ''
);
if (isset($_GET['id'])) {
    // ID param exists, edit an existing restaurant
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the restaurant
        $stmt = $pdo->prepare('UPDATE restaurants SET name = ? WHERE id = ?');
        $stmt->execute([ $_POST['name'], $_GET['id'] ]);
        header('Location: index.php?page=restaurants');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Delete the restaurant
        $stmt = $pdo->prepare('DELETE FROM restaurants WHERE id = ?');
        $stmt->execute([ $_GET['id'] ]);
        header('Location: index.php?page=restaurants');
        exit;
    }
    // Get the restaurant from the database
    $stmt = $pdo->prepare('SELECT * FROM restaurants WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $restaurant = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    // Create a new restaurant
    $page = 'Create';
    if (isset($_POST['submit'])) {
        $stmt = $pdo->prepare('INSERT INTO restaurants (name) VALUES (?)');
        $stmt->execute([ $_POST['name'] ]);
        header('Location: index.php?page=restaurants');
        exit;
    }
}
?>

<?=template_admin_header($page . ' Restaurant')?>

<h2><?=$page?> Restaurant</h2>

<div class="content-block">
    <form action="" method="post" class="form responsive-width-100">
        <label for="name">Name</label>
        <input type="text" name="name" placeholder="Name" value="<?=$restaurant['name']?>" required>
        <div class="submit-btns">
            <input type="submit" name="submit" value="Submit">
            <?php if ($page == 'Edit'): ?>
            <input type="submit" name="delete" value="Delete" class="delete">
            <?php endif; ?>
        </div>
    </form>
</div>

<?=template_admin_footer()?>