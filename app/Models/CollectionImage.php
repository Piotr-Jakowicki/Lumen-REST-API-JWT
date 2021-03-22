<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionImage extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'collection_image';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'image_id', 'collection_id'
    ];
}
