<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferSksCpmk extends Model
{
    protected $table = 'transfer_sks_cpmk';

    protected $fillable = [
        'transfer_sks_id',
        'cpmk'
    ];
}
