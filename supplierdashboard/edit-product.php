<?php
session_start();
include '../db.php'; // Replace with your actual connection script

// Disable displaying errors to users and enable error logging
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Ensure the user is authenticated
if (!isset($_SESSION['supplier_id']) || !isset($_SESSION['supplier_name'])) {
    // Redirect to login page or show an error
    header("Location: ../login.php");
    exit();
}

$supplier_id = $_SESSION['supplier_id'];
define('UPLOAD_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR);
define('UPLOAD_URL', '../uploads/'); // URL path for browser access
// Define allowed file extensions and maximum file size (e.g., 5MB)
$allowed_extensions = array("jpg", "jpeg", "png", "gif");
$max_file_size = 5 * 1024 * 1024; // 5MB

// Define the target directory by going up one level and then to Uploads
$target_dir = UPLOAD_DIR;

// Check if the uploads directory exists and is writable
if (!file_exists($target_dir)) {
    die("<div class='alert alert-danger'>Uploads directory does not exist.</div>");
}

if (!is_writable($target_dir)) {
    die("<div class='alert alert-danger'>The uploads directory is not writable. Please adjust the permissions.</div>");
}

// Function to sanitize input data
function sanitize_input($data)
{
    return htmlspecialchars(strip_tags(trim($data)));
}

// Check if product ID is passed via GET
if (!isset($_GET['id'])) {
    header('Location: products.php');
    exit();
}

$product_id = $_GET['id'];

// Fetch product data from the database
$query = "SELECT * FROM products WHERE id = ? AND supplier_id = ?";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    error_log("Database prepare failed: " . $conn->error);
    die("<div class='alert alert-danger'>An internal error occurred. Please try again later.</div>");
}
$stmt->bind_param("ii", $product_id, $supplier_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    header('Location: products.php');
    exit();
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("<div class='alert alert-danger'>Invalid CSRF token.</div>");
    }

    // Collect and sanitize form data
    $name = sanitize_input($_POST['name']);
    $description = sanitize_input($_POST['description']);
    $price = sanitize_input($_POST['price']);

    // Validate inputs
    if (empty($name) || empty($description) || empty($price)) {
        $error = "All fields are required.";
    } elseif (!is_numeric($price) || $price < 0) {
        $error = "Please enter a valid price.";
    } else {
        // Initialize filePath with existing image path
        $filePath = $product['filePath'];

        // Handle file upload if a new file is provided
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['file']['tmp_name'];
            $originalFileName = $_FILES['file']['name'];
            $fileSize = $_FILES['file']['size'];
            $fileType = $_FILES['file']['type'];
            $fileNameCmps = pathinfo($originalFileName);
            $fileExtension = strtolower($fileNameCmps['extension']);

            // Validate file size
            if ($fileSize > $max_file_size) {
                $error = "File size exceeds the maximum limit of 5MB.";
            }

            // Validate file extension
            if (!in_array($fileExtension, $allowed_extensions)) {
                $error = "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
            }

            // Proceed if no errors
            if (!isset($error)) {
                // Sanitize file name
                $newFileName = preg_replace("/[^A-Za-z0-9.\-]/", '_', $fileNameCmps['filename']) . '.' . $fileExtension;

                // Generate unique file name to prevent overwriting
                $finalFileName = $newFileName;
                $counter = 1;
                while (file_exists($target_dir . $finalFileName)) {
                    $finalFileName = preg_replace("/\.[^.]+$/", "", $newFileName) . "_$counter." . $fileExtension;
                    $counter++;
                }

                $target_file = $target_dir . $finalFileName;

                // When saving the file path to database, modify this part in your file upload section
                if (move_uploaded_file($fileTmpPath, $target_file)) {
                    // Store the path relative to the root directory
                    $filePath = 'uploads/' . $finalFileName; // Updated path format

                    // Delete old file if it exists and isn't the default
                    if ($product['filePath'] !== 'uploads/default.png' && file_exists(dirname(__DIR__) . DIRECTORY_SEPARATOR . $product['filePath'])) {
                        unlink(dirname(__DIR__) . DIRECTORY_SEPARATOR . $product['filePath']);
                    }
                } else {
                    $error = "Failed to move the uploaded file.";
                }
            }
        }

        // Proceed to update the product if no errors
        if (!isset($error)) {
            // Prepare the SQL statement
            $update_query = "UPDATE products SET name = ?, description = ?, price = ?, filePath = ?, updated_at = NOW() WHERE id = ? AND supplier_id = ?";
            $update_stmt = $conn->prepare($update_query);

            if ($update_stmt === false) {
                // Log the error and show a generic message to the user
                error_log("Database prepare failed: " . $conn->error);
                $error = "An internal error occurred. Please try again later.";
            } else {
                // Bind parameters
                $update_stmt->bind_param("ssdsii", $name, $description, $price, $filePath, $product_id, $supplier_id);

                // Execute the statement
                if ($update_stmt->execute()) {
                    // Success: Redirect to products page with success message
                    header('Location: products.php?update=success');
                    exit();
                } else {
                    // Log the error and show a generic message to the user
                    error_log("Database execution failed: " . $update_stmt->error);
                    $error = "Failed to update product. Please try again.";
                }

                // Close the statement
                $update_stmt->close();
            }
        }
    }

    // Fetch the updated product data to reflect changes in the form
    $query = "SELECT * FROM products WHERE id = ? AND supplier_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        error_log("Database prepare failed: " . $conn->error);
        die("<div class='alert alert-danger'>An internal error occurred. Please try again later.</div>");
    }
    $stmt->bind_param("ii", $product_id, $supplier_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    if (!$product) {
        header('Location: products.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Product - Dashboard Admin Template</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700" />
    <link rel="stylesheet" href="css/fontawesome.min.css" />
    <link rel="stylesheet" href="jquery-ui-datepicker/jquery-ui.min.css" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/templatemo-style.css">
    <style>
        /* Custom responsive styles */
        .tm-product-img-edit {
            position: relative;
            max-width: 100%;
            height: auto;
            margin-bottom: 1rem;
        }

        .tm-product-img-edit img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }

        .tm-upload-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 2.5rem;
            color: #fff;
            cursor: pointer;
            background: rgba(0, 0, 0, 0.5);
            padding: 1rem;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .tm-upload-icon:hover {
            background: rgba(0, 0, 0, 0.7);
        }

        .upload-btn-wrapper {
            width: 100%;
            padding: 0 15px;
            margin-bottom: 1.5rem;
        }

        .btn-upload {
            width: 100%;
            white-space: normal;
            word-wrap: break-word;
            padding: 10px 15px;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .tm-edit-product-row {
                flex-direction: column;
            }

            .col-xl-6, .col-lg-6 {
                width: 100%;
                padding: 0 15px;
            }

            .tm-product-img-edit {
                max-width: 300px;
                margin: 0 auto;
            }

            .btn-upload {
                font-size: 0.8rem;
                padding: 8px 12px;
            }
        }

        /* Form responsiveness improvements */
        .tm-edit-product-form {
            padding: 0 15px;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-control {
            width: 100%;
        }

        /* Update button responsiveness */
        .update-btn-wrapper {
            padding: 0 15px;
            margin-bottom: 1.5rem;
        }

        .btn-update {
            width: 100%;
        }

        /* Additional responsive improvements */
        .navbar-expand-xl {
            padding: 0.5rem 1rem;
        }

        .tm-block {
            padding: 20px;
        }

        @media (max-width: 480px) {
            .tm-block {
                padding: 15px;
            }

            .tm-block-title {
                font-size: 1.2rem;
            }

            .form-control {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-xl">
        <div class="container h-100">
            <a class="navbar-brand" href="products.php">
                <h1 class="tm-site-title mb-0">Product Admin</h1>
            </a>
            <button class="navbar-toggler ml-auto mr-0" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars tm-nav-icon"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mx-auto h-100">
                    <li class="nav-item">
                        <a class="nav-link active" href="products.php">
                            <i class="fas fa-shopping-cart"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="accounts.php">
                            <i class="far fa-user"></i> Accounts
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link d-block" href="../logout.php">
                            <?= htmlspecialchars($_SESSION['supplier_name']); ?>, <b>Logout</b>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container tm-mt-big tm-mb-big">
        <div class="row">
            <div class="col-xl-9 col-lg-10 col-md-12 col-sm-12 mx-auto">
                <div class="tm-bg-primary-dark tm-block tm-block-h-auto">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="tm-block-title d-inline-block">Edit Product</h2>
                        </div>
                    </div>
                    <div class="row tm-edit-product-row">
                        <div class="col-xl-6 col-lg-6 col-md-12">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            <form action="" method="post" enctype="multipart/form-data" class="tm-edit-product-form">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">

                                <div class="form-group mb-3">
                                    <label for="name">Product Name</label>
                                    <input id="name" name="name" type="text" value="<?= htmlspecialchars($product['name']); ?>" class="form-control validate" required />
                                </div>
                                <div class="form-group mb-3">
                                    <label for="description">Description</label>
                                    <textarea id="description" name="description" class="form-control validate tm-small" rows="5" required maxlength="150"><?= htmlspecialchars($product['description']); ?></textarea>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="price">Price</label>
                                    <input id="price" name="price" type="number" step="0.01" value="<?= htmlspecialchars($product['price']); ?>" class="form-control validate" required />
                                </div>

                                <div class="tm-product-img-edit mx-auto">
                                        <img id="productImage" src="<?php echo UPLOAD_URL . htmlspecialchars(basename($product['filePath'])); ?>" alt="Product image" class="img-fluid d-block mx-auto" onerror="this.onerror=null; this.src='../uploads/default.png';">
                                        <i class="fas fa-cloud-upload-alt tm-upload-icon" onclick="document.getElementById('fileInput').click();"></i>
                                    </div>
                                    <div class="custom-file mt-3 mb-3">
                                        <input id="fileInput" type="file" name="file" accept=".jpg,.jpeg,.png,.gif" style="display:none;" onchange="previewImage(event)" />
                                        <input type="button" class="btn btn-primary btn-block mx-auto" value="CHANGE IMAGE NOW" onclick="document.getElementById('fileInput').click();" />
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-block text-uppercase">Update Now</button>
                                </div>
                            </form>
                        </div>
                        <!-- Optional: Additional Information or Preview -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="tm-footer row tm-mt-small">
        <div class="col-12 font-weight-light">
            <p class="text-center text-white mb-0 px-4 small">
                Copyright &copy; <b>2024 Farticonnect. </b> All rights reserved.
            </p>
        </div>
    </footer>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="jquery-ui-datepicker/jquery-ui.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        function previewImage(event) {
            const imagePreview = document.getElementById('productImage');
            const file = event.target.files[0];
            const reader = new FileReader();

            if (file) {
                reader.onload = function() {
                    imagePreview.src = reader.result;
                }
                reader.readAsDataURL(file);
            } else {
                imagePreview.src = "uploads/default.png";
            }
        }
    </script>
</body>

</html>