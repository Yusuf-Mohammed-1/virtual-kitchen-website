<?php

include('config.php'); 


session_start();

// This code finds all recipes from the database
$sql = "SELECT recipes.*, users.username FROM recipes JOIN users ON recipes.uid = users.uid"; // Join with users to get the username
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

// The code here check if there are any recipes to display
$recipes = $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];

// This code below checks if the user is logged in
$isLoggedIn = isset($_SESSION['uid']);
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; // Get the username
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Recipes - Dashboard</title>
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
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        header {
            text-align: center;
            padding: 20px;
            background-color: #007bff;
            color: white;
            border-radius: 8px;
        }

        header h1 {
            margin: 0;
        }

        header p {
            margin: 5px 0;
        }

        a {
            text-decoration: none;
            color: white;
        }

        .btn {
            padding: 8px 12px; 
            border-radius: 5px;
            color: white;
            text-align: center;
            cursor: pointer;
            font-size: 0.9em; 
            display: inline-block; 
            margin: 5px; 
        }

        .btn-primary {
            background-color: #007bff;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .recipes-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .recipes-table th, .recipes-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .recipes-table th {
            background-color: #007bff;
            color: white;
        }

        .recipes-table tr:hover {
            background-color: #f1f1f1;
        }

        section {
            margin-top: 20px;
        }

        .message {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #d6e9c6;
            border-radius: 5px;
        }

        .add-recipe-btn {
            display: block;
            margin: 20px 0;
            text-align: center;
        }

        .search-form {
            margin-bottom: 20px;
        }

        .recipe-image {
            max-width: 100px; 
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Welcome to Your Dashboard</h1>
            <p>Hello, <?php echo htmlspecialchars($username); ?>!</p>
            <p>Manage your recipes here</p>
            <?php if ($isLoggedIn): ?>
                <a href="logout.php">Logout</a>
            <?php endif; ?>
        </header>

        <!-- Search Form -->
        <form class="search-form" action="search.php" method="GET">
            <input type="text" name="query" placeholder="Search by name or type" required>
            <select name="type">
                <option value="">All Types</option>
                <option value="French">French</option>
                <option value="Italian">Italian</option>
                <option value="Mexican">Mexican</option>
                <option value="Indian"> Indian</option>
                <option value="Chinese">Chinese</option>
            </select>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <section>
            <h2>Your Recipes</h2>
            <?php if (count($recipes) > 0): ?>
                <table class="recipes-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Cooking Time</th>
                            <th>Ingredients</th>
                            <th>Instructions</th>
                            <th>Image</th>
                            <th>Owner</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recipes as $recipe): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($recipe['name']); ?></td>
                                <td><?php echo htmlspecialchars($recipe['type']); ?></td>
                                <td>
                                    <?php 
                                    if (isset($recipe['Cookingtime']) && is_numeric($recipe['Cookingtime'])) {
                                        echo htmlspecialchars($recipe['Cookingtime']) . ' minutes';
                                    } else {
                                        echo 'Not specified';
                                    }
                                    ?>
                                </td>
                                <td><?php echo nl2br(htmlspecialchars($recipe['ingredients'])); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($recipe['instructions'])); ?></td>
                                <td>
                                    <?php if (!empty($recipe['image'])): ?>
                                        <img src="images/<?php echo htmlspecialchars($recipe['image']); ?>" alt="Image of <?php echo htmlspecialchars($recipe['name']); ?>" class="recipe-image">
                                    <?php else: ?>
                                        <img src="path/to/default/image/icon.png" alt="No Image Available" class="recipe-image">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($recipe['username']); ?></td>
                                <td>
                                    <?php if ($isLoggedIn && $recipe['uid'] == $_SESSION['uid']): ?>
                                        <a href="edit_recipe.php?id=<?php echo $recipe['rid']; ?>" class="btn btn-primary">Edit</a>
                                        <a href="delete.php?id=<?php echo $recipe['rid']; ?>" class="btn btn-danger">Delete</a>
                                    <?php else: ?>
                                        <a href="recipe_detail.php?rid=<?php echo $recipe['rid']; ?>" class="btn btn-primary">View</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No recipes found. Start adding some!</p>
            <?php endif; ?>
        </section>

        <div class="add-recipe-btn">
            <?php if ($isLoggedIn): ?>
                <a href="add_recipe.php" class="btn btn-primary">Add New Recipe</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>