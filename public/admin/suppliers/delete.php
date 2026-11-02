<?php
require_once '/var/www/src/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/suppliers/');
    exit;
}

$supplierID = isset($_POST['id']) ? (int) $_POST['id'] : 0;
if ($supplierID <= 0) {
    header('Location: /admin/suppliers/?notice=not_found');
    exit;
}

$stmt = $conn->prepare('SELECT COUNT(*) AS ProductCount FROM products WHERE SupplierID = ?');
$stmt->bind_param('i', $supplierID);
$stmt->execute();
$productCount = (int) $stmt->get_result()->fetch_assoc()['ProductCount'];
$stmt->close();

if ($productCount > 0) {
    $conn->close();
    header('Location: /admin/suppliers/?notice=in_use');
    exit;
}

$stmt = $conn->prepare('DELETE FROM suppliers WHERE SupplierID = ?');
$stmt->bind_param('i', $supplierID);
$stmt->execute();
$deleted = $stmt->affected_rows === 1;
$stmt->close();
$conn->close();

header('Location: /admin/suppliers/?notice=' . ($deleted ? 'deleted' : 'not_found'));
exit;
