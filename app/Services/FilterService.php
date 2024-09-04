<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;


class FilterService implements FilterServiceInterface
{

public function applyFilters(Builder $query, array $filters)
    {

        foreach ($filters as $column => $value) {
            if (str_contains($column, '.')) {
                $parts = explode('.', $column);
                $relation = $parts[0];
                $field = $parts[1];
                
                $query->whereHas($relation, function ($q) use ($field, $value) {
                    if (is_array($value)) {
                        $q->whereIn($field, $value);
                    } else {
                        $q->where($field, $value);
                    }
                });
            } else {
                if (is_array($value)) {
                    $query->whereIn($column, $value);
                } else {
                    $query->where($column, $value);
                }
            }
        }
    }
}
