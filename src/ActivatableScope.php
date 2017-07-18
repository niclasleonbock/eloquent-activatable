<?php
namespace niclasleonbock\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ActivatableScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
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
