<?php

namespace App\Livewire;

use App\Models\Material;
use Livewire\Component;
use Livewire\Attributes\On;

class MaterialDetailModal extends Component
{
    public $materialId = null;
    public $showModal = false;

    #[On('open-detail-modal')]
    public function openModal($materialId)
    {
        $this->materialId = $materialId;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->materialId = null;
    }

    public function getMaterial()
    {
        return Material::with(['materialFisico', 'materialDigital'])
            ->findOrFail($this->materialId);
    }

    public function render()
    {
        return view('livewire.material-detail-modal', [
            'material' => $this->materialId ? $this->getMaterial() : null
        ]);
    }
}
