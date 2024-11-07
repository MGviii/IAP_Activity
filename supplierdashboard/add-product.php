<?php
session_start();
include('../db.php');

// Disable displaying errors to users
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Enable error logging
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

// Define the uploads directory using an absolute path
$target_dir = UPLOAD_DIR;

// Check if the uploads directory exists
if (!file_exists($target_dir)) {
    die("<div class='alert alert-danger'>Uploads directory does not exist.</div>");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);

    // Basic validation
    if (empty($name) || empty($description) || empty($price)) {
        echo "<div class='alert alert-danger'>All fields are required.</div>";
    } elseif (!is_numeric($price) || $price < 0) {
        echo "<div class='alert alert-danger'>Please enter a valid price.</div>";
    } elseif (isset($_FILES['filePath']) && $_FILES['filePath']['error'] === UPLOAD_ERR_OK) {
        // Handle the uploaded file
        $fileTmpPath = $_FILES['filePath']['tmp_name'];
        $originalFileName = $_FILES['filePath']['name'];
        $fileSize = $_FILES['filePath']['size'];
        $fileType = $_FILES['filePath']['type'];
        $fileNameCmps = pathinfo($originalFileName);
        $fileExtension = strtolower($fileNameCmps['extension']);

        // Check file size
        if ($fileSize > $max_file_size) {
            echo "<div class='alert alert-danger'>File size exceeds the maximum limit of 5MB.</div>";
            exit();
        }

        // Check file extension
        if (!in_array($fileExtension, $allowed_extensions)) {
            echo "<div class='alert alert-danger'>Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.</div>";
            exit();
        }

        // Sanitize file name
        $newFileName = preg_replace("/[^A-Za-z0-9.\-]/", '_', $fileNameCmps['filename']) . '.' . $fileExtension;

        // Prevent overwriting existing files
        $counter = 1;
        $finalFileName = $newFileName;
        while (file_exists($target_dir . $finalFileName)) {
            $finalFileName = preg_replace("/\.[^.]+$/", "", $newFileName) . "_$counter." . $fileExtension;
            $counter++;
        }

        $target_file = $target_dir . $finalFileName;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($fileTmpPath, $target_file)) {
            // Prepare SQL query to include supplier_id and sanitized file name
            $sql = "INSERT INTO products (supplier_id, name, description, price, filePath) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                // Log the error and show a generic message to the user
                error_log("Database prepare failed: " . $conn->error);
                echo "<div class='alert alert-danger'>An internal error occurred. Please try again later.</div>";
            } else {
                $stmt->bind_param("issss", $supplier_id, $name, $description, $price, $finalFileName);

                // Execute the statement
                if ($stmt->execute()) {
                    echo "<div class='alert alert-success'>Product added successfully!</div>";
                } else {
                    // Log the error and show a generic message to the user
                    error_log("Database execution failed: " . $stmt->error);
                    echo "<div class='alert alert-danger'>An internal error occurred. Please try again later.</div>";
                }

                $stmt->close();
            }
        } else {
            $error = error_get_last();
            error_log("Error moving file: " . $error['message']);
            echo "<div class='alert alert-danger'>Error moving file. Please try again.</div>";
        }
    } else {
        // Handle different upload errors
        $upload_error = $_FILES['filePath']['error'];
        $error_messages = array(
            UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
            UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
        );

        $message = isset($error_messages[$upload_error]) ? $error_messages[$upload_error] : 'Unknown upload error.';
        echo "<div class='alert alert-danger'>Error uploading file: " . htmlspecialchars($message) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Add Product - Dashboard HTML Template</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700" />
    <link rel="stylesheet" href="css/fontawesome.min.css" />
    <link rel="stylesheet" href="jquery-ui-datepicker/jquery-ui.min.css" type="text/css" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/templatemo-style.css">
    <style>
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container.tm-mt-big {
                padding: 15px;
                margin-top: 1rem !important;
            }
            
            .tm-block {
                padding: 20px;
            }
            
            .tm-edit-product-row {
                flex-direction: column;
            }
            
            .col-xl-6.col-lg-6.col-md-12 {
                width: 100%;
                padding: 0;
            }
            
            .tm-product-img-dummy {
                max-width: 100%;
                height: auto;
                margin: 10px 0;
            }
            
            .form-group {
                margin-bottom: 1rem;
            }
            
            .btn {
                width: 100%;
                margin: 5px 0;
            }
        }

        /* General responsive improvements */
        .tm-block {
            overflow-x: hidden;
        }
        
        .navbar-brand {
            max-width: 80%;
        }
        
        .tm-site-title {
            font-size: clamp(1.2rem, 4vw, 1.8rem);
        }
        
        .form-control {
            max-width: 100%;
        }
        
        #imagePreview {
            max-width: 100%;
            height: auto;
        }
        
        .tm-footer {
            padding: 15px;
            margin-top: 2rem;
        }
        
        /* Improve form layout on medium screens */
        @media (min-width: 769px) and (max-width: 1199px) {
            .tm-edit-product-row {
                gap: 20px;
            }
            
            .col-xl-6.col-lg-6.col-md-12 {
                flex: 0 0 100%;
                max-width: 100%;
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
                        <?php echo htmlspecialchars($_SESSION['supplier_name']); ?>, <b>Logout</b>
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
                            <h2 class="tm-block-title d-inline-block">Add Product</h2>
                        </div>
                    </div>
                    <div class="row tm-edit-product-row">
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <form action="" method="POST" enctype="multipart/form-data" class="tm-edit-product-form">
                                <div class="form-group mb-3">
                                    <label for="name">Product Name</label>
                                    <input id="name" name="name" type="text" class="form-control validate" required />
                                </div>
                                <div class="form-group mb-3">
                                    <label for="description">Description</label>
                                    <textarea id="description" name="description" class="form-control validate" rows="3" required maxlength="150"></textarea>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="price">Price</label>
                                    <input id="price" name="price" type="number" step="0.01" class="form-control validate" required />
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-8 mx-auto mb-4">
                                        <div class="tm-product-img-dummy mx-auto">
                                            <i class="fas fa-cloud-upload-alt tm-upload-icon" onclick="document.getElementById('fileInput').click();"></i>
                                        </div>
                                        <div class="custom-file mt-3 mb-3">
                                            <input id="fileInput" name="filePath" type="file" style="display:none;" required onchange="previewImage(event)" />
                                            <button type="button" class="btn btn-primary btn-block text-uppercase w-100" onclick="document.getElementById('fileInput').click();">
                                                UPLOAD PRODUCT IMAGE
                                            </button>
                                        </div>
                                        <img id="imagePreview" src="" alt="Image Preview" style="display:none; max-width: 100%; height: auto; margin-top: 10px;" />
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary btn-block text-uppercase w-100">Add Product Now</button>
                                    </div>
                                </div>
                            </form>
                        </div>
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
            const imagePreview = document.getElementById('imagePreview');
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = function(){
                imagePreview.src = reader.result;
                imagePreview.style.display = 'block'; // Show the image preview
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                imagePreview.src = "";
                imagePreview.style.display = 'none'; // Hide the image preview if no file is selected
            }
        }
    </script>
</body>
</html>