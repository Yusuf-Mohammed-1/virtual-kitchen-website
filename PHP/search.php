<?php

include('config.php');


session_start();


if (!isset($_SESSION['uid'])) {
    header('Location: login.php');
    exit();
}

$query = isset($_GET['query']) ? $_GET['query'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';

$sql = "SELECT recipes.*, users.username FROM recipes JOIN users ON recipes.uid = users.uid WHERE (name LIKE ? OR type LIKE ?)";
$params = ["%$query%", "%$query%"];

// The code here checks if a specific type of recipe is selected and adds it to the query
if (!empty($type)) {
    $sql .= " AND type = ?";
    $params[] = $type;
}


$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat('s', count($params)), ...$params);
$stmt->execute();
$result = $stmt->get_result();

// This code finds the results
$recipes = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
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

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .recipes-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .recipes-table th, .recipes-table td {
            padding: 12px;
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

        .btn {
            padding: 8px 12px;
            border-radius: 5px;
            color: white;
            text-align: center;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-primary {
            background-color: #007bff;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .no-results {
            text-align: center;
            margin-top: 20px;
            font-size: 18px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Search Results</h2>
        <?php if (count($recipes) > 0): ?>
            <table class="recipes-table">
                <thead>
                    <tr>
                        <th>Recipe Name</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recipes as $recipe): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($recipe['name']); ?></td>
                            <td><?php echo htmlspecialchars($recipe['type']); ?></td>
                            <td><?php echo htmlspecialchars($recipe['description']); ?></td>
                            <td>
                                <?php if ($recipe['uid'] == $_SESSION['uid']): ?>
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
            <p class="no-results">No recipes found matching your search criteria.</p>
        <?php endif; ?>
    </div>
</body>
</html>