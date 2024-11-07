<?php
session_start(); // Starts a session to keep track of session variables, such as the user's login information.

include("../db.php"); // Includes the database connection file, allowing access to the database.

include 'pay.php'; // Includes the 'pay.php' file, likely containing the payment processing functions or logic.

if (!isset($_SESSION['supplier_id'])) { // Checks if the user is logged in by verifying if 'supplier_id' is set in the session.
    header("Location:../login.php"); // Redirects the user to the login page if they are not logged in.
    exit(); // Ensures the script stops executing after the redirect.
}

if (isset($_POST['phone_number'])) { // Checks if the 'phone_number' field is submitted via POST.
    $phone_number = $_POST['phone_number']; // Retrieves the submitted phone number.

    // Validate phone number format
    if (!preg_match('/^(078|079|072|073)\d{7}$/', $phone_number)) { // Checks if the phone number matches the required pattern.
        echo "Invalid phone number. It must start with 078, 079, 072, or 073 and be exactly 10 digits long."; // Error message for an invalid phone number.
        exit(); // Stops further script execution if the phone number is invalid.
    }
    
    // Get form input
    $supplier_id = $_SESSION['supplier_id']; // Retrieves the supplier's ID from the session.
    $phone_number = $_POST['phone_number']; // Retrieves the phone number from the form submission.
    $amount = 500; // Sets the amount to be paid to 500.

    // Generate random payment reference
    $transaction_ref = rand(100000, 999999); // Generates a random 6-digit transaction reference number.
    
    // Prefix with 'farti-connect'
    $transaction_ref = 'farti-connect-' . $transaction_ref; // Adds a prefix to the transaction reference for identification.

    $payment = hdev_payment::pay($phone_number, $amount, $transaction_ref, ''); // Initiates the payment using the pay function in hdev_payment, passing the phone number, amount, and transaction reference.

    if (!is_null($payment) && $payment->status == 'success') { // Checks if the payment was successful.
        echo "<script>alert(" . $payment->message . ");</script>"; // Shows an alert message on the success of the payment.

        // Uncommented redirections (likely for debugging or future use)
         header("Location: add-product.php?msg=Payment successful! You can now add your product."); // Redirects to add-product page with a success message.
         header("Location: wait.php?tx_ref=".$transaction_ref); // Redirects to a waiting page with the transaction reference.

        $payment_success = true; // Marks payment as successful.
    } else {
        header("Location: products.php?msg=Payment failed! Please try again."); // Redirects to the products page with a failure message if the payment failed.
    }

    // Mock payment process (used in absence of actual payment gateway logic)
    //$payment_success = true; // Sets payment as successful, replace with actual gateway result in a live environment.

    // Prepare SQL to insert transaction into the database
    $status = $payment_success ? 'pending' : 'failed'; // Determines the payment status as 'success' or 'failed'.
    $sql = "INSERT INTO transactions (supplier_id, phone_number, amount, status, tx_ref) VALUES (?, ?, ?, ?,?)"; // SQL statement for inserting a transaction record.
    $stmt = $conn->prepare($sql); // Prepares the SQL statement.
    $stmt->bind_param("isdss", $supplier_id, $phone_number, $amount, $status,$transaction_ref); // Binds the supplier ID, phone number, amount, and status to the SQL statement.
    $stmt->execute(); // Executes the SQL statement, adding the transaction record to the database.

    // Check if payment was successful and redirect accordingly
    if ($payment_success) { // If payment is successful,
        echo "<script>window.location.href='wait.php?tx_ref=" . $transaction_ref . "';</script>"; // Redirects to 'wait.php' with the transaction reference.
    } else {
        header("Location: products.php?msg=Payment failed! Please try again."); // If payment failed, redirects to products page with an error message.
    }
}
exit(); // Ends the script execution.
