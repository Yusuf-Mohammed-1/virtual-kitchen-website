<?php
require 'config.php';

if (!isset($_GET['rid']) || empty($_GET['rid'])) {
    echo "Recipe ID missing.";
    exit;
}

$rid = intval($_GET['rid']);

$stmt = $conn->prepare("SELECT recipes.*, users.username 
                        FROM recipes 
                        JOIN users ON recipes.uid = users.uid 
                        WHERE recipes.rid = ?");
$stmt->bind_param("i", $rid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Recipe not found.";
    exit;
}

$recipe = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($recipe['name']); ?> | Recipe Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fff8f0;
            padding: 30px;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h1 {
            margin-top: 0;
        }

        .meta {
            color: #777;
            font-size: 0.9em;
        }

        .section {
            margin-top: 20px;
        }

        pre {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 6px;
            white-space: pre-wrap;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #444;
        }

        .recipe-image {
            max-width: 100%; 
            height: auto;
            border-radius: 8px; 
            margin-top: 20px; 
        }
    </style>
</head>
<body>

<div class="container">
    <h1><?php echo htmlspecialchars($recipe['name']); ?></h1>
    <p class="meta">Type: <?php echo htmlspecialchars($recipe['type']); ?> | Cooking time: 
    <?php 
    
    if (isset($recipe['Cookingtime']) && is_numeric($recipe['Cookingtime'])) {
        echo htmlspecialchars($recipe['Cookingtime']) . ' mins';
    } else {
        echo 'Not specified';
    }
    ?>
    </p>
    <p class="meta">By: <?php echo htmlspecialchars($recipe['username']); ?></p>

    <!-- Display the recipe image -->
    <?php if (!empty($recipe['image'])): ?>
        <img src="images/<?php echo htmlspecialchars($recipe['image']); ?>" alt="Image of <?php echo htmlspecialchars($recipe['name']); ?>" class="recipe-image">
    <?php else: ?>
        <p>No image available for this recipe.</p>
    <?php endif; ?>

    <div class="section">
        <h3>Description</h3>
        <p><?php echo nl2br(htmlspecialchars($recipe['description'])); ?></p>
    </div>

    <div class="section">
        <h3>Ingredients</h3>
        <pre><?php echo isset($recipe['ingredients']) ? htmlspecialchars($recipe['ingredients']) : ''; ?></pre>
    </div>

    <div class="section">
        <h3>Instructions</h3>
        <pre><?php echo htmlspecialchars($recipe['instructions']); ?></pre>
    </div>

    <a class="back-link" href="view_recipes.php">← Back to All Recipes</a>
    <a class="back-link" href="index.html">Home</a>
</div>

</body>
</html>