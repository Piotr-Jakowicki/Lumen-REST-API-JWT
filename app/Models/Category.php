<?php

namespace App\Models;

use App\Filters\FilterBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id', 'name'
    ];

    public function scopeFilterBy($query, $filters)
    {
        $namespace = 'App\Filters\CategoryFilters';
        $filter = new FilterBuilder($query, $filters, $namespace);

        return $filter->apply();
    }
}
