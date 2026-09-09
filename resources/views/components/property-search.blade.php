<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Property;

new class extends Component {
    public string $postcode = '';
    public string $propertyType = '';

    #[Computed]
    public function properties()
    {
        return Property::query()
            ->when($this->postcode, fn ($q) => $q->where('postcode', 'like', "{$this->postcode}%"))
            ->when($this->propertyType, fn ($q) => $q->where('property_type', $this->propertyType))
            ->latest()
            ->get();
    }
};
?>

<div>
    {{-- Breathing in, I calm body and mind. Breathing out, I smile. - Thich Nhat Hanh --}}
</div>