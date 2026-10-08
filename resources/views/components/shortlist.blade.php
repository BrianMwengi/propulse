<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\ShortlistItem;

new class extends Component {
    public $items;
    public array $notes = [];

    public function mount(): void
    {
        $this->loadItems();
    }

    #[On('shortlist-updated')]
    public function loadItems(): void
    {
        $this->items = ShortlistItem::with('property')
            ->where('session_id', session()->getId())
            ->orderBy('sort_order')
            ->get();

        foreach ($this->items as $item) {
            if (! array_key_exists($item->id, $this->notes)) {
                $this->notes[$item->id] = $item->notes ?? '';
            }
        }
    }

    public function saveNote($itemId): void
    {
        ShortlistItem::where('id', $itemId)->update(['notes' => $this->notes[$itemId]]);
    }

    public function updateOrder($itemId, $newPosition): void
    {
        $ordered = $this->items->pluck('id')->reject(fn ($id) => $id == $itemId)->values()->toArray();
        array_splice($ordered, $newPosition, 0, $itemId);

        foreach ($ordered as $index => $id) {
            ShortlistItem::where('id', $id)->update(['sort_order' => $index]);
        }

        $this->loadItems();
    }

    public function remove($itemId): void
    {
        ShortlistItem::where('id', $itemId)->delete();
        unset($this->notes[$itemId]);
        $this->loadItems();
    }
};
?>

<div>
    <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Your Shortlist</h2>

    @if ($items->isEmpty())
        <p class="text-gray-400 text-sm">No properties saved yet.</p>
    @else
        <ul wire:sort="updateOrder" class="space-y-3">
            @foreach ($items as $item)
                <li wire:transition wire:sort:item="{{ $item->id }}" wire:key="{{ $item->id }}"
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm px-4 py-3 border border-gray-200 dark:border-gray-700 cursor-move">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $item->property->postcode }}</p>
                            <p class="text-xs text-gray-500">£{{ number_format($item->property->price) }}</p>
                        </div>
                        <button wire:click="remove({{ $item->id }})" wire:sort:ignore class="text-red-400 text-xs">
                            Remove
                        </button>
                    </div>

                    <div wire:sort:ignore class="mt-2">
                        <textarea wire:model="notes.{{ $item->id }}"
                            wire:bind:class="notes.{{ $item->id }}.length > 150 && 'border-yellow-500 bg-yellow-50'"
                            rows="2" placeholder="Add a note..."
                            class="w-full text-sm rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500"></textarea>

                        <div class="flex items-center justify-between mt-1">
                            <span wire:dirty wire:target="notes.{{ $item->id }}" class="text-xs text-yellow-600">
                                Unsaved
                            </span>
                            <button wire:click="saveNote({{ $item->id }})"
                                class="text-xs text-blue-500 hover:underline">
                                Save note
                            </button>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>