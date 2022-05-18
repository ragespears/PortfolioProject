<?php
/* I certify that this is my own work Danny Gee R01810967 */
// Prevent direct access to file
defined('orderinsystem') or exit;
// Check for search query
if (isset($_GET['query']) && $_GET['query'] != '') {
    // Escape the user query, prevent XSS attacks
    $search_query = htmlspecialchars($_GET['query'], ENT_QUOTES, 'UTF-8');
    // Select items ordered by the date added
    $stmt = $pdo->prepare('SELECT * FROM items WHERE name LIKE ? ORDER BY date_added DESC');
    // bindValue will allow us to use integer in the SQL statement, we need to use for LIMIT
    $stmt->execute(['%' . $search_query . '%']);
    // Fetch the items from the database and return the result as an Array
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Get the total number of items
    $total_items = count($items);
} else {
    // Simple error, if no search query was specified why is the user on this page?
    $error = 'No search query was specified!';
}
?>

<?=template_header('Search')?>

<?php if ($error): ?>

<p class="content-wrapper error"><?=$error?></p>

<?php else: ?>

<div class="items content-wrapper">

    <h1>Search Results for "<?=$search_query?>"</h1>

    <p><?=$total_items?> Items</p>

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

</div>

<?php endif; ?>

<?=template_footer()?>
