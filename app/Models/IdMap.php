<?php

namespace App\Models;

/**
 * @param string $entity
 * @param int|string $old_id
 * @param string $new_id
 */
class IdMap extends Model
{
    protected $table = 'id_maps';
    public $timestamps = false;
    protected $fillable = ['entity', 'old_id', 'new_id'];
}
