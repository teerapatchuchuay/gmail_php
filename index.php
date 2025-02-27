<?php 
include('./database.php');
$db = new db();
session_start();

$id_user = isset($_SESSION['userid']) ? intval($_SESSION['userid']) : 0;

$cart_count = 0;
if ($id_user) {
    $cart_query = $db->select("cart", "SUM(amount) as total_items", "WHERE id_user = $id_user");
    $cart_data = $cart_query->fetch_object();
    $cart_count = $cart_data->total_items ?? 0;
}

if (isset($_POST['add_to_cart'])) {
    $id_user = intval($_SESSION['userid']);
    $id_product = intval($_POST['id_product']);
    $amount = intval($_POST['amount']);
    $check = $db->select("cart", "*", "WHERE id_user = $id_user AND id_pro = $id_product");

    if ($check->num_rows > 0) {
        $db->update("cart", ["amount" => "amount + $amount"], "id_user = $id_user AND id_pro = $id_product");
        $_SESSION['alert'] = "เพิ่มจำนวนสินค้าในตะกร้าเรียบร้อยแล้ว!";
    } else {
        $db->insert("cart", [
            "id_user" => $id_user,
            "id_pro" => $id_product,
            "amount" => $amount
        ]);
        if($db->query){
            $db->setalert("success","เพิ่มสินค้าสำเร็จ");
            return;
        }else{
            $db->setalert("error","เกิดข้อผิดพลาด");
            return;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<style>
         .text-style{
    color: white;
    background-color: black;
    text-align: center;
    border-radius: 15px;
}
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
</style>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">ร้านค้าออนไลน์</a>
            <div class="ms-auto">
                <a href="select_cart.php" class="btn btn-outline-light position-relative me-2">
                    <i class="bi bi-cart"></i> ตะกร้า
                    <?php if ($cart_count > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= $cart_count ?>
                        </span>
                    <?php endif; ?>
                </a>
                <a href="./logout.php" class="btn btn-danger">ออกจากระบบ</a>
            </div>
        </div>
    </nav>

<div class="container-fluid">
        <div style="height:80px;"></div>
        <div class="row">
            <div class="col-1"></div>
            <div class="col-10">
                <div class="card">
                    <div class="card-body">
                        <div class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner" style="object-fit:cover;height:250px;">
                                <div class="carousel-item active">
                                    <img src="./img/1.1.jpg" alt="" class="w-100">
                                </div>
                                <div class="carousel-item">
                                    <img src="./img/2.1.jpg" alt="" class="w-100">
                                </div>
                                <div class="carousel-item">
                                    <img src="./img/3.png" alt="" class="w-100">
                                </div>
                            </div>
                        </div>
                            <div class="mt-3"></div>
                            <?php $db->loadalert(); ?>
                            <div class="mt-2"></div>
                            <?php 
                            $type = $db->select("type_pro","*");
                            while($fetchtype = $type->fetch_object()){
                            ?>
                            <h2 class="text-center mb-2 text-style"><?=$fetchtype->name_typepro?></h2>
                            <div class="row row-cols-1 row-cols-md-5 row-cols-sm-1">
                                <?php 
                                if(isset($_POST['like']) && $_POST['search'] && !empty($_POST['search'])){
                                    $search = $_POST['search'];
                                    $rest = $db->select("produc","*","WHERE  type_pro = $fetchtype->id_typepro ");
                                }else{
                                    $rest = $db->select("produc","*","WHERE  type_pro = $fetchtype->id_typepro");
                                }
                                while($fetchrest = $rest->fetch_object()){
                                ?>
                                <div class="col mb-2">
                                  <a href="./showfood.php?id_rest=<?=$fetchrest->id_rest?>" class="mb-2" style="text-decoration: none;">
                                    <div class="card shadow">
                                        <img src="./img/<?=$fetchrest->img_pro?>" class="img-fluid" style="object-fit:cover;height:250px;" alt="">
                                        <div class="card-body">
                                            <h5><?=$fetchrest->name_pro?></h5>
                                            <div class="card-title">
                                                <h5><?=$fetchrest->price_pro?> บาท</h5>
                                            </div>
                                            <form action="" method="POST">
    <input type="hidden" name="id_product" value="<?=$fetchrest->id_pro?>">
    <input type="hidden" name="id_user" > <!-- ต้องใช้ session แทนค่าคงที่ -->
    <input type="number" name="amount" value="1" min="1" class="form-control mb-2">
    <button type="submit" name="add_to_cart" class="btn btn-primary w-100">เพิ่มลงตะกร้า</button>
</form>

                                        </div>
                                    </div>
                                  </a> 
                                </div>
                                <?php } ?>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-1"></div>
        </div>
    </div>
    <div style="height:80px;"></div>    
</body>
</html>