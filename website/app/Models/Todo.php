<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Todo extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'deadline',
        'completed_at',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'completed_at' => 'datetime:Y-m-d H:i:s',
        'deadline' => 'date',
    ]; 
    
    protected function serializeDate($date){
        if ($date){
            $carbonDate = Carbon::parse($date);
            return $carbonDate->timezone('Asia/Jakarta')->format('Y-m-d H:i:s');
        }
        return null;
    }

     // Accessor untuk mendapatkan status real-time
    public function getStatusAttribute($value)
    {
        // Jika sudah selesai, tetap selesai
        if ($value === 'completed') {
            return $value;
        }
        
        // Jika belum selesai dan sudah melewati deadline, ubah ke terlambat
        if ($value !== 'completed' && Carbon::parse($this->deadline)->isPast()) {
            return 'late';
        }
        
        return $value;
    }

    public function updateStatusBasedOnDeadLine(){
        if ($this->status !== 'completed' && $this->deadline && Carbon::parse($this->deadline)->isPast()){
            $this->update(['status'=> 'late']);

        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}