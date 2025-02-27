<?php
include('./database.php');
$db = new db();
session_start();

if (!isset($_SESSION['userid'])) {
    die("กรุณาเข้าสู่ระบบก่อนทำรายการ");
}

if (isset($_POST['add_to_cart'])) {
    $id_user = intval($_SESSION['userid']);
    $id_product = intval($_POST['id_product']);
    $amount = intval($_POST['amount']);

    // ตรวจสอบว่าสินค้าอยู่ในตะกร้าแล้วหรือไม่
    $check = $db->select("cart", "*", "WHERE id_user = $id_user AND id_pro = $id_product");

    if ($check->num_rows > 0) {
        // อัปเดตจำนวนสินค้าในตะกร้า
        $db->update("cart", ["amount" => "amount + $amount"], "id_user = $id_user AND id_pro = $id_product");
        $_SESSION['alert'] = "เพิ่มจำนวนสินค้าในตะกร้าเรียบร้อยแล้ว!";
    } else {
        // เพิ่มสินค้าใหม่ลงตะกร้า
        $db->insert("cart", [
            "id_user" => $id_user,
            "id_pro" => $id_product,
            "amount" => $amount
        ]);
        $_SESSION['alert'] = "เพิ่มสินค้าในตะกร้าสำเร็จ!";
    }

    header("Location: index.php");
    exit();
}
?>
