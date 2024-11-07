<?php
require('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));
    $contact_details = htmlspecialchars(trim($_POST['contact_details']));
    $address = htmlspecialchars(trim($_POST['address']));
    $description = htmlspecialchars(trim($_POST['description']));

    // Validate required fields
    if (empty($name) || empty($email) || empty($password) || empty($contact_details) || empty($description)) {
        die("All fields are required.");
    }

    // Validate phone number format
    if (!preg_match('/^(07[2389])[0-9]{7}$/', $contact_details)) {
        die("Invalid phone number. Phone number must start with 072, 073, 078, or 079 and be 10 digits long.");
    }

    // Check for existing email
    $sql = "SELECT * FROM suppliers WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        die("Email already registered in suppliers.");
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Handle file upload
    $logo_path = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $target_dir = "uploads/";
        $file_name = basename($_FILES["file"]["name"]);
        $target_file = $target_dir . time() . "_" . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = array("jpg", "jpeg", "png", "gif");

        if (in_array($imageFileType, $allowed_types)) {
            if ($_FILES["file"]["size"] > 2000000) {
                die("Sorry, your file is too large. Maximum size is 2MB.");
            }

            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $logo_path = $target_file;
            } else {
                die("Error uploading file.");
            }
        } else {
            die("Only JPG, JPEG, PNG, & GIF files are allowed.");
        }
    }

    // Insert into database
    $sql = "INSERT INTO suppliers (name, email, password, phone, address, logo, description) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $name, $email, $hashed_password, $contact_details, $address, $logo_path, $description);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit();
    } else {
        die("Error during insertion");
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Registration</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --plant-green: #2e8b57;
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
            font-size: 1.5rem;
            font-weight: 700;
        }

        .nav-links {
            display: flex;
            list-style: none;
        }

        .nav-links li {
            margin-left: 20px;
        }

        .nav-links a {
            color: #fff;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #ffc107;
        }

        .form-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
            margin-bottom: 50px;
        }

        .form-header {
            margin-bottom: 30px;
            text-align: center;
        }

        .form-header h2 {
            color: var(--plant-green-dark);
            font-size: 24px;
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
        }

        .btn-custom {
            background-color: var(--plant-green-dark);
            color: white;
            border: none;
        }

        .btn-custom:hover {
            background-color: #218838;
        }

        /* Input focus effects */
        input:focus, textarea:focus {
            border-color: #28a745;
            box-shadow: 0 0 5px rgba(40, 167, 69, 0.5);
        }

        .form-control {
            border-radius: 8px;
        }
        
        .form-footer {
            margin-top: 1rem;
            text-align: center;
        }

        .form-footer a {
            color: #28a745;
            text-decoration: none;
        }

        footer {
            background-color: var(--plant-green-dark);
            color: #fff;
            text-align: center;
            padding: 1rem 0;
            margin-top: auto;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-container {
                padding: 20px;
            }
        }
    </style>
    <script>
        function validatePhone(input) {
            const phoneNumber = input.value.trim();
            const phoneRegex = /^(07[2389])[0-9]{7}$/;
            const errorElement = document.getElementById('phone-error');
            
            if (!phoneRegex.test(phoneNumber)) {
                errorElement.textContent = "Phone number must start with 072, 073, 078, or 079 and be 10 digits long";
                input.setCustomValidity("Invalid phone number format");
            } else {
                errorElement.textContent = "";
                input.setCustomValidity("");
            }
        }
    </script>
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <h1>FertiConnect</h1>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="supplier.php">Traders</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="form-container">
                    <div class="form-header">
                        <h2 style="color: #218838;">Supplier Registration</h2>
                    </div>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password (min 6 characters)" required>
                        </div>

                        <div class="mb-3">
                            <label for="contact_details" class="form-label">Phone Number</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="contact_details" 
                                   name="contact_details" 
                                   placeholder="Enter your phone number (e.g., 0781234567)" 
                                   pattern="^(07[2389])[0-9]{7}$"
                                   oninput="validatePhone(this)"
                                   required>
                            <div id="phone-error" class="text-danger"></div>
                            <small class="form-text text-muted">Phone number must start with 072, 073, 078, or 079 and be 10 digits long</small>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address" placeholder="Enter your address">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter a brief description" maxlength="50"></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-custom btn-lg">Register</button>
                        </div>
                    </form>

                    <div class="form-footer">
                        <p>Already have an account? <a href="login.php">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <footer>
        <p>&copy; 2024 FertiConnect. All rights reserved.</p>
    </footer>
</body>
</html>