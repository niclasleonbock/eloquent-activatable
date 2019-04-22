<?php
namespace niclasleonbock\Eloquent;

use niclasleonbock\Eloquent\ActivatableScope;

trait ActivatableTrait
{
    protected $activatedAtColumn = 'activated_at';

    /**
     * Boot the activatable trait for the model.
     *
     * @return void
     */
    public static function bootActivatableTrait()
    {
        static::addGlobalScope(new ActivatableScope());
    }

//    /**
//     * Get a new query builder that includes deactivated data sets.
//     *
//     * @return \Illuminate\Database\Eloquent\Builder|static
//     */
//    public static function withDeactivated()
//    {
//        return (new static)->newQueryWithoutScope(new ActivatableScope());
//    }

//    /**
//     * Get a new query builder that only includes deactivated data sets.
//     *
//     * @return \Illuminate\Database\Eloquent\Builder|static
//     */
//    public static function onlyDeactivated()
//    {
//        $instance = new static;
//
//        $column = $instance->getQualifiedActivatedAtColumn();
//
//        return $instance->newQueryWithoutScope(new ActivatableScope())->whereNotNull($column);
//    }

    /**
     * Determine if the model instance is activated.
     *
     * @return bool
     */
    public function activated()
    {
        return !is_null($this->{$this->getActivatedAtColumn()});
    }

    /**
     * Determine if the model instance is activated. Alias for activated().
     *
     * @return bool
     */
    public function isActivated()
    {
        return $this->activated();
    }

    /**
     * Activate the given model instance.
     *
     * @return void
     */
    public function activate()
    {
        if (false === $this->fireModelEvent('activate')) {
            return false;
        }

        $query = $this->newQuery()->where($this->getKeyName(), $this->getKey());

        $this->{$this->getActivatedAtColumn()} = $time = $this->freshTimestamp();

        $query->update([ $this->getActivatedAtColumn() => $this->fromDateTime($time) ]);
    }

    /**
     * Deactivate the given model instance.
     *
     * @return void
     */
    public function deactivate()
    {
        if (false === $this->fireModelEvent('deactivate')) {
            return false;
        }

        $query = $this->newQuery()->where($this->getKeyName(), $this->getKey());

        $this->{$this->getActivatedAtColumn()} = null;

        $query->update([ $this->getActivatedAtColumn() => null ]);
    }

    /**
     * Get the name of the "activated at" column.
     *
     * @return string
     */
    public function getActivatedAtColumn()
    {
        return $this->activatedAtColumn;
    }

    /**
     * Get the fully qualified "activated at" column.
     *
     * @return string
     */
    public function getQualifiedActivatedAtColumn()
    {
        return $this->getTable() . '.' . $this->getActivatedAtColumn();
    }
}
