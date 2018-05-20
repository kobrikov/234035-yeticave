<?php

require_once 'config.php';
require_once 'init.php';
require_once 'functions.php';
require_once 'db_functions.php';
require_once 'mysql_helper.php';

$is_auth = (bool) rand(0, 1);

$user_name = 'Константин';
$user_avatar = 'img/user.jpg';
$errors = [];
$add_lot = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $add_lot = $_POST;

    $required_fields = ['lot-name', 'category', 'message', 'lot-date'];
    $required_num_fields = ['lot-rate', 'lot-step'];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[$field] = 'Поле не заполнено';
        }
    }

    foreach ($required_num_fields as $num) {
        if (!(is_numeric($_POST[$num]) && $_POST[$num] > 0)) {
            $errors[$num] = 'Поле не заполнено';
        }
    }

    if (isset($_FILES['lot_image'])) {
        $file_name = uniqid() . '.jpg';
        $add_lot['path'] = 'img/' . $file_name;
        move_uploaded_file($_FILES['lot_image']['tmp_name'], $add_lot['path']);
    }

    if (empty($errors)) {
        $sql = post_lot();
        $stmt = db_get_prepare_stmt($link, $sql, [$add_lot['lot-name'], $add_lot['message'], $add_lot['path'], $add_lot['lot-rate'], $add_lot['lot-date'], $add_lot['lot-step'], 1]);
        $res = mysqli_stmt_execute($stmt);

        if ($res) {
            $add_lot_id = mysqli_insert_id($link);
            header("Location: lot.php?id=" . $add_lot_id);
        }
    }

}

if ($link) {
    $sql_category = get_categories();
    $categories = get_data($link, $sql_category);
}

$content = render_template('add', $categories, $errors, $add_lot);
$output = render_template('layout', [
    'title' => 'Главная',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'user_avatar' => $user_avatar,
    'categories' => $categories,
    'content' => $content
]);

print($output);
