<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'logo', 'content', 'meta_title', 'meta_description'];

    public function products() { return $this->hasMany(Product::class); }
    public function series() { return $this->hasMany(Series::class); }
}