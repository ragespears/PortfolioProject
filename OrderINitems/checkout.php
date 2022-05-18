<?php
/* I certify that this is my own work Danny Gee R01810967 */
// Prevent direct access to file
defined('orderinsystem') or exit;
// Default values for the input form elements
$account = array(
    'first_name' => '',
    'last_name' => '',
    'address_street' => '',
    'address_city' => '',
    'address_state' => '',
    'address_zip' => '',
    'address_country' => 'United States'
);
// Check if user is logged in
if (isset($_SESSION['account_loggedin'])) {
    $stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ?');
    $stmt->execute([ $_SESSION['account_id'] ]);
    // Fetch the account from the database and return the result as an Array
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
}
// Make sure when the user submits the form all data was submitted and shopping cart is not empty
if (isset($_POST['first_name'], $_POST['last_name'], $_POST['address_street'], $_POST['address_city'], $_POST['address_state'], $_POST['address_zip'], $_POST['address_country'], $_SESSION['cart'])) {
    $account_id = null;
    // If the user is already logged in
    if (isset($_SESSION['account_loggedin'])) {
        // Account logged-in, update the user's details
        $stmt = $pdo->prepare('UPDATE accounts SET first_name = ?, last_name = ?, address_street = ?, address_city = ?, address_state = ?, address_zip = ?, address_country = ? WHERE id = ?');
        $stmt->execute([ $_POST['first_name'], $_POST['last_name'], $_POST['address_street'], $_POST['address_city'], $_POST['address_state'], $_POST['address_zip'], $_POST['address_country'], $_SESSION['account_id'] ]);
        $account_id = $_SESSION['account_id'];
    } else if (isset($_POST['email'], $_POST['password'], $_POST['cpassword']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        // User is not logged in, check if the account already exists with the email they submitted
        $stmt = $pdo->prepare('SELECT id FROM accounts WHERE email = ?');
        $stmt->execute([ $_POST['email'] ]);
    	if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            // Email exists, user should login instead...
    		$error = 'Account already exists with this email, please login instead!';
        } else if ($_POST['password'] != $_POST['cpassword']) {
            // Password and confirm password fields do not match...
            $error = 'Passwords do not match!';
    	} else {
            // Email doesnt exist, create new account
            $stmt = $pdo->prepare('INSERT INTO accounts (email, password, first_name, last_name, address_street, address_city, address_state, address_zip, address_country) VALUES (?,?,?,?,?,?,?,?,?)');
            // Hash the password
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt->execute([ $_POST['email'], $password, $_POST['first_name'], $_POST['last_name'], $_POST['address_street'], $_POST['address_city'], $_POST['address_state'], $_POST['address_zip'], $_POST['address_country'] ]);
            $account_id = $pdo->lastInsertId();
            $stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ?');
            $stmt->execute([ $account_id ]);
            // Fetch the account from the database and return the result as an Array
            $account = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        /******* */
    } else if (strtolower(account_required) == 'true') {
        $error = 'Account creation required!';
    }
    if (!$error) {
        // No errors, process the order
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
        if (isset($_POST['checkout']) && $items_in_cart) {
            // Process Normal Checkout
            // Iterate each item in the user's shopping cart
            // Unqiue transaction ID
            $transaction_id = strtoupper(uniqid('SC') . substr(md5(mt_rand()), 0, 5));
            $stmt = $pdo->prepare('INSERT INTO transactions (txn_id, payment_amount, payment_status, created, payer_email, first_name, last_name, address_street, address_city, address_state, address_zip, address_country, account_id, payment_method) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $stmt->execute([
                $transaction_id,
                $subtotal,
                'In-Progress',
                date('Y-m-d H:i:s'),
                $account ? $account['email'] : $_POST['email'],
                $_POST['first_name'],
                $_POST['last_name'],
                $_POST['address_street'],
                $_POST['address_city'],
                $_POST['address_state'],
                $_POST['address_zip'],
                $_POST['address_country'],
                $account_id,
                'website'
            ]);
            $order_id = $pdo->lastInsertId();
            foreach ($items_in_cart as $item) {
                // For every item in the shopping cart insert a new transaction into our database
                $stmt = $pdo->prepare('INSERT INTO transactions_items (txn_id, item_id, item_price, item_quantity, item_options) VALUES (?,?,?,?,?)');
                $stmt->execute([ $transaction_id, $item['id'], $item['options_price'] > 0 ? $item['options_price'] : $item['meta']['price'], $item['quantity'], $item['options'] ]);
                // Update item quantity in the items table
                $stmt = $pdo->prepare('UPDATE items SET quantity = quantity - ? WHERE quantity > 0 AND id = ?');
                $stmt->execute([ $item['quantity'], $item['id'] ]);
            }
            if ($account_id != null) {
                // Log the user in with the details provided
                session_regenerate_id();
                $_SESSION['account_loggedin'] = TRUE;
                $_SESSION['account_id'] = $account_id;
                $_SESSION['account_admin'] = $account ? $account['admin'] : 0;
            }
            send_order_details_email(
                $account ? $account['email'] : $_POST['email'],
                $items_in_cart,
                $_POST['first_name'],
                $_POST['last_name'],
                $_POST['address_street'],
                $_POST['address_city'],
                $_POST['address_state'],
                $_POST['address_zip'],
                $_POST['address_country'],
                $subtotal,
                $order_id
            );
            header('Location: index.php?page=placeorder');
            exit;
        }
        // When the user clicks the PayPal checkout button the below code will execute.*******
        if (isset($_POST['paypal']) && $items_in_cart) {
            // Process PayPal Checkout
            // Variables we need to pass to paypal
            // Make sure you have a business account and set the "business" variable to your paypal business account email
            $data = array(
                'cmd'			=> '_cart',
                'upload'        => '1',
                'custom'        => $account_id,
                'lc'			=> paypal_language,
                'business' 		=> paypal_email,
                'cancel_return'	=> paypal_cancel_url,
                'notify_url'	=> paypal_ipn_url,
                'currency_code'	=> paypal_currency,
                'return'        => paypal_return_url
            );
            // Add all the items that are in the shopping cart to the data array variable
            for ($i = 0; $i < count($items_in_cart); $i++) {
                $data['item_number_' . ($i+1)] = $items_in_cart[$i]['id'];
                $data['item_name_' . ($i+1)] = str_replace(array('(', ')'), '', $items_in_cart[$i]['meta']['name']);
                $data['quantity_' . ($i+1)] = $items_in_cart[$i]['quantity'];
                $data['amount_' . ($i+1)] = $items_in_cart[$i]['options_price'] > 0 ? $items_in_cart[$i]['options_price'] : $items_in_cart[$i]['meta']['price'];
                $data['on0_' . ($i+1)] = 'Options';
                $data['os0_' . ($i+1)] = $items_in_cart[$i]['options'];
            }
            if ($account_id != null) {
                // Log the user in with the details provided
                session_regenerate_id();
                $_SESSION['account_loggedin'] = TRUE;
                $_SESSION['account_id'] = $account_id;
                $_SESSION['account_admin'] = $account ? $account['admin'] : 0;
            }
            // Redirect the user to the PayPal checkout screen
            header('location:' . (strtolower(paypal_testmode) == 'true' ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr') . '?' . http_build_query($data));
            // End the script don't need to execute anything else
            exit;
        }
    }
}
if (empty($_SESSION['cart'])) {
    header('Location: index.php?page=cart');
    exit;
}
// List of countries available, feel free to remove any country from the array
$countries = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");

?>

<?=template_header('Checkout')?>

<div class="checkout content-wrapper">

    <h1>Checkout</h1>

    <p class="error"><?=$error?></p>

    <?php if (!isset($_SESSION['account_loggedin'])): ?>
    <p>Already have an account? <a href="index.php?page=myaccount">Log In</a></p>
    <?php endif; ?>

    <form action="index.php?page=checkout" method="post">

        <?php if (!isset($_SESSION['account_loggedin'])): ?>
        <h2>Create Account<?php if (strtolower(account_required) == 'false'): ?> (optional)<?php endif; ?></h2>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="john@example.com">

        <label for="password">Password</label>
        <input type="password" name="password" id="password" placeholder="Password">

        <label for="cpassword">Confirm Password</label>
        <input type="password" name="cpassword" id="cpassword" placeholder="Confirm Password">
        <?php endif; ?>

        <h2>Shipping Details</h2>

        <div class="row1">
            <label for="first_name">First Name</label>
            <input type="text" value="<?=$account['first_name']?>" name="first_name" id="first_name" placeholder="John" required>
        </div>

        <div class="row2">
            <label for="last_name">Last Name</label>
            <input type="text" value="<?=$account['last_name']?>" name="last_name" id="last_name" placeholder="Doe" required>
        </div>

        <label for="address_street">Address</label>
        <input type="text" value="<?=$account['address_street']?>" name="address_street" id="address_street" placeholder="24 High Street" required>

        <label for="address_city">City</label>
        <input type="text" value="<?=$account['address_city']?>" name="address_city" id="address_city" placeholder="New York" required>

        <div class="row1">
            <label for="address_state">State</label>
            <input type="text" value="<?=$account['address_state']?>" name="address_state" id="address_state" placeholder="NY" required>
        </div>

        <div class="row2">
            <label for="address_zip">Zip</label>
            <input type="text" value="<?=$account['address_zip']?>" name="address_zip" id="address_zip" placeholder="10001" required>
        </div>

        <label for="address_country">Country</label>
        <select name="address_country" required>
            <?php foreach($countries as $country): ?>
            <option value="<?=$country?>"<?=$country==$account['address_country']?' selected':''?>><?=$country?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="checkout">Place Order</button>



    </form>

</div>

<?=template_footer()?>
