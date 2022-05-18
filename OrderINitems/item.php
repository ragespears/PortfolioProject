<?php
/* I certify that this is my own work Danny Gee R01810967 */
// Prevent direct access to file
defined('orderinsystem') or exit;
// Check to make sure the id parameter is specified in the URL
if (isset($_GET['id'])) {
    // Prepare statement and execute, prevents SQL injection
    $stmt = $pdo->prepare('SELECT * FROM items WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    // Fetch the item from the database and return the result as an Array
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if the item exists (array is not empty)
    if (!$item) {
        // Simple error to display if the id for the item doesn't exists (array is empty)
        $error = 'Item does not exist!';
    }
    // Select the item images (if any) from the items_images table
    $stmt = $pdo->prepare('SELECT * FROM items_images WHERE item_id = ?');
    $stmt->execute([ $_GET['id'] ]);
    // Fetch the item images from the database and return the result as an Array
    $item_imgs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Select the item options (if any) from the items_options table
    $stmt = $pdo->prepare('SELECT title, GROUP_CONCAT(name) AS options, GROUP_CONCAT(price) AS prices FROM items_options WHERE item_id = ? GROUP BY title');
    $stmt->execute([ $_GET['id'] ]);
    // Fetch the item options from the database and return the result as an Array
    $item_options = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Simple error to display if the id wasn't specified
    $error = 'item does not exist!';
}
?>

<?=template_header(isset($item) && $item ? $item['name'] : 'Error')?>

<?php if ($error): ?>

<p class="content-wrapper error"><?=$error?></p>

<?php else: ?>

<div class="item content-wrapper">

    <div class="item-imgs">

        <?php if (!empty($item['img']) && file_exists('imgs/' . $item['img'])): ?>
        <img class="item-img-large" src="imgs/<?=$item['img']?>" width="500" height="500" alt="<?=$item['name']?>">
        <?php endif; ?>

        <div class="item-small-imgs">
            <?php foreach ($item_imgs as $item_img): ?>
            <img class="item-img-small<?=$item_img['img']==$item['img']?' selected':''?>" src="imgs/<?=$item_img['img']?>" width="150" height="150" alt="<?=$item['name']?>">
            <?php endforeach; ?>
        </div>

    </div>

    <div class="item-wrapper">

        <h1 class="name"><?=$item['name']?></h1>

        <span class="price">
            <?=currency_code?><?=number_format($item['price'],2)?>
        </span>

        <form id="item-form" action="index.php?page=cart" method="post">
            <?php foreach ($item_options as $option): ?>
            <select name="option-<?=$option['title']?>" required>
                <option value="" selected disabled style="display:none"><?=$option['title']?></option>
                <?php
                $options_names = explode(',', $option['options']);
                $options_prices = explode(',', $option['prices']);
                ?>
                <?php foreach ($options_names as $k => $name): ?>
                <option value="<?=$name?>" data-price="<?=$options_prices[$k]?>"><?=$name?></option>
                <?php endforeach; ?>
            </select>
            <?php endforeach; ?>
            <input type="number" name="quantity" value="1" min="1" <?php if ($item['quantity'] != -1): ?>max="<?=$item['quantity']?>"<?php endif; ?> placeholder="Quantity" required>
            <input type="hidden" name="item_id" value="<?=$item['id']?>">
            <input type="submit" value="<?=$item['quantity']==0?'Out of Stock':'Add To Cart'?>">
        </form>

        <div class="description">
            <?=$item['desc']?>
        </div>

    </div>

</div>

<?php endif; ?>

<?=template_footer()?>
