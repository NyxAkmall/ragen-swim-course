<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Sponsor extends Model {
 protected $fillable=['name','logo','website_url','sort_order','is_active'];
 protected $casts=['is_active'=>'boolean','sort_order'=>'integer'];
}
