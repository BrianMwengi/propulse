<?php

use Livewire\Component;
use App\Models\Property;
use Illuminate\Support\Collection;

new class extends Component {
    public string $postcode = '';
    public string $propertyType = '';

    public Collection $properties;
    public int $perPage = 8;
    public bool $hasMoreProperties = true;

    public function mount(): void
    {
        $this->properties = collect();
        $this->loadInitialProperties();
    }

    public function updated($property): void
    {
        if (in_array($property, ['postcode', 'propertyType'])) {
            $this->loadInitialProperties();
        }
    }

    protected function baseQuery()
    {
        return Property::query()
            ->when($this->postcode, fn ($q) => $q->where('postcode', 'like', "{$this->postcode}%"))
            ->when($this->propertyType, fn ($q) => $q->whereRaw('LOWER(property_type) = ?', [strtolower($this->propertyType)]))
            ->latest();
    }

    public function loadInitialProperties(): void
    {
        sleep(2); // Simulate a delay for loading
        $results = $this->baseQuery()->limit($this->perPage)->get();

        $this->properties = $results;
        $this->hasMoreProperties = $results->count() >= $this->perPage;
    }

    public function loadMore(): void
    {
        sleep(2); // Simulate a delay for loading
        if (! $this->hasMoreProperties) {
            return;
        }

        $newProperties = $this->baseQuery()
            ->skip($this->properties->count())
            ->limit($this->perPage)
            ->get();

        $this->properties = $this->properties->concat($newProperties);
        $this->hasMoreProperties = $newProperties->count() >= $this->perPage;
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

   <div class="flex gap-4 mb-6 has-data-loading:opacity-60 transition-opacity">
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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse ($properties as $property)
                <div wire:key="{{ $property->id }}"
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $property->postcode }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">£{{ number_format($property->price) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ ucfirst($property->property_type) }} · {{ $property->bedrooms }} bed</p>
                </div>
            @empty
                <p class="text-gray-500 col-span-full">No properties match your search.</p>
            @endforelse
        </div>

       @if ($hasMoreProperties)
        <div wire:intersect="loadMore" class="col-span-full py-6 text-center text-sm text-gray-400">
            <span class="not-in-data-loading:hidden">Loading more properties...</span>
            <span class="in-data-loading:hidden">Scroll for more</span>
        </div>
    @endif
    @endisland

    <div class="mt-16">
        <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Area Breakdown</h2>
        <livewire:stats.area-breakdown lazy />
    </div>
</div>