<?php
// Include the database connection
include('config.php');

// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['uid'])) {
    header('Location: login.php');
    exit();
}

// Check if the recipe ID is provided
if (isset($_GET['id'])) {
    $recipe_id = $_GET['id'];

    // Prepare the SQL statement to delete the recipe
    $sql = "DELETE FROM recipes WHERE rid = ? AND uid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $recipe_id, $_SESSION['uid']);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect to the dashboard with a success message
        header('Location: dashboard.php?message=Recipe deleted successfully!');
        exit();
    } else {
        // Redirect to the dashboard with an error message
        header('Location: dashboard.php?message=Error deleting recipe.');
        exit();
    }
} else {
    // Redirect to the dashboard if no recipe ID is provided
    header('Location: dashboard.php?message=No recipe ID provided.');
    exit();
}
?>