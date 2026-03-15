<?php

class OrderController
{
    private PDO $conn;
    private Product $productModel;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
        $this->productModel = new Product($conn);
    }

    public function add(int $id)
    {
        $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
    }

    public function increase(int $id)
    {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]++;
        }
    }

    public function decrease(int $id)
    {
        if (!isset($_SESSION['cart'][$id])) {
            return;
        }

        if ($_SESSION['cart'][$id] > 1) {
            $_SESSION['cart'][$id]--;
        } else {
            unset($_SESSION['cart'][$id]);
        }
    }

    public function confirm(?int $roomId, string $notes)
    {
        if (empty($_SESSION['cart'])) {
            return null;
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return null;
        }

        $this->conn->beginTransaction();

        try {
            $totalPrice = 0;
            foreach ($_SESSION['cart'] as $productId => $qty) {
                $product = $this->productModel->getById($productId);
                if (!$product) {
                    continue;
                }
                $totalPrice += $product['price'] * $qty;
            }

            $orderStmt = $this->conn->prepare("
                INSERT INTO orders (user_id, room_id, notes, total_price, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");

            $orderStmt->execute([
                $userId,
                $roomId,
                $notes,
                $totalPrice
            ]);

            $orderId = $this->conn->lastInsertId();

            $itemStmt = $this->conn->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ");

            foreach ($_SESSION['cart'] as $productId => $qty) {

                $product = $this->productModel->getById($productId);

                if ($product) {
                    $itemStmt->execute([
                        $orderId,
                        $productId,
                        $qty,
                        $product['price']
                    ]);
                }
            }

            $this->conn->commit();

            $_SESSION['cart'] = [];

            return $orderId;
        } catch (Exception $e) {

            $this->conn->rollBack();
            throw $e;
        }
    }

    public function getLatestOrder()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return null;
        }

        $stmt = $this->conn->prepare("
                SELECT *
                FROM orders_with_rooms
                WHERE user_id = ?
                ORDER BY created_at DESC
                LIMIT 1
                ");

        $stmt->execute([$userId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return null;
        }

        $itemsStmt = $this->conn->prepare("
                    SELECT *
                    FROM order_items_with_products
                    WHERE order_id = ?
                    ");


        $itemsStmt->execute([$order['id']]);

        return [
            'order' => $order,
            'items' => $itemsStmt->fetchAll(PDO::FETCH_ASSOC)
        ];
    }

    public function getByUserId(int $userId)
    {
        $stmt = $this->conn->prepare("
                SELECT *
                FROM orders_with_rooms
                WHERE user_id = ?
                ORDER BY created_at DESC
                ");

        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $orderId)
    {
        $stmt = $this->conn->prepare("
                        SELECT *
                        FROM orders_with_room_and_user
                        WHERE id = ?
                        ");

        $stmt->execute([$orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getItems(int $orderId)
    {
        $stmt = $this->conn->prepare("
            SELECT oi.*, p.name as product_name, p.image as product_image
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cancel(int $orderId, int $userId)
    {
        $stmt = $this->conn->prepare("
            UPDATE orders 
            SET status = 'cancelled' 
            WHERE id = ? AND user_id = ? AND status = 'processing'
        ");
        $stmt->execute([$orderId, $userId]);
        return $stmt->rowCount() > 0;
    }
}
