<?php
namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class Filter implements Scope
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function apply(Builder $builder, Model $model): void 
    {
        foreach ($this->filters as $column => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if ($column === 'compte') {
                $this->applyCompteFilter($builder, $value);
            } elseif (str_contains($column, '.')) {
                $parts = explode('.', $column);
                $relation = $parts[0];
                $field = $parts[1];
                
                $builder->whereHas($relation, function ($q) use ($field, $value) {
                    $this->applyWhere($q, $field, $value);
                });
            } else {
                $this->applyWhere($builder, $column, $value);
            }
        }
    }

    protected function applyWhere($query, $field, $value)
    {
        if (is_array($value)) {
            $query->whereIn($field, $value);
        } else {
            $query->where($field, $value);
        }
    }

    protected function applyCompteFilter($query, $value)
    {
        if (strtolower($value) === 'oui') {
            $query->whereNotNull('user_id');
        } elseif (strtolower($value) === 'non') {
            $query->whereNull('user_id');
        }
    }
}