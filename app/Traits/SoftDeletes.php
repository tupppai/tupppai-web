<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\SoftDeletingScope;

trait SoftDeletes
{
    /**
     * Indicates if the model is currently force deleting.
     *
     * @var bool
     */
    protected $forceDeleting = false;

    /**
     * Boot the soft deleting trait for a model.
     *
     * @return void
     */
    public static function bootSoftDeletes()
    {
        static::addGlobalScope(new SoftDeletingScope);
    }

    /**
     * Force a hard delete on a soft deleted model.
     *
     * @return void
     */
    public function forceDelete()
    {
        $this->forceDeleting = true;

        $this->delete();

        $this->forceDeleting = false;
    }

    /**
     * Perform the actual delete query on this model instance.
     *
     * @return void
     */
    protected function performDeleteOnModel()
    {
        if ($this->forceDeleting) {
            return $this->withTrashed()->where($this->getKeyName(), $this->getKey())->forceDelete();
        }

        return $this->runSoftDelete();
    }

    /**
     * Perform the actual delete query on this model instance.
     *
     * @return void
     */
    protected function runSoftDelete()
    {
        $query = $this->newQuery()->where($this->getKeyName(), $this->getKey());

        $this->{$this->getDeletedAtColumn()} = $time = $this->freshTimestamp();
        $this->{$this->getStatusColumn()}    = $this->getDeletedStatusValue();

        $query->update([
            $this->getDeletedAtColumn() => $time,
            $this->getStatusColumn() => $this->getDeletedStatusValue()
        ]);
    }

    /**
     * Restore a soft-deleted model instance.
     *
     * @return bool|null
     */
    public function restore()
    {
        // If the restoring event does not return false, we will proceed with this
        // restore operation. Otherwise, we bail out so the developer will stop
        // the restore totally. We will clear the deleted timestamp and save.
        if ($this->fireModelEvent('restoring') === false) {
            return false;
        }

        $this->{$this->getDeletedAtColumn()}= null;
        $this->{$this->getStatusColumn()}   = $this->getNormalStatusValue();

        // Once we have saved the model, we will fire the "restored" event so this
        // developer will do anything they need to after a restore operation is
        // totally finished. Then we will return the result of the save call.
        $this->exists = true;

        $result = $this->save();

        $this->fireModelEvent('restored', false);

        return $result;
    }

    /**
     * Determine if the model instance has been soft-deleted.
     *
     * @return bool
     */
    public function trashed()
    {
        return !is_null($this->{$this->getDeletedAtColumn()});
    }

    /**
     * Get a new query builder that includes soft deletes.
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public static function withTrashed()
    {
        return (new static)->newQueryWithoutScope(new SoftDeletingScope);
    }

    /**
     * Get a new query builder that only includes soft deletes.
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public static function onlyTrashed()
    {
        $instance = new static;

        $column = $instance->getQualifiedDeletedAtColumn();

        return $instance->newQueryWithoutScope(new SoftDeletingScope)->whereNotNull($column);
    }

    /**
     * Register a restoring model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function restoring($callback)
    {
        static::registerModelEvent('restoring', $callback);
    }

    /**
     * Register a restored model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function restored($callback)
    {
        static::registerModelEvent('restored', $callback);
    }

    /**
     * Get the name of the "deleted at" column.
     *
     * @return string
     */
    public function getDeletedAtColumn()
    {
        return defined('static::DELETED_AT') ? static::DELETED_AT : 'deleted_at';
    }
    
    /**
     * Get the name of the "deleted at status" column.
     *
     * @return string
     */
    public function getStatusColumn()
    {
        return defined('static::DELETED_STATUS_NAME') ? static::DELETED_STATUS_NAME : 'status';
    }

    /**
     * Get the value of the "deleted at status".
     *
     * @return string
     */
    public function getDeletedStatusValue()
    {
        return defined('static::DELETED_STATUS') ? static::DELETED_STATUS : 0;
    }

    /**
     * Get the value of the "normal status".
     *
     * @return string
     */
    public function getNormalStatusValue()
    {
        return defined('static::NORMAL_STATUS') ? static::NORMAL_STATUS: 1;
    }

    /**
     * Get the fully qualified "deleted at" column.
     *
     * @return string
     */
    public function getQualifiedDeletedAtColumn()
    {
        return $this->getTable().'.'.$this->getDeletedAtColumn();
    }
}
