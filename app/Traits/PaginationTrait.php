<?php
namespace App\Traits;

trait PaginationTrait
{
    public function paginationLinks($paginator)
    {
        return [
            'total_pages' => $paginator->lastPage(),
            'current_page' => $paginator->currentPage(),
            'prev_page_url' => $paginator->previousPageUrl(),
            'next_page_url' => $paginator->nextPageUrl(),
        ];

    }
}
