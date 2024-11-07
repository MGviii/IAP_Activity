<?php
include("db.php");
if (isset($_GET['supplier_id'])) {
    $supplier_id = intval($_GET['supplier_id']); // Ensure the ID is an integer

    // Fetch products for the supplier
    $productsQuery = "SELECT * FROM products WHERE supplier_id = $supplier_id";
    $productsResult = $conn->query($productsQuery);

    if ($productsResult->num_rows > 0) {
        $products = $productsResult->fetch_all(MYSQLI_ASSOC);
    } else {
        $products = [];
    }
} else {
    $products = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linking Farmers with Fertilizer Suppliers</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --plant-green: #2e8b57;
            /* More natural forest green */
            --plant-green-dark: #1f593a;
            --plant-green-light: #3cb371;
        }

        body, html {
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .content {
            flex: 1;
            padding: 20px;
        }

        body {
            /*background-color: #002500;*/
            color: #fff;
            font-family: Arial, sans-serif;
        }

        /* Transparent Navbar Styles */
        header {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 10;
            padding: 15px 0;
            transition: background-color 0.3s ease;
            background-color: transparent;
            /* No background initially */
        }

        /* Background color on scroll */
        .scroll-active {
            background-color: var(--plant-green-dark);
            /* Set the background color when scrolling */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            height: 4px;
        }

        .logo h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
        }

        .nav-links {
            display: flex;
            list-style: none;
            margin: 0;
        }

        .nav-links li {
            margin-left: 30px;
            position: relative;
        }

        .nav-links a {
            color: #fff;
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 500;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        /* Hover effect */
        .nav-links a::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0%;
            height: 3px;
            background-color: #4CAF50;
            transition: all 0.3s ease;
        }

        .nav-links a:hover::before {
            width: 100%;
        }

        .nav-links a:hover {
            color: #4CAF50;
        }

        /* Mobile Menu Toggle Button */
        .navbar-toggler {
            display: none;
        }

        @media (max-width: 768px) {
            .nav-links {
                position: fixed;
                background-color: rgba(0, 0, 0, 0.95);
                top: 0;
                right: 0;
                width: 70%;
                height: 100vh;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                display: none;
                transition: all 0.3s ease;
            }

            .nav-links.active {
                display: flex;
            }

            .nav-links li {
                margin: 20px 0;
            }

            .navbar-toggler {
                display: block;
                background-color: white;
                color: #28a745;
                border: none;
                cursor: pointer;
                font-size: 1.8rem;
                padding: 10px;
                margin-left: auto;
            }

            .nav-links a {
                font-size: 1.5rem;
            }
        }

        /* Smooth Scroll for anchors */
        html {
            scroll-behavior: smooth;
        }

       
       /* Product Section Styles */
       .products-section {
            padding: 50px 20px;
            text-align: center;
            background-color: var(--plant-green-light);
            box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.3);
            height: 100vh;

        }

        .products-section h2 {
            font-size: 2.5rem;
            margin-bottom: 30px;
            color: #fff;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .product-item {
            background-color: #fff;
            color: #000;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            height: 300px;
            /* Set a fixed height for all product items */
            display: flex;
            flex-direction: column;
            /* Align content vertically */
            justify-content: space-between;
            /* Space between image and title/button */
        }

        .product-item img {
            width: 100%;
            height: 150px;
            /* Set a consistent height */
            object-fit: cover;
            /* Ensures the image covers the area without stretching */
            border-radius: 8px;
            margin-bottom: 10px;
            /* Space between image and title */
        }

        .product-item h3 {
            margin: 15px 0 10px;
            font-size: 1.3rem;
            color: #28a745;
        }

        /* Hover effect */
        .product-item:hover {
            transform: scale(1.05);
            /* Slightly enlarge on hover */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            /* Enhance shadow on hover */
        }
        /* Navigation Buttons */
        .prev,
        .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            width: auto;
            padding: 16px;
            margin-top: -22px;
            color: white;
            font-weight: bold;
            font-size: 18px;
            transition: 0.6s ease;
            border-radius: 0 3px 3px 0;
            user-select: none;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .next {
            right: 0;
            border-radius: 3px 0 0 3px;
        }

        .prev:hover,
        .next:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }

        .btn {
            padding: 12px 30px;
            background-color: #28a745;
            /* Green background */
            color: white;
            /* White text */
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.3);
            transition: background-color 0.3s ease, transform 0.3s ease;
            text-decoration: none;
            /* Remove underline */
            display: inline-block;
            /* Align properly */
        }

        .btn:hover {
            background-color: #218838;
            /* Darker green on hover */
            transform: scale(1.05);
            /* Slightly enlarge on hover */
        }


        footer {
            font-family: 'Poppins', sans-serif;
            color: #f1f1f1;
            background-color: var(--plant-green-dark);
            padding: 20px 0;
            text-align: center;
            position: absolute;
            bottom: 0;
            width: 100%;
        }


        footer h5 {
            color: #ffc107;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        footer p {
            color: #ccc;
        }

        .footer-links {
            margin: 15px 0;
        }

        .footer-link {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            margin: 0 10px;
            transition: color 0.3s ease;
        }

        .footer-link:hover {
            color: #1a1a1a;
        }
    </style>
</head>

<body>
    <div class="homepage-content">
        <!-- Navigation Bar -->
        <header id="header">
            <nav>
                <div class="logo">
                    <h1>FertiConnect</h1>
                </div>
                <button class="navbar-toggler" onclick="toggleMenu()">☰</button>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="supplier.php">Traders</a></li>
                    <li><a href="fertilizer.php">Fertilizers</a></li>
                    <li><a href="supplierdashboard/products.php">Trader Dashboard</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                </ul>
            </nav>
        </header>

        <!-- Products Section -->
        <section class="products-section">
            <h2>The Fertilizers</h2>
            <div class="products-grid">
                <?php
                if (!empty($products)) {
                    foreach ($products as $row) {
                        $productName = htmlspecialchars($row["name"]);
                        $productImagePath = htmlspecialchars($row["filePath"]);

                        if (strpos($productImagePath, 'uploads/') === false) {
                            $productImagePath = 'uploads/' . $productImagePath;
                        }

                        echo '<div class="product-item">';
                        echo '<img src="' . $productImagePath . '" alt="Product Image">';
                        echo '<h3>' . $productName . '</h3>';
                        echo '<a href="product-details.php?id=' . htmlspecialchars($row["id"]) . '" class="btn">View More</a>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No products found for this supplier.</p>';
                }
                ?>
            </div>
        </section>

        <footer id="footer">
            <h5>FertiConnect</h5>
            <p>FertiConnect is your go-to platform for connecting farmers and fertilizer traders. Our mission is to empower farmers by providing easy access to high-quality fertilizers from trusted traders.</p>
            <div class="footer-links">
                <a href="#" class="footer-link">Home</a> |
                <a href="#" class="footer-link">Products</a> |
                <a href="#" class="footer-link">Traders</a> |
                <a href="#" class="footer-link">Contact Us</a>
            </div>
            <p>&copy; 2024 FertiConnect. All rights reserved.</p>
        </footer>
    </div>

    <script>
        // Toggle menu for mobile view
        function toggleMenu() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
        }

        // Change header style on scroll
        window.addEventListener('scroll', function() {
            const header = document.getElementById('header');
            if (window.scrollY > 50) {
                header.classList.add('scroll-active');
            } else {
                header.classList.remove('scroll-active');
            }
        });
    </script>
</body>

</html>