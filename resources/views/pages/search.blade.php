<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\ShortlistItem;
use App\Models\Property;

new class extends Component {
    public string $postcode = '';
    public string $propertyType = '';

    #[Computed]
    public function properties()
    {
        // sleep(2); // temporary before recording is done

        return Property::query()
            ->when($this->postcode, fn ($q) => $q->where('postcode', 'like', "{$this->postcode}%"))
            ->when($this->propertyType, fn ($q) => $q->whereRaw('LOWER(property_type) = ?', [strtolower($this->propertyType)]))
            ->latest()
            ->get();
    }


    public function saveToShortlist($propertyId): void
    {
        ShortlistItem::firstOrCreate(
            ['session_id' => session()->getId(), 'property_id' => $propertyId],
            ['sort_order' => ShortlistItem::where('session_id', session()->getId())->max('sort_order') + 1]
        );

        $this->dispatch('shortlist-updated');
    }
};
?>

<div>
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Find a Property</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Search by postcode and type</p>
    </div>

    <div class="mb-6">
        <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Overview</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <livewire:stats.total-listings lazy.bundle />
            <livewire:stats.average-price lazy.bundle />
            <livewire:stats.new-this-week lazy.bundle />
        </div>
    </div>
  

    <div class="flex gap-4 mb-6">
        <input type="text" wire:model.live.debounce.400ms="postcode" wire:island="results" placeholder="Postcode"
            class="border rounded-lg px-3 py-2 w-full">
        <select wire:model.live="propertyType" wire:island="results" class="border rounded-lg px-3 py-2">
            <option value="">All Types</option>
            <option value="flat">Flat</option>
            <option value="house">House</option>
        </select>
    </div>

     @island(name: 'results', defer: true)
        @placeholder
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @for ($i = 0; $i < 4; $i++)
                    <div class="animate-pulse h-32 bg-gray-200 rounded-lg"></div>
                @endfor
            </div>
        @endplaceholder

        
    <div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse ($this->properties as $property)
                <div wire:key="{{ $property->id }}"
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $property->postcode }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">£{{ number_format($property->price) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ ucfirst($property->property_type) }} · {{ $property->bedrooms }} bed</p>
                    <button wire:click="saveToShortlist({{ $property->id }})"
                        class="text-xs text-blue-500 mt-2 hover:underline">
                        Save to shortlist
                    </button>
                </div>
            @empty
                <p class="text-gray-500">No properties match your search.</p>
            @endforelse
        </div>
    </div>
@endisland
    <div class="mt-16">
        <livewire:shortlist />
    </div>
    <div class="mt-16">
        <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Area Breakdown</h2>
        <livewire:stats.area-breakdown lazy />
    </div>
</div>
</div>


