<?php
include('./database.php');
$db = new db();
session_start();

if (!isset($_SESSION['userid'])) {
    die("กรุณาเข้าสู่ระบบก่อนดูตะกร้าสินค้า");
}

$id_user = intval($_SESSION['userid']);
$cart_items = $db->select("cart INNER JOIN produc ON cart.id_pro = produc.id_pro", "cart.*, produc.name_pro, produc.price_pro", "WHERE cart.id_user = $id_user");
$total_items = $cart_items->num_rows;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'order') {
            $fullname = $_POST['fullname'];
            $address = $_POST['address'];
            $phone = $_POST['phone'];
            $email = $_POST['email'];
            $order_date = date('Y-m-d H:i:s');

            while ($item = $cart_items->fetch_object()) {
                $db->insert("orderd", [
                    'id_user' => $id_user,
                    'id_pro' => $item->id_pro,
                    'amount' => $item->amount,
                    'fullname' => $fullname,
                    'address' => $address,
                    'phone' => $phone,
                    'email' => $email,
                    'order_date' => $order_date
                ]);
            }

           
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'teerapatchuchuay4240@gmail.com'; 
                $mail->Password = 'xbbx hnpg iioj vzij'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('from_email@example.com', 'Your Store');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Product Bagista';
                $mail->Body    = "<h2>รายละเอียดการจอง</h2>
                                  <p><strong>ชื่อ-นามสกุล:</strong> $fullname</p>
                                  <p><strong>ที่อยู่:</strong> $address</p>
                                  <p><strong>เบอร์โทร:</strong> $phone</p>
                                  <p><strong>อีเมล:</strong> $email</p>
                                  <p><strong>วันที่จอง:</strong> $order_date</p>
                                  <h3>รายการสินค้า:</h3>
                                  <ul>";
                
                $cart_items = $db->select("cart,produc","*","WHERE cart.id_pro = produc.id_pro AND id_user = $id_user");
                $total_price = 0;
                while ($item = $cart_items->fetch_object()) {
                    $subtotal = $item->price_pro * $item->amount;
                    $total_price += $subtotal;
                    $mail->Body .= "<li>" . htmlspecialchars($item->name_pro) . " - จำนวน: " . $item->amount . " - ราคา: " . number_format($subtotal, 2) . " บาท</li>";
                }
                
                $mail->Body .= "</ul><p><strong>รวมทั้งหมด: </strong>" . number_format($total_price, 2) . " บาท</p>";
                
                $mail->send();
                echo 'Message has been sent';
                var_dump($cart_items); 
                echo "Total Price: " . number_format($total_price, 2); 

            } catch (Exception $e) {
                var_dump($cart_items); 
                echo "Total Price: " . number_format($total_price, 2); 
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
            $db->delete("cart", "id_user = $id_user");
        }
        

        if ($_POST['action'] === 'update') {
            $id_pro = intval($_POST['id_pro']);
            $amount = intval($_POST['amount']);
            if ($amount > 0) {
                $db->update("cart", ['amount' => $amount], "id_pro = $id_pro AND id_user = $id_user");
                exit;
            }
        }

        if ($_POST['action'] === 'delete') {
            $id_pro = intval($_POST['id_pro']);
            $db->delete("cart", "id_pro = $id_pro AND id_user = $id_user");
            exit;
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    var_dump($_POST); 
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id_pro = intval($_POST['id_pro']);
        $amount = intval($_POST['amount']);
        if ($amount > 0) {
            $date = [
                'amount' => $amount
            ];
            $db->update("cart", $date, "id_pro = $id_pro AND id_user = $id_user");
        }
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id_pro = intval($_POST['id_pro']);
        $db->delete("cart","id_pro = $id_pro AND id_user = $id_user");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตะกร้าสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- เพิ่ม jQuery -->

    <style>
          body {
            background-color: #f8f9fa;
            background-image:url('./img/1.jpg');
            background-size: cover;  
            background-position: center; 
            background-repeat: no-repeat; 
            height: 100vh; 
            margin: 0;
            backdrop-filter: blur(10px);    
        }
        .cart-icon {
            position: relative;
            font-size: 24px;
            color: #000;
        }
        .cart-badge {
            position: absolute;
            top: -5px;
            right: -10px;
            background: red;
            color: white;
            font-size: 12px;
            padding: 3px 7px;
            border-radius: 50%;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-light bg-light">
        <div class="container">
            <a href="index.php" class="navbar-brand">ร้านค้า</a>
            <a href="select_cart.php" class="cart-icon">
                <i class="bi bi-cart"></i>
                <?php if ($total_items > 0): ?>
                    <span class="cart-badge"><?= $total_items ?></span>
                <?php endif; ?>
            </a>
        </div>
    </nav>
    <div style="height:80px;"></div>
    <div class="container mt-4">
        <h2 style="color: white;" class="mb-4"><i class="bi bi-cart " style="color: white;"></i > ตะกร้าสินค้าของคุณ</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>สินค้า</th>
                        <th>ราคา</th>
                        <th>จำนวน</th>
                        <th>รวม</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_price = 0;
                    while ($item = $cart_items->fetch_object()): 
                        $subtotal = $item->price_pro * $item->amount;
                        $total_price += $subtotal;
                    ?>
                    <tr id="row-<?= $item->id_pro ?>">
                        <td><?= htmlspecialchars($item->name_pro) ?></td>
                        <td><?= number_format($item->price_pro, 2) ?> บาท</td>
                        <td>
                            <input type="number" value="<?= $item->amount ?>" min="1" class="form-control form-control-sm w-50 update-qty" data-id="<?= $item->id_pro ?>">
                        </td>
                        <td class="subtotal"><?= number_format($subtotal, 2) ?> บาท</td>
                        <td>
                            <button class="btn btn-danger btn-sm delete-item" data-id="<?= $item->id_pro ?>">
                                <i class="bi bi-trash"></i> ลบ
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3"><strong>รวมทั้งหมด:</strong></td>
                        <td colspan="2"><strong id="total-price"><?= number_format($total_price, 2) ?> บาท</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="d-flex justify-content-between">
            <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> ซื้อสินค้าต่อ</a>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#orderModal">
    <i class="bi bi-credit-card"></i> จอง
</button>

        </div>
    </div>

    <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">กรอกข้อมูลการจอง</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="orderForm">
                        <input type="hidden" name="action" value="order">
                        <div class="mb-3">
                            <label>ชื่อ-นามสกุล</label>
                            <input type="text" name="fullname" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>ที่อยู่</label>
                            <textarea name="address" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label>อีเมล</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>เบอร์โทร</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">ยืนยัน</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<script>
$(document).ready(function() {
    $(".update-qty").on("change", function() {
        var id_pro = $(this).data("id");
        var amount = $(this).val();
        if (amount < 1) {
            amount = 1;
            $(this).val(1);
        }

        $.post("select_cart.php", { action: "update", id_pro: id_pro, amount: amount }, function() {
            location.reload(); 
        });
    });

    $("#orderForm").on("submit", function(e) {
    e.preventDefault();

    $.post("select_cart.php", $(this).serialize(), function(response) {
        console.log(response);  
        if (response === "success") {
            alert("จองสำเร็จ!");
            location.reload();  
        } else {
            alert("จองสำเร็จ กรุณาตรวจสอบข้อมูลในอีเมล");
            location.reload();  
        }
    }).fail(function() {
        alert("ไม่สามารถส่งข้อมูลได้ กรุณาลองใหม่.");
    });
});


    $(".delete-item").on("click", function() {
        var id_pro = $(this).data("id");
        if (confirm("คุณต้องการลบสินค้านี้ออกจากตะกร้าหรือไม่?")) {
            $.post("select_cart.php", { action: "delete", id_pro: id_pro }, function() {
                $("#row-" + id_pro).fadeOut("fast", function() {
                    $(this).remove();
                    location.reload();
                });
            });
        }
    });
});
</script>
