<?php
/**
 * User Safety Validation Page
 * 
 * This script checks if the current user has the "safety" role.
 * If not, an alert is shown and the user is redirected back.
 * If yes, the user is forwarded to the gate selection page.
 * 
 * All database queries are consolidated at the top for better maintainability.
 */

// ============================================================================
// INCLUDES & CONFIGURATION
// ============================================================================
include  "application/config/connection.php";

// ============================================================================
// INITIALIZE VARIABLES
// ============================================================================
$username = User::$username;
$user_role = '';

// ============================================================================
// DATABASE QUERIES - DATA RETRIEVAL
// ============================================================================

// ----------------------------------------------------------------------------
// 1. Fetch user role from database
// ----------------------------------------------------------------------------
$query = mysqli_query($con, 
    "SELECT user FROM tb_user WHERE username = '$username'"
) or die(mysqli_error($con));

if ($query && mysqli_num_rows($query) > 0) {
    $user_safety = mysqli_fetch_assoc($query);
    $user_role = $user_safety["user"] ?? '';
}

// ============================================================================
// BUSINESS LOGIC - ROLE VALIDATION
// ============================================================================

if ($user_role != "safety") {
    echo "<script>
            alert('Anda bukan User Safety..!!!');
            history.go(-1);
          </script>";
    exit;
} else {
    header("location: pilih_gate");
    exit;
}
?>