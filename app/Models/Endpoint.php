<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Endpoint extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['shop_id', 'entity_id', 'entity_field_id', 'name', 'url'];

    protected $searchableFields = ['*'];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function entityFields()
    {
        return $this->belongsToMany(EntityField::class);
    }

    public function mainEntity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function mainEntityField()
    {
        return $this->belongsTo(EntityField::class);
    }
}
