<?php

namespace App\Core;

class Paginator
{
    public static function paginate(\PDO $db, string $table, int $page = 1, int $perPage = 20): array
    {
        $page = max(1, $page);
        $total = (int) $db->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        $pages = max(1, (int) ceil($total / $perPage));
        $offset = ($page - 1) * $perPage;
        $rows = $db->query("SELECT * FROM $table ORDER BY id DESC LIMIT $perPage OFFSET $offset")->fetchAll();

        return [
            'items' => $rows,
            'page' => $page,
            'pages' => $pages,
            'total' => $total,
            'perPage' => $perPage,
            'hasPrev' => $page > 1,
            'hasNext' => $page < $pages,
        ];
    }
}
