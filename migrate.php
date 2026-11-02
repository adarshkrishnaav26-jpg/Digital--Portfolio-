<?php
require 'db.php';

function addColumnIfMissing($db, $table, $column, $definition) {
    $columns = $db->query("PRAGMA table_info($table)")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($columns as $col) {
        if ($col['name'] === $column) {
            echo "$table.$column already exists\n";
            return;
        }
    }

    $db->exec("ALTER TABLE $table ADD COLUMN $column $definition");
    echo "$table.$column added successfully\n";
}

try {
    $db->beginTransaction();

    // Student profile fields
    addColumnIfMissing($db, 'students', 'public_slug', 'TEXT');
    addColumnIfMissing($db, 'students', 'bio', 'TEXT');
    addColumnIfMissing($db, 'students', 'degree', 'TEXT');
    addColumnIfMissing($db, 'students', 'department', 'TEXT');
    addColumnIfMissing($db, 'students', 'graduation_year', 'INTEGER');

    // Achievement verification fields
    addColumnIfMissing($db, 'achievements', 'category', 'TEXT');
    addColumnIfMissing($db, 'achievements', 'issuer_name', 'TEXT');
    addColumnIfMissing($db, 'achievements', 'issue_date', 'TEXT');
    addColumnIfMissing($db, 'achievements', 'evidence_url', 'TEXT');
    addColumnIfMissing($db, 'achievements', 'evidence_file', 'TEXT');
    addColumnIfMissing($db, 'achievements', 'verified_at', 'TEXT');

    // SHA-256 verification token storage
    $db->exec("
        CREATE TABLE IF NOT EXISTS verification_tokens (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            achievement_id INTEGER NOT NULL UNIQUE,
            token_hash TEXT NOT NULL UNIQUE,
            qr_code_url TEXT,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (achievement_id) REFERENCES achievements(id)
        )
    ");

    $db->commit();
    echo "\nDatabase migration completed successfully!\n";

} catch (Throwable $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo "Migration failed: " . $e->getMessage() . "\n";
}
