<?php

namespace App\Service;

class PaginationService
{
    private array $pagination;

    /**
     * Pagination for API responses
     *
     * @param  string $type
     * @param  int    $limit
     * @param  int    $total
     * @return array
     */
    public function pagination(string $type, int $limit, int $total): array
    {
        $page = 0;
        while ($page < ($total / $limit)) {
            $page++;
            $pagination[$page] = ['page ' . $page => '/api/v1/' . $type . '?page=' . $page];
        }
        return $pagination;
    }
}