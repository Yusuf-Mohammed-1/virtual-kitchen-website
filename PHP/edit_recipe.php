<?php
include('config.php');
session_start();


if (!isset($_SESSION['uid'])) {
    header('Location: login.php');
    exit();
}

$errorMessage = '';

if (isset($_GET['id'])) {
    $recipe_id = intval($_GET['id']);

    // The code below finds the recipe to edit it
    $sql = "SELECT * FROM recipes WHERE rid = ? AND uid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $recipe_id, $_SESSION['uid']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $recipe = $result->fetch_assoc();
    } else {
        header('Location: dashboard.php?message=You do not have permission to edit this recipe.');
        exit();
    }
} else {
    header('Location: dashboard.php?message=No recipe selected to edit.');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $cookingtime = intval($_POST['cookingtime'] ?? 0);
    $ingredients = trim($_POST['ingredients'] ?? '');
    $instructions = trim($_POST['instructions'] ?? '');
    $image = $_FILES['image']['name'] ?? '';

    if (!is_numeric($cookingtime)) {
        $errorMessage = "Cooking time must be a valid number.";
    } else {
        if ($image) {
            $image_tmp = $_FILES['image']['tmp_name'];
            $image_path = 'images/' . basename($image);
            if (!move_uploaded_file($image_tmp, $image_path)) {
                $errorMessage = "Failed to upload image.";
            }
        } else {
            $image = $recipe['image'];
        }

        // This code prepares the SQL statement to update the recipe
        $update_sql = "UPDATE recipes 
                       SET name = ?, type = ?, description = ?, cookingtime = ?, ingredients = ?, instructions = ?, image = ? 
                       WHERE rid = ?";

        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param('sssisssi', $name, $type, $description, $cookingtime, $ingredients, $instructions, $image, $recipe_id);

        if ($stmt->execute()) {
            header('Location: dashboard.php?message=Recipe updated successfully!');
            exit();
        } else {
            $errorMessage = "Error updating recipe: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Recipe</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin: 20px 0;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
        }
        label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
            color: #333;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        textarea {
            resize: vertical;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 20px;
            text-align: center;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #45a049;
 }
        .image-preview {
            margin-top: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Edit Recipe</h1>

    <?php if ($errorMessage): ?>
        <div class="error-message"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <label for="name">Recipe Name</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($recipe['name'] ?? '', ENT_QUOTES); ?>" required>

        <label for="type">Type</label>
        <input type="text" id="type" name="type" value="<?php echo htmlspecialchars($recipe['type'] ?? '', ENT_QUOTES); ?>" required>

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($recipe['description'] ?? '', ENT_QUOTES); ?></textarea>

        <label for="cookingtime">Cooking Time (in minutes)</label>
        <input type="number" id="cookingtime" name="cookingtime" value="<?php echo htmlspecialchars($recipe['cookingtime'] ?? '', ENT_QUOTES); ?>" required>

        <label for="ingredients">Ingredients</label>
        <textarea id="ingredients" name="ingredients" rows="4" required><?php echo htmlspecialchars($recipe['ingredients'] ?? '', ENT_QUOTES); ?></textarea>

        <label for="instructions">Instructions</label>
        <textarea id="instructions" name="instructions" rows="4" required><?php echo htmlspecialchars($recipe['instructions'] ?? '', ENT_QUOTES); ?></textarea>

        <label for="image">Upload Image</label>
        <input type="file" id="image" name="image">
        <div class="image-preview">
            <?php if (!empty($recipe['image'])): ?>
                <img src="images/<?php echo htmlspecialchars($recipe['image']); ?>" alt="Recipe Image" style="max-width: 100%; height: auto;">
            <?php endif; ?>
        </div>

        <button type="submit">Update Recipe</button>
    </form>
</div>
</body>
</html>