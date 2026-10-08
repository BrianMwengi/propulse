<?php

use App\Models\Property;
use Livewire\Component;

new class extends Component {
    public ?float $average = null;

    public function mount(): void
    {
        sleep(1);

        $this->average = Property::avg('price');
    }
};
?>

@placeholder
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 animate-pulse">
    <div class="h-4 bg-gray-200 rounded w-24 mb-2"></div>
    <div class="h-8 bg-gray-200 rounded w-20"></div>
</div>
@endplaceholder

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Average Price</p>
    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">£{{ number_format($average ?? 0) }}</p>
</div>