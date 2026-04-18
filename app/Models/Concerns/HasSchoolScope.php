<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

trait HasSchoolScope
{
    protected static $schoolColumnCache = [];

    protected static function bootHasSchoolScope()
    {
        static::addGlobalScope('school_scope', function (Builder $builder) {
            if (!app()->bound('auth') || !auth()->check()) {
                return;
            }

            $user = auth()->user();

            if (!$user || $user->role === 'super_admin') {
                return;
            }

            $model = $builder->getModel();

            if (!static::modelHasSchoolColumn($model)) {
                return;
            }

            $builder->where($model->getTable() . '.school_id', $user->school_id);
        });

        static::creating(function (Model $model) {
            if (!app()->bound('auth') || !auth()->check()) {
                return;
            }

            $user = auth()->user();

            if (!$user || $user->role === 'super_admin') {
                return;
            }

            if (!static::modelHasSchoolColumn($model)) {
                return;
            }

            if (empty($model->school_id)) {
                $model->school_id = $user->school_id;
            }
        });
    }

    protected static function modelHasSchoolColumn(Model $model): bool
    {
        $table = $model->getTable();

        if (!array_key_exists($table, static::$schoolColumnCache)) {
            static::$schoolColumnCache[$table] = Schema::hasColumn($table, 'school_id');
        }

        return static::$schoolColumnCache[$table];
    }
}
