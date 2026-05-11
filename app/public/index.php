<?php

require_once __DIR__ . "/../vendor/autoload.php";

use ProsekClub\Demo\Database;

$connection = Database::getConnection();
$action = $_POST["action"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && $action === "add") {
    $title = trim($_POST["title"] ?? "");

    if ($title === "") {
        header("Location: /");
        exit();
    }

    $statement = $connection->prepare("insert into todos (title) values (?)");
    $statement->execute([$title]);

    header("Location: /");
    exit();
}

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    $action === "toggle" &&
    isset($_GET["id"])
) {
    $statement = $connection->prepare(
        "update todos set is_done = not is_done where id = ?",
    );
    $statement->execute([(int) $_GET["id"]]);

    header("Location: /");
    exit();
}

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    $action === "delete" &&
    isset($_GET["id"])
) {
    $statement = $connection->prepare("delete from todos where id = ?");
    $statement->execute([(int) $_GET["id"]]);

    header("Location: /");
    exit();
}

$todoItems = $connection
    ->query("select * from todos order by created_at desc")
    ->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Todos</title>
</head>
<body>
    <h1>Todos</h1>

    <form method="post" action="/">
        <input type="hidden" name="action" value="add">
        <input type="text" name="title" placeholder="New todo" required>
        <button type="submit">Create</button>
    </form>

    <?php if (empty($todoItems)): ?>
        <p>No todos yet.</p>
    <?php else: ?>
        <ul>
        <?php foreach ($todoItems as $todoItem): ?>
            <li>
                <span><?= htmlspecialchars($todoItem["title"]) ?></span>
                <form method="post"
                    action="/?id=<?= $todoItem["id"] ?>"
                    style="display:inline"
                >
                    <input type="hidden" name="action" value="toggle">
                    <button type="submit"><?= $todoItem["is_done"]
                        ? "Undo"
                        : "Done" ?></button>
                </form>
                <form
                    method="POST"
                    action="/?id=<?= $todoItem["id"] ?>"
                    style="display:inline"
                >
                    <input type="hidden" name="action" value="delete">
                    <button type="submit">Delete</button>
                </form>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
