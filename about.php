<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fertilizer Connect</title>
    <style>
        /* General Styling */
        :root {
            --plant-green: #2e8b57;
            --plant-green-dark: #1f593a;
            --plant-green-light: #3cb371;
        }

        body {
            font-family: 'Arial', sans-serif;
            color: #fff;
            background: var(--plant-green-light);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header and Navbar */
        header {
            background-color: var(--plant-green-dark);
            padding: 1rem 0;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
        }

        .navbar {
            max-width: 1100px;
            margin: auto;
            padding: 0 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #fff;
        }

        .navbar h1 {
            font-size: 1.8rem;
            font-weight: bold;
            margin: 0;
            color: #fff;
        }

        .navbar ul {
            list-style: none;
            display: flex;
            gap: 1.5rem;
        }

        .navbar ul li {
            transition: color 0.3s ease;
        }

        .navbar ul li a {
            text-decoration: none;
            color: #fff;
            font-size: 1rem;
            font-weight: 500;
        }

        .navbar ul li a:hover {
            color: #d1d1d1;
        }

        .navbar-toggler {
            display: none;
            font-size: 1.5rem;
            background: transparent;
            border: none;
            color: #fff;
            cursor: pointer;
        }

        /* Section Styling */
        main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem 1rem;
        }

        section {
            width: 100%;
            max-width: 800px;
            padding: 2rem;
            margin-bottom: 2rem;
            background-color: var(--plant-green-dark);
            border-radius: 8px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
        }


        p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #fff;
        }

        /* Footer Styling */
        footer {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #f1f1f1;
            background-color: var(--plant-green-dark);
            padding: 40px 0;
            text-align: center;
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar ul {
                display: none;
                flex-direction: column;
                background-color: #198754;
                width: 100%;
                position: absolute;
                top: 100%;
                left: 0;
                padding: 1rem 0;
                box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
            }

            .navbar ul.show {
                display: flex;
            }

            .navbar-toggler {
                display: inline-block;
            }
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar">
            <a href="index.php" style="text-decoration: none;">
                <h1>Fertilizer Connect</h1>
            </a>
            <button class="navbar-toggler" onclick="toggleMenu()">☰</button>
            <ul id="navbarNav">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="#footer">Contact</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Sign up</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="about" class="bg-light">
            <p>We are committed to improving agriculture by bridging the gap between farmers and fertilizer traders. Our mission is to create a transparent, accessible platform that makes it easier for farmers to find the best fertilizer for their crops.</p>
        </section>

        <section id="platform">
            <h2>How Our Platform Works</h2>
            <p>Our platform helps farmers connect with reliable fertilizer traders to get the right fertilizers for their crops. Farmers can browse through a variety of fertilizers and access detailed descriptions after registration.</p>
        </section>
    </main>

    <footer id="footer">
        <h5>FertiConnect</h5>
        <p>FertiConnect is your go-to platform for connecting farmers and fertilizer suppliers. Our mission is to empower farmers by providing easy access to high-quality fertilizers from trusted suppliers.</p>
        <div class="footer-links">
            <a href="#" class="footer-link">Home</a> |
            <a href="#" class="footer-link">Products</a> |
            <a href="#" class="footer-link">Traders</a> |
            <a href="#" class="footer-link">Contact Us</a>
        </div>
        <p>&copy; 2024 FertiConnect. All rights reserved.</p>
    </footer>

    <script>
        function toggleMenu() {
            const navbarNav = document.getElementById('navbarNav');
            navbarNav.classList.toggle('show');
        }
    </script>
</body>

</html>
