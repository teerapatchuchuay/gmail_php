<?php
// เริ่มต้น session
session_start();

// ลบข้อมูลใน session ทั้งหมด
session_unset();

// ลบ session
session_destroy();

header("Location: ./login.php");
exit();
?>
