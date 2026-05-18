<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedbackAnak extends Model
{
    protected $fillable = ['anak_id', 'pesan'];

    public function anak()
    {
        return $this->belongsTo(Anak::class);
    }
}
