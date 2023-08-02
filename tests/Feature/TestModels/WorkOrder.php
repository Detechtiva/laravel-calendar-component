<?php

namespace Detechtiva\VueCalendarForLaravel\Tests\Feature\TestModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];
}
