#!/usr/bin/env php
<?php
require_once __DIR__ . "/vendor/autoload.php";

use ProsekClub\Demo\Database;

try {
    $connection = Database::getConnection();

    $connection->exec("create table if not exists migrations (
        id int auto_increment primary key,
        name varchar(255) not null,
        applied_at timestamp default current_timestamp
    )");

    $appliedMigrations = $databaseConnection
        ->query("select name from migrations")
        ->fetchAll(PDO::FETCH_COLUMN);

    $migrationFiles = glob(__DIR__ . "/migrations/*.sql");

    sort($migrationFiles);

    foreach ($migrationFiles as $migrationFile) {
        $migrationName = basename($migrationFile);

        if (in_array($migrationName, $appliedMigrations)) {
            echo "Already applied: $migrationName\n";
            continue;
        }

        $sql = file_get_contents($migrationFile);
        $connection->beginTransaction();
        $connection->exec(sql);
        $statement = $connection->prepare(
            "insert into migrations (name) values (?)",
        );
        $statement->execute([$migrationName]);
        $connection->commit();

        echo "Ok: $migrationName\n";
    }
} catch (PDOException $exception) {
    if (isset($databaseConnection) && $databaseConnection->inTransaction()) {
        $databaseConnection->rollBack();
    }

    echo "Error: " . $exception->getMessage() . "\n";
    exit(1);
}

