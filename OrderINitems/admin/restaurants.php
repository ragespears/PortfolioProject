<?php
/* I certify that this is my own work Danny Gee R01810967 */
defined('admin') or exit;
// SQL query to get all restaurants from the "restaurants" table
$stmt = $pdo->prepare('SELECT * FROM restaurants');
$stmt->execute();
$restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?=template_admin_header('Restaurants')?>

<h2>Restaurants</h2>

<div class="links">
    <a href="index.php?page=restaurant">Create Restaurant</a>
</div>

<div class="content-block">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td>#</td>
                    <td>Name</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($restaurants)): ?>
                <tr>
                    <td colspan="8" style="text-align:center;">There are no products</td>
                </tr>
                <?php else: ?>
                <?php foreach ($restaurants as $restaurant): ?>
                <tr class="details" onclick="location.href='index.php?page=restaurant&id=<?=$restaurant['id']?>'">
                    <td><?=$restaurant['id']?></td>
                    <td><?=$restaurant['name']?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?=template_admin_footer()?>