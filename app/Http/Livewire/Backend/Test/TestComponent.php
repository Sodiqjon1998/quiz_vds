<?php

namespace App\Http\Livewire\Backend\Test;

use Livewire\Component;

class TestComponent extends Component
{
    public $count = 0;

    public function increment()
    {
        $this->count++;
    }

    public function decrement()
    {
        $this->count--;
    }


    public function render()
    {
        return view('livewire.backend.test.test-component');
    }
}
