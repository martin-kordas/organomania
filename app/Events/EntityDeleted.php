<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;
use App\Interfaces\EntityEvent;

class EntityDeleted implements EntityEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(private Model $model)
    {
        //
    }
    
    public function getModel(): Model
    {
        return $this->model;
    }
    
    public function getAmountDiff(): int
    {
        return -1;
    }
    
}
