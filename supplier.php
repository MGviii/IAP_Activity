<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traders Page</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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

        body {
            background-color: #002500;
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

        html {
            scroll-behavior: smooth;
        }

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

        .content {
            text-align: center;
            margin: 20px;
            flex: 1;
            padding: 20px;
        }

        .supplier-card {
            background-color: #f5f5f5;
            color: #333;
            border: none;
            margin: 10px;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .btn-view-products {
            background-color: #00b300;
            color: white;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
        }

        /* Enhanced Pagination Styling */
        .pagination {
            display: flex;
            justify-content: center;
            margin: 40px 0;
            gap: 5px;
            margin-left: 400px;
        }

        .pagination .page-item .page-link {
            color: var(--plant-green);
            background-color: white;
            border: 2px solid var(--plant-green);
            padding: 8px 16px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .pagination .page-item:not(.disabled):hover .page-link {
            background-color: var(--plant-green-light);
            color: white;
            border-color: var(--plant-green-light);
            transform: translateY(-2px);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--plant-green);
            border-color: var(--plant-green);
            color: white;
        }

        .pagination .page-item.disabled .page-link {
            background-color: #f5f5f5;
            border-color: #ddd;
            color: #999;
        }

        .pagination .page-link:focus {
            box-shadow: 0 0 0 0.2rem rgba(46, 139, 87, 0.25);
            outline: none;
        }

        @media (max-width: 576px) {
            .pagination .page-link {
                padding: 6px 12px;
                font-size: 14px;
            }
            
            .pagination {
                gap: 3px;
            }
        }

        /* Footer Styling */
        footer {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #f1f1f1;
            background-color: var(--plant-green-dark);
            padding: 40px 0;
            text-align: center;
            position: sticky;
        }

        footer h5 {
            color: #ffc107;
            font-size: 20px;
            font-weight: bold;
            margin: 0;
            margin-bottom: 10px;
        }

        footer p {
            color: #ccc;
            margin: 10px 0;
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

        @media (max-width: 768px) {
            .footer-flex {
                flex-direction: column;
            }

            .footer-section {
                margin-bottom: 30px;
                text-align: center;
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
            <p>Do you want to join our Platform of fertilizer traders community? <a href="#">Register Here</a></p>
        </div>
    </section>

    <!-- Content -->
    <div class="content">
        <h5>These are the Fertilizer traders that offer the best fertilizer for different crops</h5>
        <div class="container">
            <div class="row">
                <?php
                include 'db.php';

                $suppliersPerPage = 8;
                $totalSuppliersQuery = "SELECT COUNT(*) AS total FROM suppliers";
                $totalSuppliersResult = $conn->query($totalSuppliersQuery);
                $totalSuppliersRow = $totalSuppliersResult->fetch_assoc();
                $totalSuppliers = $totalSuppliersRow['total'];

                $totalPages = ceil($totalSuppliers / $suppliersPerPage);
                $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $currentPage = max(1, min($currentPage, $totalPages));
                $startIndex = ($currentPage - 1) * $suppliersPerPage;

                $suppliersQuery = "SELECT id, name, description FROM suppliers LIMIT $startIndex, $suppliersPerPage";
                $suppliersResult = $conn->query($suppliersQuery);

                if ($suppliersResult->num_rows > 0) {
                    while ($row = $suppliersResult->fetch_assoc()) {
                        echo '<div class="col-sm-6 col-md-3">';
                        echo '<div class="supplier-card">';
                        echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                        echo '<p>' . htmlspecialchars($row['description']) . '</p>';
                        echo '<a href="view_details.php?supplier_id=' . $row['id'] . '" class="btn-view-products">View Our Products</a>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="col-12 text-center"><p>No suppliers found.</p></div>';
                }
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

    <footer id="footer">
        <h5>FertiConnect</h5>
        <p>FertiConnect is your go-to platform for connecting farmers and fertilizer traders. Our mission is to empower farmers by providing easy access to high-quality fertilizers from trusted traders.</p>
        <div class="footer-links">
            <a href="index.php" class="footer-link">Home</a> |
            <a href="fertilizer.php" class="footer-link">Products</a> |
            <a href="supplier.php" class="footer-link">Traders</a> |
            <a href="#" class="footer-link">Contact Us</a>
        </div>
        <p>&copy; 2024 FertiConnect. All rights reserved.</p>
    </footer>

    <script>
        // Add scroll event listener for navbar background
        window.addEventListener('scroll', function() {
            const header = document.getElementById('header');
            if (window.scrollY > 50) {
                header.classList.add('scroll-active');
            } else {
                header.classList.remove('scroll-active');
            }
        });

        // Mobile menu toggle
        function toggleMenu() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
        }
    </script>
</body>
</html>