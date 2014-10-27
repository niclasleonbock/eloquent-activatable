<?php
namespace niclasleonbock\Eloquent;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;

class ActivatableScope implements ScopeInterface
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function apply(Builder $builder)
    {
        $model = $builder->getModel();

        $builder->whereNotNull($model->getQualifiedActivatedAtColumn());

        $builder->macro('withDeactivated', function (Builder $builder) {
            $this->remove($builder);

            return $builder;
        });

        $builder->macro('onlyDeactivated', function (Builder $builder) {
            $model = $builder->getModel();

            $this->remove($builder);

            $builder
                ->getQuery()
                ->whereNotNull($model->getQualifiedActivatedAtColumn());

            return $builder;
        });

        $builder->macro('deactivate', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withDeactivated();

            return $builder->update([ $model->getActivatedAtColumn() => null ]);
        });

        $builder->macro('activate', function (Builder $builder) {
            $model = $this->getModel();

            $query = $model->newQuery()->where($model->getKeyName(), $model->getKey());

            $model->{$model->getActivatedAtColumn()} = $time = $model->freshTimestamp();

            $query->update([ $model->getActivatedAtColumn() => $model->fromDateTime($time) ]);
        });
    }

    /**
     * Remove the scope from the given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function remove(Builder $builder)
    {
        $column = $builder->getModel()->getQualifiedActivatedAtColumn();
        $query = $builder->getQuery();

        foreach ((array) $query->wheres as $key => $where) {
            if ($this->isActivatedConstraint($where, $column)) {
                unset($query->wheres[$key]);

                $query->wheres = array_values($query->wheres);
            }
        }
    }

    /**
     * Determine if the given where clause is an activated constraint.
     *
     * @param  array   $where
     * @param  string  $column
     * @return bool
     */
    protected function isActivatedConstraint(array $where, $column)
    {
        return $where['type'] == 'NotNull' && $where['column'] == $column;
    }
}
