<?php
/* I certify that this is my own work Danny Gee R01810967 */
defined('admin') or exit;
// SQL query to get all items from the "items" table
$stmt = $pdo->prepare('SELECT i.*, GROUP_CONCAT(ii.img) AS imgs FROM items i LEFT JOIN items_images ii ON i.id = ii.item_id GROUP BY i.id ORDER BY i.date_added ASC');
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?=template_admin_header('Items')?>

<h2>Items</h2>

<div class="links">
    <a href="index.php?page=item">Create Item</a>
</div>

<div class="content-block">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td class="responsive-hidden">#</td>
                    <td>Name</td>
                    <td>Price</td>
                    <td>Quantity</td>
                    <td class="responsive-hidden">Images</td>
                    <td>Time</td>
                    <td class="responsive-hidden">Created</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                <tr>
                    <td colspan="8" style="text-align:center;">There are no items</td>
                </tr>
                <?php else: ?>
                <?php foreach ($items as $item): ?>
                <tr class="details" onclick="location.href='index.php?page=item&id=<?=$item['id']?>'">
                    <td class="responsive-hidden"><?=$item['id']?></td>
                    <td><?=$item['name']?></td>
                    <td><?=currency_code?><?=number_format($item['price'], 2)?></td>
                    <td><?=$item['quantity']?></td>
                    <td class="responsive-hidden">
                        <?PHP foreach (explode(',',$item['imgs']) as $img): ?>
                        <img src="../imgs/<?=$img?>" width="32" height="32" alt="<?=$img?>">
                        <?php endforeach; ?>
                    </td>
                    <td class="time"><?=$item['time']?></td>
                    <td class="responsive-hidden"><?=date('F j, Y', strtotime($item['date_added']))?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?=template_admin_footer()?>
