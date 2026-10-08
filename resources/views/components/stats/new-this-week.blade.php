<?php

use App\Models\Property;
use Livewire\Component;

new class extends Component {
    public int $count = 0;

    public function mount(): void
    {
        sleep(1);

        $this->count = Property::where('created_at', '>=', now()->subWeek())->count();
    }
};
?>

@placeholder
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 animate-pulse">
    <div class="h-4 bg-gray-200 rounded w-24 mb-2"></div>
    <div class="h-8 bg-gray-200 rounded w-12"></div>
</div>
@endplaceholder

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">New This Week</p>
    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $count }}</p>
</div>