<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fertilizers Page - Ferticonnect</title>
    <style>
        :root {
            --plant-green: #2e8b57;
            --plant-green-dark: #1f593a;
            --plant-green-light: #3cb371;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #002400;
            color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
        }

        .scroll-active {
            background-color: var(--plant-green-dark);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            height: auto;
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
            background-color: transparent;
            color: white;
            border: 1px solid white;
            cursor: pointer;
            font-size: 1.8rem;
            padding: 5px 10px;
            margin-left: auto;
        }

        /* Carousel Styles */
        .carousel-container {
            width: 100%;
            overflow: hidden;
            position: relative;
            margin-top: 0;
        }

        .carousel {
            display: flex;
            transition: transform 0.8s ease-in-out;
        }

        .carousel img {
            width: 100%;
            height: 700px;
            object-fit: cover;
            min-width: 100%;
        }

        .carousel-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
            background-color: rgba(0, 0, 0, 0.6);
            padding: 100px;
            border-radius: 15px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.3);
        }

        .carousel-content p {
            font-size: 1.2rem;
            margin: 10px 0;
        }

        .carousel-content a {
            color: var(--plant-green-light);
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .carousel-content a:hover {
            color: white;
        }

        /* Card Grid Layout */
        .fertilizer-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Fixed-size Card Styling */
        .card {
            display: flex;
            flex-direction: column;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            height: 400px;
            width: 100%;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        /* Fixed-size Image Container */
        .image-placeholder {
            width: 100%;
            height: 250px;
            overflow: hidden;
            position: relative;
            margin: 0;
        }

        .image-placeholder img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform 0.3s ease;
        }

        .card:hover .image-placeholder img {
            transform: scale(1.05);
        }

        /* Card Content Styling */
        .card h3 {
            padding: 1rem;
            margin: 0;
            color: white;
            font-size: 1.25rem;
            font-weight: 600;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            line-height: 1.3;
        }

        .btn {
            display: inline-block;
            background-color: var(--plant-green);
            color: white;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            margin: auto 1rem 1rem;
            transition: background-color 0.3s ease;
            text-align: center;
            font-weight: 500;
            width: calc(100% - 2rem);
        }

        .btn:hover {
            background-color: var(--plant-green-dark);
        }

        /* Main Content Area */
        main {
            padding: 6rem 1rem 2rem 1rem;
            flex: 1;
        }

        main h2 {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2rem;
            color: white;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Enhanced Pagination Styling */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .pagination .page-item {
            list-style: none;
        }

        .pagination .page-item .page-link {
            display: inline-block;
            color: #2e8b57;
            /* var(--plant-green) */
            background-color: rgba(255, 255, 255, 0.1);
            border: 2px solid #2e8b57;
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .pagination .page-item:not(.disabled):hover .page-link {
            background-color: #3cb371;
            /* var(--plant-green-light) */
            color: white;
            border-color: #3cb371;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .pagination .page-item.active .page-link {
            background-color: #2e8b57;
            border-color: #2e8b57;
            color: white;
            transform: none;
        }

        .pagination .page-item.disabled .page-link {
            background-color: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.5);
            cursor: not-allowed;
            pointer-events: none;
        }

        .pagination .page-link:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(46, 139, 87, 0.25);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .pagination {
                gap: 6px;
            }

            .pagination .page-item .page-link {
                padding: 8px 16px;
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .pagination {
                gap: 4px;
            }

            .pagination .page-item .page-link {
                padding: 6px 12px;
                font-size: 13px;
            }
        }



        /* Footer Styling */
        footer {
            background-color: var(--plant-green-dark);
            padding: 40px 0;
            text-align: center;
            margin-top: auto;
        }

        footer h5 {
            color: #ffc107;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        footer p {
            color: #ccc;
            margin: 10px 0;
            padding: 0 20px;
            line-height: 1.6;
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

        /* Responsive Design */
        @media (max-width: 1200px) {
            .fertilizer-cards {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                padding: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            nav {
                padding: 1rem;
            }

            .logo h1 {
                font-size: 1.5rem;
            }

            .navbar-toggler {
                display: block;
            }

            .nav-links {
                position: fixed;
                background-color: rgba(0, 0, 0, 0.95);
                top: 0;
                right: -100%;
                width: 70%;
                height: 100vh;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                transition: right 0.3s ease;
            }

            .nav-links.active {
                right: 0;
            }

            .nav-links li {
                margin: 1rem 0;
            }

            .nav-links a {
                font-size: 1.5rem;
            }

            .carousel img {
                height: 500px;
            }

            .carousel-content {
                padding: 2rem;
            }

            .fertilizer-cards {
                grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
                gap: 1.5rem;
            }

            .card {
                height: 380px;
            }

            .image-placeholder {
                height: 220px;
            }

            main {
                padding: 5rem 1rem 2rem 1rem;
            }

            main h2 {
                font-size: 1.5rem;
                padding: 0 1rem;
            }
        }

        @media (max-width: 480px) {
            .fertilizer-cards {
                grid-template-columns: 1fr;
                padding: 1rem;
                gap: 1rem;
            }

            .carousel img {
                height: 400px;
            }

            .carousel-content {
                padding: 1.5rem;
                width: 90%;
            }
        }
    </style>
</head>

<body>
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

    <!-- Carousel Section -->
    <section id="home" class="carousel-container">
        <div class="carousel" id="carousel">
            <img src="fertilizer-bg.jpg" alt="Fertilizer Banner">
        </div>
        <div class="carousel-content">
            <p>Do you want to join our Platform of fertilizer traders community? <a href="register.php">Register Here</a></p>
        </div>
    </section>

    <main>
        <h2>These are the Fertilizers that offered by our best fertilizer traders</h2>
        <div class="fertilizer-cards">
            <?php
            // Define pagination variables
            $productsPerPage = 8;
            $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($currentPage - 1) * $productsPerPage;

            // Get total number of products
            $totalProductsQuery = "SELECT COUNT(*) as total FROM products";
            $totalProductsResult = $conn->query($totalProductsQuery);
            $totalProductsRow = $totalProductsResult->fetch_assoc();
            $totalProducts = $totalProductsRow['total'];

            // Calculate total pages
            $totalPages = ceil($totalProducts / $productsPerPage);

            // Fetch products for the current page
            $sql = "SELECT id, name, description, price, filePath FROM products LIMIT $productsPerPage OFFSET $offset";
            $result = $conn->query($sql);

            // Display products
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="card">';
                    echo '<div class="image-placeholder">';
                    echo '<img src="uploads/' . htmlspecialchars($row['filePath']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                    echo '</div>';
                    echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                    echo '<a href="product-details.php?id=' . htmlspecialchars($row["id"]) . '" class="btn">View More</a>';
                    echo '</div>';
                }
            } else {
                echo '<p>No products available</p>';
            }

            // Close connection
            $conn->close();
            ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>" <?php echo $currentPage <= 1 ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>Previous</a>
                            </li>

                            <?php
                            for ($i = max(1, $currentPage - 1); $i <= min($totalPages, $currentPage + 1); $i++): ?>
                                <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>" <?php echo $currentPage >= $totalPages ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        <?php endif; ?>
        </div>
        </div>
        </div>
    </main>
    <footer id="footer">
        <h5>FertiConnect</h5>
        <p>FertiConnect is your go-to platform for connecting farmers and fertilizer traders. Our mission is to empower farmers by providing easy access to high-quality fertilizers from trusted traders.</p>
        <div class="footer-links">
            <a href="index.php" class="footer-link">Home</a> |
            <a href="fertilizer.php" class="footer-link">Fertilizers</a> |
            <a href="supplier.php" class="footer-link">Traders</a> |
            <a href="#" class="footer-link">Contact Us</a>
        </div>
        <p>&copy; 2024 FertiConnect. All rights reserved.</p>
    </footer>
</body>

</html>