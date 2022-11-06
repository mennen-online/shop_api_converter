<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Endpoint extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['shop_id', 'name', 'url'];

    protected $searchableFields = ['*'];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function entityFields()
    {
        return $this->belongsToMany(EntityField::class);
    }
}
