<?php
/* I certify that this is my own work Danny Gee R01810967 */
defined('admin') or exit;
// Default input item values
$item = array(
    'name' => '',
    'desc' => '',
    'price' => 0,
    'quantity' => 1,
    'date_added' => date('Y-m-d\TH:i:s'),
    'img' => '',
    'imgs' => '',
    'categories' => array(),
    'options' => array(),
    'options_string' => ''
);
// Get all the categories from the database
$stmt = $pdo->query('SELECT * FROM categories');
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all the restaurant from the database
$stmt = $pdo->query('SELECT * FROM restaurants');
$stmt->execute();
$restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all the images from the "imgs" directory
$imgs = glob('../imgs/*.{jpg,png,gif,jpeg,webp}', GLOB_BRACE);
// Add item images to the database*****
function addItemImages($pdo, $item_id) {
    if (isset($_POST['images_list'])) {
        $images_list = explode(',', $_POST['images_list']);
        $in  = str_repeat('?,', count($images_list) - 1) . '?';
        $stmt = $pdo->prepare('DELETE FROM items_images WHERE item_id = ? AND img NOT IN (' . $in . ')');
        $stmt->execute(array_merge([ $item_id ], $images_list));
        foreach ($images_list as $img) {
            if (empty($img)) continue;
            $stmt = $pdo->prepare('INSERT IGNORE INTO items_images (item_id,img) VALUES (?,?)');
            $stmt->execute([ $item_id, $img ]);
        }
    }
}
// Add item categories to the database*****
function addItemCategories($pdo, $item_id) {
    if (isset($_POST['categories_list'])) {
        $list = explode(',', $_POST['categories_list']);
        $in  = str_repeat('?,', count($list) - 1) . '?';
        $stmt = $pdo->prepare('DELETE FROM items_categories WHERE item_id = ? AND category_id NOT IN (' . $in . ')');
        $stmt->execute(array_merge([ $item_id ], $list));
        foreach ($list as $cat) {
            if (empty($cat)) continue;
            $stmt = $pdo->prepare('INSERT IGNORE INTO items_categories (item_id,category_id) VALUES (?,?)');
            $stmt->execute([ $item_id, $cat ]);
        }
    }
}

// Add item restaurant to the database*****
function addItemRestaurants($pdo, $item_id, $restaurant_id) {
            $stmt = $pdo->prepare('INSERT IGNORE INTO items_restaurants (item_id,restaurant_id) VALUES (?,?)');
            $stmt->execute([ $item_id, $restaurant_id ]);
}
$restaurant = isset($_SESSION['account_rID']) ? $_SESSION['account_rID'] : '';
$stmt->bindValue(':restaurant_id', $restaurant, PDO::PARAM_INT);
// Add item options to the database
function addItemOptions($pdo, $item_id) {
    if (isset($_POST['options'])) {
        $list = explode(',', $_POST['options']);
        $stmt = $pdo->prepare('SELECT * FROM items_options WHERE item_id = ?');
        $stmt->execute([ $_GET['id'] ]);
        $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $remove_list = array();
        foreach ($options as $option) {
            $option_string = $option['title'] . '__' . $option['name'] . '__' . $option['price'];
            if (!in_array($option_string, $list)) {
                $remove_list[] = $option['id'];
            } else {
                array_splice($list, array_search($option_string, $list), 1);
            }
        }
        $in = str_repeat('?,', count($remove_list) - 1) . '?';
        $stmt = $pdo->prepare('DELETE FROM items_options WHERE id IN (' . $in . ')');
        $stmt->execute($remove_list);
        foreach ($list as $option) {
            if (empty($option)) continue;
            $option = explode('__', $option);
            $stmt = $pdo->prepare('INSERT INTO items_options (title,name,price,item_id) VALUES (?,?,?,?)');
            $stmt->execute([ $option[0], $option[1], $option[2], $item_id ]);
        }
    }
}
if (isset($_GET['id'])) {
    // ID param exists, edit an existing item
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the item
        $stmt = $pdo->prepare('INSERT IGNORE INTO items (name,`desc`,price,quantity,img,`time`,date_added) VALUES (?,?,?,?,?,?)');
        $stmt->execute([ $_POST['name'], $_POST['desc'], $_POST['price'], $_POST['quantity'], $_POST['main_image'], $_POST['time'], date('Y-m-d H:i:s', strtotime($_POST['date'])) ]);
        addItemImages($pdo, $_GET['id']);
        addItemRestaurants($pdo, $_GET['id'], $restaurant);
        addItemCategories($pdo, $_GET['id']);
        addItemOptions($pdo, $_GET['id']);
        header('Location: index.php?page=items');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Delete the item and its images, categories, options
        $stmt = $pdo->prepare('DELETE i, ii, io, ic, ir FROM items i LEFT JOIN items_images ii ON ii.item_id = i.id LEFT JOIN items_options io ON io.item_id = i.id LEFT JOIN items_restaurants ir ON ir.item_id = i.id LEFT JOIN items_categories ic ON ic.item_id = i.id WHERE i.id = ?');
        $stmt->execute([ $_GET['id'] ]);
        header('Location: index.php?page=items');
        exit;
    }
    // Get the item and its images from the database
    $stmt = $pdo->prepare('SELECT i.*, GROUP_CONCAT(ii.img) AS imgs FROM items i LEFT JOIN items_images ii ON i.id = ii.item_id WHERE i.id = ? GROUP BY i.id');
    $stmt->execute([ $_GET['id'] ]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    // Get the item restaurant
    $stmt = $pdo->prepare('SELECT r.name, r.id FROM items_restaurants ir JOIN restaurants r ON r.id = ir.restaurant_id WHERE ir.item_id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $item['categories'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Get the item categories
    $stmt = $pdo->prepare('SELECT c.name, c.id FROM items_categories ic JOIN categories c ON c.id = ic.category_id WHERE ic.item_id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $item['categories'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Get the item options
    $stmt = $pdo->prepare('SELECT * FROM items_options WHERE item_id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $item['options'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $item['options_string'] = '';
    foreach($item['options'] as $option) {
        $item['options_string'] .= $option['title'] . '__' . $option['name'] . '__' . $option['price'] . ',';
    }
    $item['options_string'] = rtrim($item['options_string'], ',');
} else {
    // Create a new item
    $page = 'Create';
    if (isset($_POST['submit'])) {
        $stmt = $pdo->prepare('INSERT IGNORE INTO items (name,`desc`,price,quantity,img,time,date_added) VALUES (?,?,?,?,?,?,?)');
        $stmt->execute([ $_POST['name'], $_POST['desc'], $_POST['price'], $_POST['quantity'], $_POST['main_image'], $_POST['time'], date('Y-m-d H:i:s', strtotime($_POST['date'])) ]);
        $last_id = $pdo->lastInsertId();
        addItemImages($pdo, $last_id);
        addItemRestaurants($pdo, $last_id, $restaurant);
        addItemCategories($pdo, $last_id);
        addItemOptions($pdo, $last_id);
        header('Location: index.php?page=items');
        exit;
    }
}
?>

<?=template_admin_header($page . ' Item')?>

<h2><?=$page?> Item</h2>

<div class="content-block">

    <form action="" method="post" class="form responsive-width-100">

        <label for="name">Name</label>
        <input type="text" name="name" placeholder="Name" value="<?=$item['name']?>" required>

        <label for="desc">Description (HTML)</label>
        <textarea name="desc" placeholder="Item Description (HTML)"><?=$item['desc']?></textarea>

        <label for="price">Price</label>
        <input type="number" name="price" placeholder="Price" min="0" step=".01" value="<?=$item['price']?>" required>

        <label for="quantity">Quantity</span></label>
        <input type="number" name="quantity" placeholder="Quantity" min="-1" value="<?=$item['quantity']?>" title="-1 = unlimited" required>

        <label for="time">Time</span></label>
        <input type="number" name="time" placeholder="Time" min="-1" value="<?=$item['time']?>">

        <label for="date">Date Added</label>
        <input type="datetime-local" name="date" placeholder="Date" value="<?=date('Y-m-d\TH:i:s', strtotime($item['date_added']))?>" required>

        <label for="add_restaurants">Restaurants</label>
        <div style="display:flex;flex-flow:wrap;">
            <select name="add_restaurants" id="add_restaurants" style="width:50%;" multiple>
                <?php foreach ($restaurants as $rest): ?>
                <option value="<?=$rest['id']?>"><?=$rest['name']?></option>
                <?php endforeach; ?>
            </select>
            <select name="restaurants" style="width:50%;" multiple>
                <?php foreach ($item['restaurants'] as $rest): ?>
                <option value="<?=$rest['id']?>"><?=$rest['name']?></option>
                <?php endforeach; ?>
            </select>
            <button id="add_selected_restaurants" style="width:50%;">Add</button>
            <button id="remove_selected_restaurants" style="width:50%;">Remove</button>
            <input type="hidden" name="restaurants_list" value="<?=implode(',', array_column($item['restaurants'], 'id'))?>">
        </div>

        <label for="add_categories">Categories</label>
        <div style="display:flex;flex-flow:wrap;">
            <select name="add_categories" id="add_categories" style="width:50%;" multiple>
                <?php foreach ($categories as $cat): ?>
                <option value="<?=$cat['id']?>"><?=$cat['name']?></option>
                <?php endforeach; ?>
            </select>
            <select name="categories" style="width:50%;" multiple>
                <?php foreach ($item['categories'] as $cat): ?>
                <option value="<?=$cat['id']?>"><?=$cat['name']?></option>
                <?php endforeach; ?>
            </select>
            <button id="add_selected_categories" style="width:50%;">Add</button>
            <button id="remove_selected_categories" style="width:50%;">Remove</button>
            <input type="hidden" name="categories_list" value="<?=implode(',', array_column($item['categories'], 'id'))?>">
        </div>

        <label for="add_option">Options</label>
        <div style="display:flex;flex-flow:wrap;">
            <input type="text" name="option_title" placeholder="Option Title (e.g. Size)" style="width:47%;margin-right:13px;">
            <input type="text" name="option_name" placeholder="Option Name (e.g. Large)" style="width:50%;">
            <input type="number" name="option_price" min="0" step=".01" placeholder="Option Price (e.g. 15.00)">
            <button id="add_option" style="margin-bottom:10px;">Add</button>
            <select name="options" multiple>
                <?php foreach ($item['options'] as $option): ?>
                <option value="<?=$option['title']?>__<?=$option['name']?>__<?=$option['price']?>"><?=$option['title']?>,<?=$option['name']?>,<?=$option['price']?></option>
                <?php endforeach; ?>
            </select>
            <button id="remove_selected_options">Remove</button>
            <input type="hidden" name="options" value="<?=$item['options_string']?>">
        </div>

        <label for="add_images">Images</label>
        <div style="display:flex;flex-flow:wrap;">
            <select name="add_images" id="add_images" style="width:50%;" multiple>
                <?php foreach ($imgs as $img): ?>
                <option value="<?=basename($img)?>"><?=basename($img)?></option>
                <?php endforeach; ?>
            </select>
            <select name="images" style="width:50%;" multiple>
                <?php foreach (explode(',', $item['imgs']) as $img): ?>
                <?php if (!empty($img)): ?>
                <option value="<?=$img?>"><?=$img?></option>
                <?php endif; ?>
                <?php endforeach; ?>
            </select>
            <button id="add_selected_images" style="width:50%;">Add</button>
            <button id="remove_selected_images" style="width:50%;">Remove</button>
            <input type="hidden" name="images_list" value="<?=$item['imgs']?>">
        </div>

        <div>
            <label for="main_image">Main Image</label>
            <select name="main_image" id="main_image">
                <?php foreach (explode(',', $item['imgs']) as $img): ?>
                <option value="<?=$img?>"<?=$item['img'] == $img ? ' selected' : ''?>><?=$img?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="submit-btns">
            <input type="submit" name="submit" value="Submit">
            <?php if ($page == 'Edit'): ?>
            <input type="submit" name="delete" value="Delete" class="delete">
            <?php endif; ?>
        </div>

    </form>

</div>

<script>
document.querySelector("#remove_selected_options").onclick = function(e) {
    e.preventDefault();
    document.querySelectorAll("select[name='options'] option").forEach(function(option) {
        if (option.selected) {
            let list = document.querySelector("input[name='options']").value.split(",");
            list.splice(list.indexOf(option.value), 1);
            document.querySelector("input[name='options']").value = list.join(",");
            option.remove();
        }
    });
};
document.querySelector("#add_option").onclick = function(e) {
    e.preventDefault();
    if (document.querySelector("input[name='option_title']").value == "") {
        document.querySelector("input[name='option_title']").focus();
        return;
    }
    if (document.querySelector("input[name='option_name']").value == "") {
        document.querySelector("input[name='option_name']").focus();
        return;
    }
    if (document.querySelector("input[name='option_price']").value == "") {
        document.querySelector("input[name='option_price']").focus();
        return;
    }
    let option = document.createElement("option");
    option.value = document.querySelector("input[name='option_title']").value + '__' + document.querySelector("input[name='option_name']").value + '__' + document.querySelector("input[name='option_price']").value;
    option.text = document.querySelector("input[name='option_title']").value + ',' + document.querySelector("input[name='option_name']").value + ',' + document.querySelector("input[name='option_price']").value;
    document.querySelector("select[name='options']").add(option);
    document.querySelector("input[name='option_title']").value = "";
    document.querySelector("input[name='option_name']").value = "";
    document.querySelector("input[name='option_price']").value = "";
    document.querySelectorAll("select[name='options'] option").forEach(function(option) {
        let list = document.querySelector("input[name='options']").value.split(",");
        if (!list.includes(option.value)) {
            list.push(option.value);
        }
        document.querySelector("input[name='options']").value = list.join(",");
    });
};

document.querySelector("#remove_selected_restaurants").onclick = function(e) {
    e.preventDefault();
    document.querySelectorAll("select[name='restaurants'] option").forEach(function(option) {
        if (option.selected) {
            let list = document.querySelector("input[name='restaurants_list']").value.split(",");
            list.splice(list.indexOf(option.value), 1);
            document.querySelector("input[name='restaurants_list']").value = list.join(",");
            option.remove();
        }
    });
};
document.querySelector("#add_selected_restaurants").onclick = function(e) {
    e.preventDefault();
    document.querySelectorAll("select[name='add_restaurants'] option").forEach(function(option) {
        if (option.selected) {
            let list = document.querySelector("input[name='restaurants_list']").value.split(",");
            if (!list.includes(option.value)) {
                list.push(option.value);
            }
            document.querySelector("input[name='restaurants_list']").value = list.join(",");
            document.querySelector("select[name='restaurants']").add(option.cloneNode(true));
        }
    });
};

document.querySelector("#remove_selected_categories").onclick = function(e) {
    e.preventDefault();
    document.querySelectorAll("select[name='categories'] option").forEach(function(option) {
        if (option.selected) {
            let list = document.querySelector("input[name='categories_list']").value.split(",");
            list.splice(list.indexOf(option.value), 1);
            document.querySelector("input[name='categories_list']").value = list.join(",");
            option.remove();
        }
    });
};
document.querySelector("#add_selected_categories").onclick = function(e) {
    e.preventDefault();
    document.querySelectorAll("select[name='add_categories'] option").forEach(function(option) {
        if (option.selected) {
            let list = document.querySelector("input[name='categories_list']").value.split(",");
            if (!list.includes(option.value)) {
                list.push(option.value);
            }
            document.querySelector("input[name='categories_list']").value = list.join(",");
            document.querySelector("select[name='categories']").add(option.cloneNode(true));
        }
    });
};
document.querySelector("#remove_selected_images").onclick = function(e) {
    e.preventDefault();
    document.querySelectorAll("select[name='images'] option").forEach(function(option) {
        if (option.selected) {
            let images_list = document.querySelector("input[name='images_list']").value.split(",");
            images_list.splice(images_list.indexOf(option.value), 1);
            document.querySelector("input[name='images_list']").value = images_list.join(",");
            document.querySelectorAll("select[name='main_image'] option").forEach(i => i.value == option.value ? i.remove() : false);
            option.remove();
        }
    });
};
document.querySelector("#add_selected_images").onclick = function(e) {
    e.preventDefault();
    document.querySelectorAll("select[name='add_images'] option").forEach(function(option) {
        if (option.selected) {
            let images_list = document.querySelector("input[name='images_list']").value.split(",");
            if (!images_list.includes(option.value)) {
                images_list.push(option.value);
            }
            let add_to_main_images = true;
            document.querySelectorAll("select[name='main_image'] option").forEach(i => add_to_main_images = i.value == option.value ? false : add_to_main_images);
            document.querySelector("input[name='images_list']").value = images_list.join(",");
            document.querySelector("select[name='images']").add(option.cloneNode(true));
            if (add_to_main_images) {
                document.querySelector("select[name='main_image']").add(option.cloneNode(true));
            }
        }
    });
};
</script>

<?=template_admin_footer()?>
