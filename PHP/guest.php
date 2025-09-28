<?php

include('config.php'); 


$query = isset($_GET['query']) ? $_GET['query'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';


$sql = "SELECT recipes.*, users.username FROM recipes JOIN users ON recipes.uid = users.uid WHERE (name LIKE ? OR type LIKE ?)";
$params = ["%$query%", "%$query%"];


if (!empty($type)) {
    $sql .= " AND type = ?";
    $params[] = $type;
}

// The code below prepares and then executes the statement
$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat('s', count($params)), ...$params);
$stmt->execute();
$result = $stmt->get_result();

// The code below finds the results
$recipes = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Recipes</title>
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

        .nav-button {
            display: inline-block;
            margin: 10px 0;
            padding: 10px 15px;
            background-color: #007BFF; 
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .nav-button:hover {
            background-color: #0056b3; /* Darker shade for hover effect */
        }

        .search-form {
            margin-bottom: 20px;
            text-align: center;
        }

        .search-form input, .search-form select {
            padding: 10px;
            margin-right: 5px;
        }

        .search-form button {
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>All Recipes</h1>
        </header>
        <a href="index.html" class="nav-button">Home</a>
        <a href="view_recipes.php" class="nav-button">View Recipes</a>

        <!-- Search Form -->
        <form class="search-form" action="" method="GET">
            <input type="text" name="query" placeholder="Search by name or type" value="<?php echo htmlspecialchars($query); ?>" required>
            <select name="type">
                <option value="">All Types</option>
                <option value="French" <?php if ($type == 'French') echo 'selected'; ?>>French</option>
                <option value="Italian" <?php if ($type == 'Italian') echo 'selected'; ?>>Italian</option>
                <option value="Mexican" <?php if ($type == 'Mexican') echo 'selected'; ?>>Mexican</option>
                <option value="Indian" <?php if ($type == 'Indian') echo 'selected'; ?>>Indian</option>
                <option value="Chinese" <?php if ($type == 'Chinese') echo 'selected'; ?>>Chinese</option>
            </select>
            <button type="submit">Search</button>
        </form>

        <?php if (count($recipes) > 0): ?>
            <table class="recipes-table">
                <thead>
                    <tr>
                        <th>Recipe Name</th>
                        <th>Type</th>
                        <th>Cooking Time</th>
                        <th>Author</th>
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
                            <td><?php echo htmlspecialchars($recipe['username']); ?></td>
                            <td>
                                <a href="recipe_detail.php?rid=<?php echo $recipe['rid']; ?>">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No recipes found.</p>
        <?php endif; ?>
    </div>
</body>
</html>