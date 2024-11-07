<?php
session_start();
require('db.php');

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get input values
    $identifier = htmlspecialchars(trim($_POST['identifier'])); // Can be email or phone
    $password = htmlspecialchars(trim($_POST['password']));

    // Basic validation
    if (empty($identifier) || empty($password)) {
        $error = "All fields are required.";
    } else {
        // Function to validate phone number (accepts formats like +1234567890, 1234567890)
        function isValidPhone($phone) {
            // Remove any non-digit characters except leading +
            $cleanPhone = preg_replace('/[^\d+]/', '', $phone);
            // Check if it starts with + and has 10-15 digits, or just has 10-15 digits
            return preg_match('/^\+?\d{10,15}$/', $cleanPhone);
        }

        // Function to validate email
        function isValidEmail($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        }

        // Determine if input is email or phone
        $isEmail = isValidEmail($identifier);
        $isPhone = isValidPhone($identifier);

        if (!$isEmail && !$isPhone) {
            $error = "Please enter a valid email address or phone number.";
        } else {
            // Prepare the SQL query based on input type
            if ($isEmail) {
                $sql = "SELECT * FROM suppliers WHERE email = ?";
            } else {
                // Clean phone number to consistent format
                $identifier = preg_replace('/[^\d+]/', '', $identifier);
                $sql = "SELECT * FROM suppliers WHERE phone = ?";
            }

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $identifier);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Fetch user data
                $user = $result->fetch_assoc();

                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Successful login, set session variables
                    $_SESSION['supplier_name'] = $user['name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['supplier_id'] = $user['id'];
                    $_SESSION['loggedin'] = true;
                    header("Location: supplierdashboard/products.php");
                    exit();
                } else {
                    $error = "Incorrect password.";
                }
            } else {
                $error = "No user found with the provided " . ($isEmail ? "email" : "phone number") . ".";
            }

            // Close statement
            $stmt->close();
        }
    }
    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FertiConnect</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --plant-green: #2e8b57; /* More natural forest green */
            --plant-green-dark: #1f593a;
            --plant-green-light: #3cb371;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background: var(--plant-green-light);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background-color: var(--plant-green-dark);
            color: #fff;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .logo h1 {
            font-size: clamp(1.2rem, 4vw, 1.5rem);
            font-weight: 700;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 1.5rem;
        }

        .nav-links li {
            margin: 0;
        }

        .nav-links a {
            color: #fff;
            text-decoration: none;
            font-size: clamp(0.9rem, 3vw, 1rem);
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #c5e1a5;
        }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: clamp(1rem, 5vw, 2rem);
        }

        .card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            width: min(100%, 400px);
            margin: 0 auto;
        }

        .card-header {
            background-color: var(--plant-green);
            color: #fff;
            padding: 1.5rem;
            text-align: center;
        }

        .card-body {
            padding: clamp(1.5rem, 5vw, 2rem);
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        input {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--plant-green);
        }

        button {
            background-color: var(--plant-green);
            color: #fff;
            padding: 0.8rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            margin-top: 0.5rem;
        }

        button:hover {
            background-color: var(--plant-green-dark);
        }

        .form-footer {
            margin-top: 1rem;
            text-align: center;
            font-size: 0.9rem;
        }

        .form-footer a {
            color: var(--plant-green);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .form-footer a:hover {
            color: var(--plant-green-dark);
        }

        footer {
            background-color: var(--plant-green-dark);
            color: #fff;
            text-align: center;
            padding: 1rem;
            margin-top: auto;
            font-size: clamp(0.8rem, 3vw, 1rem);
        }

        .error {
            color: #d32f2f;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        /* Mobile menu styles */
        .menu-toggle {
            display: none;
            flex-direction: column;
            gap: 4px;
            cursor: pointer;
            padding: 5px;
        }

        .menu-toggle span {
            width: 25px;
            height: 3px;
            background-color: #fff;
            transition: 0.3s;
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: flex;
            }

            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background-color: var(--plant-green);
                flex-direction: column;
                padding: 1rem;
                text-align: center;
            }

            .nav-links.active {
                display: flex;
            }

            .card {
                margin: 0 1rem;
            }
        }
        .input-hint {
            font-size: 0.8rem;
            color: #666;
            margin-top: -0.5rem;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <h1>FertiConnect</h1>
            </div>
            <div class="menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="card">
            <div class="card-header">
                <h2>Login</h2>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                    <input type="text" id="identifier" name="identifier" placeholder="Enter your email or phone number" required>
                    <div class="input-hint">Enter either your email address or phone number (e.g., 0781111111)</div>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <span class="error"><?php echo $error; ?></span>
                    <button type="submit">Login</button>
                    <div class="form-footer">
                        <a href="forget.html">Forgot password?</a>
                        <p>Don't have an account? <a href="register.php">Register</a></p>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 FertiConnect. All rights reserved.</p>
    </footer>

    <script>
        const menuToggle = document.querySelector('.menu-toggle');
        const navLinks = document.querySelector('.nav-links');

        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });
    </script>
</body>
</html>