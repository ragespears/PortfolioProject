<?php
/* I certify that this is my own work Danny Gee R01810967 */
// Prevent direct access to file
defined('orderinsystem') or exit;
//Get all the restaurants from the database
$stmt = $pdo->query('SELECT * FROM restaurants');
$stmt->execute();
$restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
//get the current restaurant catergory request, if none exists set the default selected category to all 
$restaurant = isset($_GET['restaurant']) ? $_GET['restaurant'] : 'all';
$restaurant_sql = '';

// Get all the categories from the database
$stmt = $pdo->query('SELECT * FROM categories');
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Get the current category from the GET request, if none exists set the default selected category to: all
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$category_sql = '';
if ($category != 'all' && $restaurant == 'all') {
    $category_sql = 'JOIN items_categories ic ON ic.category_id = :category_id AND ic.item_id = i.id JOIN categories c ON c.id = ic.category_id';
}
if ($restaurant != 'all' && $category == 'all') {
    $restaurant_sql = 'JOIN items_restaurants ir ON ir.restaurant_id = :restaurant_id AND ir.item_id = i.id JOIN restaurants r ON r.id = ir.restaurant_id';
}
if ($restaurant != 'all' && $category != 'all') {
    $restaurant_sql = 'JOIN items_restaurants ir ON ir.restaurant_id = :restaurant_id AND ir.item_id = i.id JOIN restaurants r ON r.id = ir.restaurant_id JOIN items_categories ic ON ic.category_id = :category_id AND ic.item_id = i.id JOIN categories c ON c.id = ic.category_id';
}
// Get the sort from GET request, will occur if the user changes an item in the select box
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'sort3';
// The amounts of items to show on each page
$num_items_on_each_page = 8;
// The current page, in the URL this will appear as index.php?page=items&i=1, index.php?page=items&i=2, etc...
$current_page = isset($_GET['i']) && is_numeric($_GET['i']) ? (int)$_GET['i'] : 1;
// Select items ordered by the date added
if ($sort == 'sort1') {
    // sort1 = Alphabetical A-Z
    $stmt = $pdo->prepare('SELECT i.* FROM items i ' . $restaurant_sql . $category_sql .  ' ORDER BY i.name ASC LIMIT :page,:num_items');
} elseif ($sort == 'sort2') {
    // sort2 = Alphabetical Z-A
    $stmt = $pdo->prepare('SELECT i.* FROM items i ' . $restaurant_sql . $category_sql . ' ORDER BY i.name DESC LIMIT :page,:num_items');
} elseif ($sort == 'sort3') {
    // sort3 = Newest
    $stmt = $pdo->prepare('SELECT i.* FROM items i ' . $restaurant_sql . $category_sql . ' ORDER BY i.date_added DESC LIMIT :page,:num_items');
} elseif ($sort == 'sort4') {
    // sort4 = Oldest
    $stmt = $pdo->prepare('SELECT i.* FROM items i ' . $restaurant_sql . $category_sql . ' ORDER BY i.date_added ASC LIMIT :page,:num_items');
} else {
    // No sort was specified, get the items with no sorting
    $stmt = $pdo->prepare('SELECT i.* FROM items i ' . $restaurant_sql . $category_sql . ' LIMIT :page,:num_items');
}
if ($restaurant != 'all') {
    $stmt->bindValue(':restaurant_id', $restaurant, PDO::PARAM_INT);
}
// bindValue will allow us to use integer in the SQL statement, we need to use for LIMIT
if ($category != 'all') {
    $stmt->bindValue(':category_id', $category, PDO::PARAM_INT);
}
$stmt->bindValue(':page', ($current_page - 1) * $num_items_on_each_page, PDO::PARAM_INT);
$stmt->bindValue(':num_items', $num_items_on_each_page, PDO::PARAM_INT);
$stmt->execute();
// Fetch the items from the database and return the result as an Array
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Get the total number of items
$stmt = $pdo->prepare('SELECT COUNT(*) FROM items i ' .$restaurant_sql . $category_sql);
if ($category != 'all') {
    $stmt->bindValue(':category_id', $category, PDO::PARAM_INT);
}
if ($restaurant != 'all') {
    $stmt->bindValue(':restaurant_id', $restaurant, PDO::PARAM_INT);
 }
$stmt->execute();
$total_items = $stmt->fetchColumn()
?>

<?=template_header('Items')?>

<div class="items content-wrapper">

    <h1>Items</h1>

    <div class="items-header">
        <p><?=$total_items?> Items</p>
        <form action="" method="get" class="items-form">
            <input type="hidden" name="page" value="items">
            <label class="restaurant">
                Restaurant
                <select name="restaurant">
                    <option value="all"<?=($restaurant == 'all' ? ' selected' : '')?>>All</option>
                    <?php foreach ($restaurants as $r): ?>
                    <option value="<?=$r['id']?>"<?=($restaurant == $r['id'] ? ' selected' : '')?>><?=$r['name']?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="category">
                Category
                <select name="category">
                    <option value="all"<?=($category == 'all' ? ' selected' : '')?>>All</option>
                    <?php foreach ($categories as $c): ?>
                    <option value="<?=$c['id']?>"<?=($category == $c['id'] ? ' selected' : '')?>><?=$c['name']?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="sortby">
                Sort by
                <select name="sort">
                    <option value="sort1"<?=($sort == 'sort1' ? ' selected' : '')?>>Alphabetical A-Z</option>
                    <option value="sort2"<?=($sort == 'sort2' ? ' selected' : '')?>>Alphabetical Z-A</option>
                    <option value="sort3"<?=($sort == 'sort3' ? ' selected' : '')?>>Newest</option>
                    <option value="sort4"<?=($sort == 'sort4' ? ' selected' : '')?>>Oldest</option>
                </select>
            </label>
        </form>
    </div>

    <div class="items-wrapper">
        <?php foreach ($items as $item): ?>
        <a href="index.php?page=item&id=<?=$item['id']?>" class="item">
            <?php if (!empty($item['img']) && file_exists('imgs/' . $item['img'])): ?>
            <img src="imgs/<?=$item['img']?>" width="200" height="200" alt="<?=$item['name']?>">
            <?php endif; ?>
            <span class="name"><?=$item['name']?></span>
            <span class="price">
                <?=currency_code?><?=number_format($item['price'],2)?>
            </span>
        </a>
        <?php endforeach; ?>
    </div>

    <div class="buttons">
        <?php if ($current_page > 1): ?>
        <a href="index.php?page=items&i=<?=$current_page-1?>&restaurant=<?=$restaurant?>&category=<?=$category?>&sort=<?=$sort?>">Prev</a>
        <?php endif; ?>
        <?php if ($total_items > ($current_page * $num_items_on_each_page) - $num_items_on_each_page + count($items)): ?>
        <a href="index.php?page=items&i=<?=$current_page+1?>&restaurant=<?=$restaurant?>&category=<?=$category?>&sort=<?=$sort?>">Next</a>
        <?php endif; ?>
    </div>

</div>

<?=template_footer()?>
