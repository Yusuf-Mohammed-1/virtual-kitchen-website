<?php
require 'config.php';

$sql = "SELECT recipes.*, users.username FROM recipes JOIN users ON recipes.uid = users.uid ORDER BY recipes.rid DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Recipes | Virtual Kitchen</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .recipe {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .recipe h2 {
            margin-top: 0;
        }

        .recipe p {
            margin: 6px 0;
        }

        .meta {
            color: #777;
            font-size: 0.9em;
        }

        .recipe-type {
            display: inline-block;
            background: #e0e0e0;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.85em;
        }

        .nav-button {
            display: inline-block;
            margin: 10px 0;
            padding: 10px 15px;
            background-color: #007BFF; /* Bootstrap primary color */
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .nav-button:hover {
            background-color: #0056b3; 
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Explore All Recipes</h1>
    <a href="index.html" class="nav-button">Home</a>
    <a href="guest.php" class="nav-button">Guest Page</a>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="recipe">
                <h2><?php echo htmlspecialchars($row["name"]); ?></h2>
                <p class="recipe-type"><?php echo htmlspecialchars($row["type"]); ?></p>
                <p><?php echo nl2br(htmlspecialchars($row["description"])); ?></p>
                <p class="meta">By <?php echo htmlspecialchars($row["username"]); ?> | Cooking time: 
                    <?php 
                    // The code here checks if cookingtime exists and it is numeric
                    if (isset($row["Cookingtime"]) && is_numeric($row["Cookingtime"])) {
                        echo htmlspecialchars($row["Cookingtime"]) . ' mins';
                    } else {
                        echo 'Not specified';
                    }
                    ?>
                </p>
                <a href="recipe_detail.php?rid=<?php echo $row["rid"]; ?>">View Full Recipe</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No recipes available yet.</p>
    <?php endif; ?>
</div>

</body>
</html>