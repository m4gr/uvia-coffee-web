<?php
// product.php - API endpoint for product details
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in output

try {
    require_once 'config/database.php';
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit();
}

$productId = (int)$_GET['id'];

try {
    // Fetch product
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit();
    }
    
    // Fetch tags
    $stmt = $pdo->prepare("SELECT * FROM product_tags WHERE product_id = ?");
    $stmt->execute([$productId]);
    $tags = $stmt->fetchAll();
    
    // Prepare response
    $response = [
        'success' => true,
        'product' => [
            'id' => $product['id'],
            'name' => $product['name'],
            'name_ar' => $product['name_ar'],
            'description' => $product['description'],
            'description_ar' => $product['description_ar'],
            'price' => (float)$product['price'],
            'calories' => (int)$product['calories'],
            'image' => $product['image'],
            'origin' => $product['origin'],
            'origin_ar' => $product['origin_ar'],
            'process_method' => $product['process_method'],
            'process_method_ar' => $product['process_method_ar'],
            'roast_level' => $product['roast_level'],
            'roast_level_ar' => $product['roast_level_ar'],
            'caffeine_level' => $product['caffeine_level'],
            'caffeine_level_ar' => $product['caffeine_level_ar'],
            'flavor_notes' => $product['flavor_notes'],
            'flavor_notes_ar' => $product['flavor_notes_ar'],
            'ingredients' => $product['ingredients'],
            'ingredients_ar' => $product['ingredients_ar'],
            'is_available' => (bool)$product['is_available'],
            'is_featured' => (bool)$product['is_featured'],
            'tags' => $tags
        ]
    ];
    
    echo json_encode($response);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>