<?php

namespace niclasleonbock\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ActivatableScope implements Scope
{

    /**
     * All of the extensions to be added to the builder.
     *
     * @var array
     */
    protected $extensions = ['WithDeactivated', 'WithoutDeactivated', 'OnlyDeactivated', 'Deactivate', 'Activate'];

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->whereNotNull($model->getQualifiedActivatedAtColumn());
    }

    public function extend(Builder $builder)
    {
        foreach ( $this->extensions as $extension ) {
            $this->{"add{$extension}"}($builder);
        }
    }

    public function addWithDeactivated(Builder $builder)
    {
        $builder->macro('withDeactivated', function (Builder $builder, $withDeactivated = true) {
            if ( !$withDeactivated ) {
                return $builder->withoutDeactivated();
            }

            return $builder->withoutGlobalScope($this);
        });
    }

    public function addWithoutDeactivated(Builder $builder)
    {
        $builder->macro('withoutDeactivated', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withoutGlobalScope($this)->whereNotNull(
                $model->getQualifiedActivatedAtColumn()
            );

            return $builder;
        });
    }

    public function addOnlyDeactivated(Builder $builder)
    {
        $builder->macro('onlyDeactivated', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withoutGlobalScope($this)->whereNull(
                $model->getQualifiedActivatedAtColumn()
            );

            return $builder;
        });
    }

    public function addDeactivate(Builder $builder)
    {
        $builder->macro('deactivate', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withDeactivated();

            return $builder->update([$model->getActivatedAtColumn() => null]);
        });
    }

    public function addActivate(Builder $builder)
    {
        $builder->macro('activate', function (Builder $builder) {
            $model = $this->getModel();

            $query = $model->newQuery()->where($model->getKeyName(), $model->getKey());

            $model->{$model->getActivatedAtColumn()} = $time = $model->freshTimestamp();

            $query->update([$model->getActivatedAtColumn() => $model->fromDateTime($time)]);
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
