<?php
// Include the database connection file
include 'db.php'; // This will use your existing db.php file for the connection

// Fetch product data by product ID (you can replace 1 with a dynamic ID from GET/POST request)
$product_id = $_GET['id']; // Assuming you have a way to pass the product ID dynamically

// SQL query to get the product and supplier details
$sql = "
    SELECT p.name AS product_name, p.description AS product_description, p.filePath AS product_image,p.price AS price,
           s.name AS supplier_name, s.address AS supplier_location, s.email AS supplier_email, s.phone AS supplier_phone
    FROM products p
    INNER JOIN suppliers s ON p.supplier_id = s.id
    WHERE p.id = ?
";

// Prepare and bind
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch the product and owner details
    $row = $result->fetch_assoc();
    
    // Store data in an array similar to your existing array structure
    $product = [
        'name' => $row['product_name'],
        'image' => $row['product_image'], // Assuming filePath is the correct image path
        'description' => $row['product_description'],
        'price'=>$row['price'],
        'owner' => [
            'full_name' => $row['supplier_name'],
            'location' => $row['supplier_location'],
            'email' => $row['supplier_email'],
            'phone' => $row['supplier_phone']
        ]
    ];
} else {
    echo "No product found.";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Fertilizer Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
         :root {
            --plant-green: #2e8b57;
            /* More natural forest green */
            --plant-green-dark: #1f593a;
            --plant-green-light: #3cb371;
        }
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: var(--plant-green-light);
        }

        .close-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: var(--plant-green-dark);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
            transition: background-color 0.3s ease;
        }

        .close-button:hover {
            background-color: #2980b9;
        }

        header {
            background-color: var(--plant-green-dark);
            color: white;
            text-align: center;
            padding: 1em;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        h1 {
            margin: 0;
            font-size: 2rem;
        }

        .product-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-wrap: wrap;
        }

        .product-image-container {
            flex: 1 1 300px;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .product-details {
            flex: 1 1 300px;
            padding: 20px;
        }

        .owner-details, .product-info-section {
            background-color: #ecf0f1;
            padding: 20px;
            margin-top: 20px;
            border-radius: 8px;
        }

        .owner-title, .product-dtls {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-top: 0;
            font-size: 1.5rem;
        }

        .owner-info, .product-info {
            margin: 10px 0;
            font-size: 0.9rem;
        }

        .owner-info span, .product-info span {
            font-weight: bold;
            color: #34495e;
        }

        a {
            color: #3498db;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .product-container {
                flex-direction: column;
            }

            .product-image-container {
                height: 200px;
            }

            .owner-title, .product-dtls {
                font-size: 1.3rem;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.5rem;
            }

            .owner-title, .product-dtls {
                font-size: 1.2rem;
            }

            .owner-info, .product-info {
                font-size: 0.85rem;
            }

            .close-button {
                display: block;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <a href="javascript:history.back()" class="close-button">Close</a>

    <header>
        <h1>Fertilizer Details</h1>
    </header>

    <div class="product-container">
        <div class="product-image-container">
        <img src="<?php echo 'uploads/' . htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
        </div>
        <div class="product-details">
            <div class="product-info-section">
                <h3 class="product-dtls">Fertilizer</h3>
                <p class="product-info"><span>Fertilizer name: </span><?php echo htmlspecialchars($product['name']); ?></p>
                <p class="product-info"><span>Fertilizer description: </span><?php echo htmlspecialchars($product['description']); ?></p>
                <p class="product-info"><span>Fertilizer price: </span><?php echo htmlspecialchars($product['price']); ?> <span>RWF per KG</span></p>
            </div>

            <div class="owner-details">
                <h3 class="owner-title">Fertilizer Owner Details</h3>
                <p class="owner-info"><span>Full Name:</span> <?php echo htmlspecialchars($product['owner']['full_name']); ?></p>
                <p class="owner-info"><span>Location:</span> <?php echo htmlspecialchars($product['owner']['location']); ?></p>
                <p class="owner-info"><span>Email:</span> <a href="mailto:<?php echo htmlspecialchars($product['owner']['email']); ?>"><?php echo htmlspecialchars($product['owner']['email']); ?></a></p>
                <p class="owner-info"><span>Phone:</span> <?php echo htmlspecialchars($product['owner']['phone']); ?></p>
            </div>
        </div>
    </div>
</body>
</html>