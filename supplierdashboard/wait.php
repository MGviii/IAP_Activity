<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../db.php");
include 'pay.php';

if ($_GET) {
    if (isset($_GET['tx_ref'])) {
        $tx_ref = $_GET['tx_ref'];
        
        $sql = "SELECT * FROM transactions WHERE tx_ref = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $tx_ref);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $transaction = $result->fetch_assoc();
            if ($transaction['status'] == 'pending') {
                $get_pay = hdev_payment::get_pay($tx_ref);
                
                if (!is_null($get_pay) && $get_pay->status == 'success') {
                    $sql = "UPDATE transactions SET status = 'success' WHERE tx_ref = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $tx_ref);
                    $stmt->execute();
                    header("Location: add-product.php?msg=Payment successful! You can now add your product.");
                    exit(); // Exit to stop further script execution

                } elseif (!is_null($get_pay) && ($get_pay->status == 'failed' || $get_pay->status == 'rejected')) {
                    $sql = "UPDATE transactions SET status = 'failed' WHERE tx_ref = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $tx_ref);
                    $stmt->execute();
                    header("Location: products.php?msg=Payment failed! Please try again.");
                    exit();
                } else {
                    $sql = "SELECT * FROM transactions WHERE tx_ref = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $tx_ref);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $transaction = $result->fetch_assoc();
                        if ($transaction['status'] == 'pending') {
                            echo "<h1>Payment is still pending. Please wait...</h1>";
                            echo "<script>setTimeout(function(){window.location.href='wait.php?tx_ref=" . $tx_ref . "';},5000);</script>";
                        } else {
                            header("Location: products.php?msg=Payment status unknown. Please try again.");
                            exit();
                        }
                    }
                }
            } else {
                if ($transaction['status'] == 'success') {
                    header("Location: add-product.php?msg=Payment successful! You can now add your product.");
                    exit();
                } else {
                    header("Location: products.php?msg=Payment failed! Please try again.");
                    exit();
                }
            }
        } else {
            header("Location: products.php?msg=Payment failed! Please try again.");
            exit();
        }
    }
}
