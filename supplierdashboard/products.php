<?php
session_start(); // Start the session to access session variables
include("../db.php"); // Include the database connection
if(!isset($_SESSION['loggedin'])){
    header("location:../login.php");
    exit();
}
$id=$_SESSION['supplier_id'];
// Fetch products from the database
$sql = "SELECT * FROM products where supplier_id = $id"; // Adjust table name as necessary
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Product Page - Admin HTML Template</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700" />
    <link rel="stylesheet" href="css/fontawesome.min.css" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/templatemo-style.css">
    <style>
        .tm-product-edit-icon-large {
            font-size: 1.5em;
            color: #007bff;
        }
    </style>
    <script>
        function validatePhoneNumber() {
            const phoneInput = document.getElementById('nbr');
            const phoneNumber = phoneInput.value;

            // Regular expression to match 078, 079, 072, or 073 followed by 7 digits
            const phoneRegex = /^(078|079|072|073)\d{7}$/;

            if (!phoneRegex.test(phoneNumber)) {
                alert("Phone number must start with 078, 079, 072, or 073 and be exactly 10 digits long.");
                return false;
            }
            return true;
        }
    </script>
</head>

<body id="reportsPage">
<nav class="navbar navbar-expand-xl">
    <div class="container h-100">
        <a class="navbar-brand" href="products.php">
            <h1 class="tm-site-title mb-0">Product Admin</h1>
        </a>
        <button class="navbar-toggler ml-auto mr-0" type="button" data-toggle="collapse"
                data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                aria-expanded="false" aria-label="Toggle navigation">
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
                    <a class="nav-link" href="products.php">
                        <i class="far fa-user"></i> Accounts
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <?php if (isset($_SESSION['supplier_name'])): ?>
                        <a class="nav-link d-block" href="../logout.php">
                            <?= htmlspecialchars($_SESSION['supplier_name']); ?>, <b>Logout</b>
                        </a>
                    <?php else: ?>
                        <a class="nav-link d-block" href="login.php">
                            Login
                        </a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row tm-content-row">
        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8 tm-block-col">
            <div class="tm-bg-primary-dark tm-block tm-block-products">
                <div class="tm-product-table-container">
                    <?php if (isset($_GET['msg'])): ?>
                        <div class="alert alert-info">
                            <?= htmlspecialchars($_GET['msg']); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="delete-product.php">
                        <table class="table table-hover tm-table-small tm-product-table">
                            <thead>
                            <tr>
                                <th scope="col">&nbsp;</th>
                                <th scope="col">PRODUCT NAME</th>
                                <th scope="col">PRODUCT DESCRIPTION</th>
                                <th scope="col">PRICE</th>
                                <th scope="col">ACTION</th>
                                <th scope="col">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td><input type="checkbox" name="product_ids[]" value="' . htmlspecialchars($row['id']) . '" /></td>';
                                    echo '<td class="tm-product-name">' . htmlspecialchars($row['name']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['description']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['price']) . '</td>';
                                    echo '<td>
                                            <a href="edit-product.php?id=' . $row['id'] . '" class="tm-product-edit-link">
                                                <i class="fas fa-edit tm-product-edit-icon tm-product-edit-icon-large"></i>
                                            </a>
                                            <a href="delete-product.php?id=' . $row['id'] . '" class="tm-product-delete-link" onclick="return confirm(\'Are you sure you want to delete this product?\');">
                                                <i class="far fa-trash-alt tm-product-delete-icon"></i>
                                            </a>
                                          </td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="6" class="text-center">No products found</td></tr>';
                            }
                            ?>
                            </tbody>
                        </table>
                        <button type="submit" class="btn btn-primary btn-block text-uppercase mb-3" name="delete_selected">Delete selected products</button>
                    </form>
                </div>
                <button type="button" class="btn btn-success btn-block text-uppercase mb-3" data-toggle="modal" data-target="#paymentModal">Add new product</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Make Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="process-payment.php" method="POST" onsubmit="return validatePhoneNumber()">
                    <div class="form-group">
                        <label for="nbr">Phone Number</label>
                        <input type="number" class="form-control" id="nbr" name="phone_number" placeholder="Shyiramo nimero" required>
                    </div>
                    <button type="submit" class="btn btn-success mt-3 w-100">Make Payment</button>
                </form>
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
<script src="js/bootstrap.min.js"></script>
<script>
    $(function () {
        $(".tm-product-name").on("click", function () {
            window.location.href = "edit-product.php";
        });
    });
</script>
</body>
</html>
