<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Gigs extends Model {

    use HasFactory;

    protected $fillable =[
        'user_id','gig_type','title','email','description','tag','salary','image'
    ];

    // List all added gigs
    
    // List gig by ID
    // public static function find($id){
    //     $gigs =self::all();
        
    //     foreach($gigs as $gig){
    //         if($gig['id']==$id){
    //             return $gig;
    //         }
    //     }
    // }

    public function scopeFilter($query, array $filters){
        if($filters['tag'] ?? false){
            $query->where('tag','like', '%' . request('tag') .'%');
        }

        if($filters['type'] ?? false){
            $query->where('gig_type','like', '%' . request('type') .'%')
            ->orWhere('gig_type','like', '%' . request('type') .'%');
        }

        if($filters['search'] ?? false){
           
            $query
            ->where('title','like', '%' . request('search') .'%')
            ->orWhere('tag','like', '%' . request('search') .'%')
            ->orWhere('description','like', '%' . request('search') .'%')
            ->orWhere('gig_type','like', '%' . request('search') .'%');
        }
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function requests(){
        return $this->hasMany(Requests::class, 'gig_id');
    }
}
