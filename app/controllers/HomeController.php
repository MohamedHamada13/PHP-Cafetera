<?php

require_once __DIR__ . "/../models/Product.php";
require_once __DIR__ . "/../models/Room.php";
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/OrderController.php";
// require_once __DIR__ . "/../../routes/web.php";

class HomeController
{
    public function index()
    {

        $db = new Database();
        $conn = $db->connect();
        $productModel = new Product($conn);
        $roomModel    = new Room($conn);
        $userModel    = new User($conn);
        $orderController = new OrderController($conn);

        $isAdmin = (($_SESSION['user_role'] ?? '') === 'admin');
        $currentUser = null;
        $selectedOrderUserId = null;
        $availableUsers = [];

        $sessionUserId = (int)($_SESSION['user_id'] ?? 0);
        if ($sessionUserId > 0) {
            $currentUser = $userModel->getById($sessionUserId);
        }

        if ($isAdmin) {
            foreach ($userModel->getAll() as $user) {
                if (($user['role'] ?? '') === 'user') {
                    $availableUsers[] = $user;
                }
            }

            $requestedOrderUserId = (int)($_GET['order_user_id'] ?? ($_POST['order_user_id'] ?? 0));
            $allowedUserIds = array_map(static fn($user) => (int)$user['id'], $availableUsers);

            if ($requestedOrderUserId > 0 && in_array($requestedOrderUserId, $allowedUserIds, true)) {
                $selectedOrderUserId = $requestedOrderUserId;
            } elseif (!empty($_SESSION['order_for_user_id']) && in_array((int)$_SESSION['order_for_user_id'], $allowedUserIds, true)) {
                $selectedOrderUserId = (int)$_SESSION['order_for_user_id'];
            } elseif (!empty($availableUsers)) {
                $selectedOrderUserId = (int)$availableUsers[0]['id'];
            }

            if ($selectedOrderUserId !== null) {
                $_SESSION['order_for_user_id'] = $selectedOrderUserId;
            }
        } else {
            $selectedOrderUserId = $sessionUserId > 0 ? $sessionUserId : null;
            unset($_SESSION['order_for_user_id']);
        }

        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_GET['add'])) {
            $orderController->add((int) $_GET['add']);
            header("Location: index.php");
            exit();
        }

        if (isset($_GET['plus'])) {
            $orderController->increase((int) $_GET['plus']);
            header("Location: index.php");
            exit();
        }

        if (isset($_GET['minus'])) {
            $orderController->decrease((int) $_GET['minus']);
            header("Location: index.php");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {

            $roomId = !empty($_POST['room_id']) ? (int) $_POST['room_id'] : null;
            $notes  = trim($_POST['notes'] ?? '');
            $orderForUserId = !empty($_POST['order_user_id']) ? (int)$_POST['order_user_id'] : null;

            $orderController->confirmFor($roomId, $notes, $orderForUserId);

            header("Location: index.php");
            exit();
        }

        $products = $productModel->getAll();
        $rooms    = $roomModel->getAll();

        $cartItems  = [];
        $totalPrice = 0;

        foreach ($_SESSION['cart'] as $productId => $quantity) {

            $product = $productModel->getById($productId);

            if (!$product) {
                continue;
            }

            $lineTotal  = $product['price'] * $quantity;
            $totalPrice += $lineTotal;

            $cartItems[] = [
                'product'    => $product,
                'quantity'   => $quantity,
                'line_total' => $lineTotal
            ];
        }

        $latestOrder = $orderController->getLatestOrder($selectedOrderUserId);

        require __DIR__ . "/../views/user/home.php";
    }
}
