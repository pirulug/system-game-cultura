<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class Category extends Model
{
    public static function getAll(bool $onlyActive = true): array
    {
        $sql = "SELECT c.*, (SELECT COUNT(*) FROM questions q WHERE q.category_id = c.id AND q.is_active = 1) AS questions_count
                FROM categories c";
        if ($onlyActive) {
            $sql .= " WHERE c.is_active = 1";
        }
        $sql .= " ORDER BY c.display_order ASC, c.name ASC";

        $stmt = self::db()->query($sql);
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array
    {
        $stmt = self::db()->prepare("SELECT * FROM categories WHERE id = :id LIMIT 1");
        $stmt->execute([":id" => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(string $name, string $slug, ?string $description, string $iconClass, int $displayOrder = 0): int
    {
        $stmt = self::db()->prepare(
            "INSERT INTO categories (name, slug, description, icon_class, display_order, is_active)
             VALUES (:n, :s, :d, :i, :o, 1)"
        );
        $stmt->execute([
            ":n" => $name,
            ":s" => $slug,
            ":d" => $description,
            ":i" => $iconClass,
            ":o" => $displayOrder,
        ]);
        return (int)self::db()->lastInsertId();
    }

    public static function update(int $id, string $name, string $slug, ?string $description, string $iconClass, int $displayOrder, bool $isActive): bool
    {
        $stmt = self::db()->prepare(
            "UPDATE categories SET name = :n, slug = :s, description = :d, icon_class = :i, display_order = :o, is_active = :a WHERE id = :id"
        );
        return $stmt->execute([
            ":n" => $name,
            ":s" => $slug,
            ":d" => $description,
            ":i" => $iconClass,
            ":o" => $displayOrder,
            ":a" => $isActive ? 1 : 0,
            ":id" => $id,
        ]);
    }

    public static function toggleActive(int $id): bool
    {
        $stmt = self::db()->prepare("UPDATE categories SET is_active = IF(is_active = 1, 0, 1) WHERE id = :id");
        return $stmt->execute([":id" => $id]);
    }

    public static function countAll(): int
    {
        $stmt = self::db()->query("SELECT COUNT(*) FROM categories");
        return (int)$stmt->fetchColumn();
    }
}

