<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cafeteria</title>
    <?php include_once __DIR__ . "/../layouts/jsCDN.php"; ?>
</head>

<body>
    <?php
        $isAdmin = (($_SESSION['user_role'] ?? '') === 'admin');
        $targetUserId = (int)($selectedOrderUserId ?? 0);
        $selectedUser = null;
        $selectedUserRoomId = null;

        if ($isAdmin && !empty($availableUsers)) {
            foreach ($availableUsers as $candidateUser) {
                if ((int)$candidateUser['id'] === $targetUserId) {
                    $selectedUser = $candidateUser;
                    break;
                }
            }
        } elseif (!$isAdmin && !empty($currentUser)) {
            $selectedUser = $currentUser;
        }

        if (!empty($selectedUser['room_id'])) {
            $selectedUserRoomId = (int)$selectedUser['room_id'];
        }

        $orderUserQuery = ($isAdmin && $targetUserId > 0)
            ? ('&order_user_id=' . $targetUserId)
            : '';
    ?>
    <?php include __DIR__ . "/../layouts/navbar.php"; ?>
    <div class="container py-4">
        <div class="mb-4">
            <?php if ($isAdmin): ?>
                <h2 class="mb-1">Cashier Order Panel</h2>
                <p class="text-muted mb-0">Create orders on behalf of cafeteria users.</p>
            <?php else: ?>
                <h2 class="mb-1">Welcome, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></h2>
                <p class="text-muted mb-0">Build your order and confirm it for your room.</p>
            <?php endif; ?>
        </div>

        <?php if (!empty($latestOrder)): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            Latest Order
                            <?php if ($isAdmin && !empty($selectedUser)): ?>
                                for <?= htmlspecialchars($selectedUser['name']) ?>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <?php $order = $latestOrder['order']; ?>
                            <div class="d-flex justify-content-between mb-2">
                                <div>
                                    <strong>Order #<?= htmlspecialchars($order['id']) ?></strong>
                                    <?php if (!empty($order['room_name'])): ?>
                                        <span class="text-muted"> | Room: <?= htmlspecialchars($order['room_name']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <strong>Total:</strong>
                                    <?= number_format((float) $order['total_price'], 2) ?>
                                </div>
                            </div>
                            <?php if (!empty($order['notes'])): ?>
                                <p class="mb-2"><strong>Notes:</strong> <?= nl2br(htmlspecialchars($order['notes'])) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($latestOrder['items'])): ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($latestOrder['items'] as $item): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><?= htmlspecialchars($item['product_name']) ?></span>
                                            <span class="badge bg-secondary">
                                                x<?= (int) $item['quantity'] ?>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8 mb-4">
                <h2 class="mb-3">Products</h2>
                <div class="row g-3">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <a href="/cart/add?id=<?= (int) $product['id'] ?><?= htmlspecialchars($orderUserQuery) ?>" class="text-decoration-none">
                                    <?php if (!empty($product['image'])): ?>
                                        <?php
                                            $imageFile = (string) $product['image'];
                                            if ($imageFile !== '' && filter_var($imageFile, FILTER_VALIDATE_URL)) {
                                                $imageSrc = $imageFile;
                                            } else {
                                                $productImageFs = __DIR__ . '/../../../public/assets/images/products/' . $imageFile;
                                                $legacyImageFs = __DIR__ . '/../../../public/assets/images/' . $imageFile;
                                                if (file_exists($productImageFs)) {
                                                    $imageSrc = '/assets/images/products/' . rawurlencode($imageFile);
                                                } elseif (file_exists($legacyImageFs)) {
                                                    $imageSrc = '/assets/images/' . rawurlencode($imageFile);
                                                } else {
                                                    $imageSrc = '';
                                                }
                                            }
                                        ?>
                                        <?php if ($imageSrc !== ''): ?>
                                            <img src="<?= htmlspecialchars($imageSrc) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                                        <?php else: ?>
                                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                                <span class="text-muted">No Image</span>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                            <span class="text-muted">No Image</span>
                                        </div>
                                    <?php endif; ?>
                                </a>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                    <p class="card-text mb-0">
                                        <strong>Price:</strong>
                                        <?= number_format((float) $product['price'], 2) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        Cart
                    </div>
                    <div class="card-body">
                        <?php if (!empty($cartItems)): ?>
                            <ul class="list-group mb-3">
                                <?php foreach ($cartItems as $item): ?>
                                    <?php $product = $item['product']; ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-semibold"><?= htmlspecialchars($product['name']) ?></div>
                                            <div class="small text-muted">
                                                <?= number_format((float) $product['price'], 2) ?> each
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <a href="/cart/minus?id=<?= (int) $product['id'] ?><?= htmlspecialchars($orderUserQuery) ?>" class="btn btn-sm btn-outline-secondary me-1">-</a>
                                             <span class="mx-1"><?= (int) $item['quantity'] ?></span>
                                            <a href="/cart/plus?id=<?= (int) $product['id'] ?><?= htmlspecialchars($orderUserQuery) ?>" class="btn btn-sm btn-outline-secondary ms-1">+</a>
                                        </div>
                                        <div class="ms-3 text-end">
                                            <small class="text-muted">Total</small>
                                            <div><?= number_format((float) $item['line_total'], 2) ?></div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total Price</strong>
                                <strong><?= number_format((float) $totalPrice, 2) ?></strong>
                            </div>
                        <?php else: ?>
                            <p class="mb-0 text-muted">Cart is empty.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        Order Details
                    </div>
                    <div class="card-body">
                         <form method="post" action="/order/confirm">
                            <?php if ($isAdmin): ?>
                                <div class="mb-3">
                                    <label for="order_user_id" class="form-label">Order For User</label>
                                    <select
                                        name="order_user_id"
                                        id="order_user_id"
                                        class="form-select"
                                        onchange="window.location.href='/?order_user_id=' + this.value"
                                        required>
                                        <option value="">Select a user</option>
                                        <?php foreach ($availableUsers as $availableUser): ?>
                                            <option value="<?= (int)$availableUser['id'] ?>" <?= ((int)$availableUser['id'] === $targetUserId) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($availableUser['name']) ?> (<?= htmlspecialchars($availableUser['email']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="room_id" class="form-label">Room</label>
                                    <select name="room_id" id="room_id" class="form-select" required>
                                        <option value="">Select a room</option>
                                        <?php foreach ($rooms as $room): ?>
                                            <?php $roomId = (int)$room['id']; ?>
                                            <option value="<?= $roomId ?>" <?= ($selectedUserRoomId === $roomId) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($room['name'] ?? $room['room_name'] ?? ('Room #' . $roomId)) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php else: ?>
                                <?php if (!empty($currentUser['room_id'])): ?>
                                    <input type="hidden" name="room_id" value="<?= (int)$currentUser['room_id'] ?>">
                                <?php endif; ?>
                                <div class="mb-3">
                                    <label class="form-label">Order For</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($currentUser['name'] ?? ($_SESSION['user_name'] ?? 'Current user')) ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Room</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        value="<?php
                                            $roomLabel = 'Not assigned';
                                            if (!empty($rooms) && !empty($currentUser['room_id'])) {
                                                foreach ($rooms as $room) {
                                                    if ((int)$room['id'] === (int)$currentUser['room_id']) {
                                                        $roomLabel = $room['name'] ?? $room['room_name'] ?? ('Room #' . $room['id']);
                                                        break;
                                                    }
                                                }
                                            }
                                            echo htmlspecialchars($roomLabel);
                                        ?>"
                                        readonly>
                                </div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="Any special instructions?"></textarea>
                            </div>
                            <button type="submit" name="confirm_order" class="btn btn-primary w-100" <?= empty($cartItems) ? 'disabled' : '' ?>>
                                Confirm Order
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/jquery-3.7.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.js"></script>
</body>

</html>