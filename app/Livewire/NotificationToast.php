<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationToast extends Component
{
    public $message = '';
    public $type = 'success'; // success, error, warning, info
    public $duration = 3000; // milliseconds

    #[\Livewire\Attributes\On('notify')]
    public function notify($message, $type = 'success', $duration = 3000)
    {
        $this->message = $message;
        $this->type = $type;
        $this->duration = $duration;
    }

    public function render()
    {
        return view('livewire.notification-toast');
    }
}
