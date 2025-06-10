<?php

class AuthMiddleware {
    public static function check() {
        session_start();
        if (!isset($_SESSION['admin_id'])) {
            header('Location: admin_login.php');
            exit();
        }
    }
}
