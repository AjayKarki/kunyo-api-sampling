<?php

use Illuminate\Database\Eloquent\Builder;

\Illuminate\Database\Query\Builder::macro('addSubSelect', function ($column, $query) {
    if (is_null($this->getQuery()->columns)) {
        $this->select($this->getQuery()->from.'.*');
    }

    return $this->selectSub($query->limit(1)->getQuery(), $column);
});

Builder::macro('orderBySub', function ($query, $direction = 'asc') {
    return $this->orderByRaw("({$query->limit(1)->toSql()}) {$direction}");
});

Builder::macro('orderBySubDesc', function ($query) {
    return $this->orderBySub($query, 'desc');
});

/**
 * @usage $query->whereLike(['email', 'phone_number', 'roles.slug'], $keyword);
 */
Builder::macro('whereLike', function ($columns, string $value) {
    $this->where(function (Builder $query) use ($columns, $value) {
        foreach (Arr::wrap($columns) as $column) {
            $query->when(
                Str::contains($column, '.'),

                // Relational searches
                function (Builder $query) use ($column, $value) {
                    [$relationName, $relationColumn] = explode('.', $column);

                    return $query->orWhereHas(
                        $relationName,
                        function (Builder $query) use ($relationColumn, $value) {
                            $query->where($relationColumn, 'LIKE', "%{$value}%");
                        }
                    );
                },

                // Default searches
                function (Builder $query) use ($column, $value) {
                    return $query->orWhere($column, 'LIKE', "%{$value}%");
                }
            );
        }
    });

    return $this;
});

/**
 * @usage $query->correlate($relationName, $operator) ie $query->correlate('slugs')
 */
Builder::macro('correlate', function (string $relationName, $operator = '=') {
    $relation = $this->getRelation($relationName);

    return $this->join(
        $relation->getRelated()->getTable(),
        $relation->getQualifiedForeignKeyName(),
        $operator,
        $relation->getQualifiedOwnerKeyName()
    );
});

/**
 * @usage $query->leftCorrelate($relationName, $operator) ie $query->leftCorrelate('slugs')
 */
Builder::macro('leftCorrelate', function (string $relationName, $operator = '=') {
    $relation = $this->getRelation($relationName);

    return $this->leftJoin(
        $relation->getRelated()->getTable(),
        $relation->getQualifiedForeignKeyName(),
        $operator,
        $relation->getQualifiedOwnerKeyName()
    );
});

Builder::macro('selectAll', function () {
    if (is_null($this->getQuery()->columns)) {
        $this->select($this->getQuery()->from.'.*');
    }

    return $this;
});
