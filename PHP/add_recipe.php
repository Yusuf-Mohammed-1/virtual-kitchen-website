<?php
session_start();
require 'config.php';

if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['uid'];
$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $type = $_POST['type'];
    $description = trim($_POST['description']);
    $cookingtime = intval($_POST['cookingtime']);
    $ingredients = trim($_POST['ingredients']);
    $instructions = trim($_POST['instructions']);
    $image = "";


    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $image = basename($_FILES["image"]["name"]);
        $target_file = $upload_dir . $image;

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $errors[] = "Failed to upload image.";
        }
    }

    // Validate input
    if (empty($name) || empty($type) || empty($description) || $cookingtime <= 0 || empty($ingredients) || empty($instructions)) {
        $errors[] = "Please fill in all required fields.";
    } else {
        $sql = "INSERT INTO recipes (name, description, type, cookingtime, ingredients, instructions, image, uid)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssisssi", $name, $description, $type, $cookingtime, $ingredients, $instructions, $image, $uid);

        if ($stmt->execute()) {
            $success = "Recipe added successfully!";
        } else {
            $errors[] = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Recipe</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 700px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        h2 {
            margin-bottom: 20px;
            color: #3e64ff;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        input[type="file"] {
            padding: 3px;
        }

        button {
            margin-top: 20px;
            background-color: #3e64ff;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background-color: #2f4ccc;
        }

        .back-link {
            margin-top: 15px;
            display: block;
            text-align: right;
        }

        .back-link a {
            color: #3e64ff;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
            margin-top: 10px;
        }

        .success {
            color: green;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Add New Recipe</h2>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form action="add_recipe.php" method="post" enctype="multipart/form-data">
        <label>Recipe Name*</label>
        <input type="text" name="name" required>

        <label>Type*</label>
        <select name="type" required>
            <option value="">--Select--</option>
            <option value="French">French</option>
            <option value="Italian">Italian</option>
            <option value="Chinese">Chinese</option>
            <option value="Indian">Indian</option>
            <option value="Mexican">Mexican</option>
            <option value="Others">Others</option>
        </select>

        <label>Description*</label>
        <textarea name="description" required></textarea>

        <label>Cooking Time (minutes)*</label>
        <input type="number" name="cookingtime" min="1" required>

        <label>Ingredients*</label>
        <textarea name="ingredients" required></textarea>

        <label>Instructions*</label>
        <textarea name="instructions" required></textarea>

        <label>Image</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit">Add Recipe</button>
    </form>

    <div class="back-link">
        <a href="dashboard.php">← Back to Dashboard</a>
    </div>
</div>
</body>
</html>
