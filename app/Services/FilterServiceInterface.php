<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

interface FilterServiceInterface
{
    public function applyFilters(Builder $query, array $filters);
}