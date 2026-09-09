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
    {{-- Static header — outside the island, never re-renders --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Find a Property</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Search by postcode and type</p>
    </div>

    {{-- Filter bar — triggers the island, but this bar itself stays untouched --}}
    <div class="flex gap-4 mb-6">
        <input type="text" wire:model.live.debounce.400ms="postcode" placeholder="Postcode"
            class="border rounded-lg px-3 py-2 w-full">
        <select wire:model.live="propertyType" class="border rounded-lg px-3 py-2">
            <option value="">All Types</option>
            <option value="flat">Flat</option>
            <option value="house">House</option>
        </select>
    </div>

    {{-- Island: only this grid re-renders when filters change --}}
    @island('results')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse ($this->properties as $property)
                <div wire:key="{{ $property->id }}"
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $property->postcode }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">£{{ number_format($property->price) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ ucfirst($property->property_type) }} · {{ $property->bedrooms }} bed</p>
                </div>
            @empty
                <p class="text-gray-500">No properties match your search.</p>
            @endforelse
        </div>
    @endisland
</div>