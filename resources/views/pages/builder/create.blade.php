<?php

use Livewire\Component;
use App\Models\Part;
use App\Models\Bike;

new class extends Component
{
    public $selectedParts = [];
    public $allParts = [];
    public $incompatibilities = [];
    public $searchQuery = '';
    public $selectedCategory = '';
    public $showSaveModal = false;
    public $editingBikeId = null;
    public $loaded = false;

    #[\Livewire\Attributes\Validate('required|string|max:255|min:3')]
    public $title = '';

    #[\Livewire\Attributes\Validate('nullable|string|max:1000')]
    public $description = '';

    public function mount()
    {
        $bikeId = request()->query('bike');
        if ($bikeId) {
            $bike = Bike::find($bikeId);
            if ($bike && auth()->id() === $bike->user_id) {
                $this->editingBikeId = $bike->id;
                $this->title = $bike->title;
                $this->description = $bike->description;
                $this->selectedParts = $bike->parts ?? [];
                $this->checkCompatibilities();
            }
        }
    }

    public function addPart($partId)
    {
        $part = Part::find($partId);
        if (!$part) return;

        $this->selectedParts[$part->category] = $partId;
        $this->checkCompatibilities();
    }

    public function removePart($category)
    {
        unset($this->selectedParts[$category]);
        $this->checkCompatibilities();
    }

    public function checkCompatibilities()
    {
        $this->incompatibilities = [];
        $selectedPartObjects = Part::whereIn('id', array_values($this->selectedParts))->get();

        foreach ($selectedPartObjects as $part) {
            if (!$part->compatibility) continue;

            $issues = [];
            $compatibility = $part->compatibility;

            // Speeds check
            if (isset($compatibility['min_speeds']) || isset($compatibility['max_speeds'])) {
                $speeds = $selectedPartObjects
                    ->where('category', 'cassette')
                    ->first()?->compatibility['speeds'] ?? null;

                if ($speeds) {
                    $minSpeeds = $compatibility['min_speeds'] ?? 0;
                    $maxSpeeds = $compatibility['max_speeds'] ?? 999;

                    if ($speeds < $minSpeeds || $speeds > $maxSpeeds) {
                        $issues[] = "Incompatible speeds: requires {$minSpeeds}-{$maxSpeeds} speeds but found {$speeds}";
                    }
                }
            }

            // Frame type check
            if (isset($compatibility['frame_types'])) {
                $frameType = $selectedPartObjects
                    ->where('category', 'frame')
                    ->first()?->compatibility['type'] ?? null;

                if ($frameType && !in_array($frameType, $compatibility['frame_types'])) {
                    $issues[] = "Incompatible frame type: {$frameType}";
                }
            }

            if (!empty($issues)) {
                $this->incompatibilities[] = [
                    'part_id' => $part->id,
                    'part_name' => $part->name,
                    'issues' => $issues,
                ];
            }
        }
    }

    public function getFilteredParts()
    {
        return $this->allParts->filter(function ($part) {
            $matchesSearch = empty($this->searchQuery) ||
                stripos($part->name, $this->searchQuery) !== false ||
                stripos($part->description, $this->searchQuery) !== false;

            $matchesCategory = empty($this->selectedCategory) ||
                $part->category === $this->selectedCategory;

            return $matchesSearch && $matchesCategory;
        });
    }

    public function loadParts()
    {
        if ($this->loaded) {
            return;
        }

        $this->allParts = collect(Part::all());
        $this->loaded = true;
    }

    public function getCategories()
    {
        return collect($this->allParts)->pluck('category')->unique()->sort();
    }

    public function openSaveModal()
    {
        if (empty($this->selectedParts)) {
            $this->dispatch('notify', message: 'Please select at least one part before saving');
            return;
        }
        $this->showSaveModal = true;
    }

    public function closeSaveModal()
    {
        $this->showSaveModal = false;
        $this->title = '';
        $this->description = '';
    }

    public function saveBike()
    {
        $this->validate([
            'title'       => 'required|string|max:255|min:3',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($this->editingBikeId) {
            $bike = Bike::find($this->editingBikeId);

            if (!$bike || auth()->id() !== $bike->user_id) {
                session()->flash('error', 'You are not authorized to update this bike.');
                $this->closeSaveModal();
                return;
            }

            $bike->update([
                'title'       => $this->title,
                'description' => $this->description,
                'parts'       => $this->selectedParts,
            ]);

            session()->flash('success', 'Bike updated successfully!');
        } else {
            Bike::create([
                'user_id'     => auth()->id(),
                'title'       => $this->title,
                'description' => $this->description,
                'parts'       => $this->selectedParts,
            ]);

            session()->flash('success', 'Bike saved successfully!');
        }

        $this->closeSaveModal();

        $this->selectedParts = [];
        $this->title = '';
        $this->description = '';
        $this->editingBikeId = null;
        $this->checkCompatibilities();
    }
};
?>
<div class="grid grid-rows-2 h-screen gap-3 md:gap-4 p-3 md:p-4 bg-white dark:bg-neutral-800 transition-colors">
    <!-- Top Row: Selected Parts (left) and Incompatibilities (right) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4">
        <!-- 1st Div: Selected Parts Display -->
        <div class="md:col-span-2 bg-zinc-100 dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-3 md:p-4 overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-base md:text-lg font-bold text-zinc-900 dark:text-white">Selected Parts</h2>
                <div class="flex gap-2">
                    <a href="{{ route('showcase') }}" class="px-3 md:px-4 py-1.5 md:py-2 rounded text-xs md:text-sm font-semibold bg-zinc-200 dark:bg-zinc-700 text-zinc-900 dark:text-white hover:bg-zinc-300 dark:hover:bg-zinc-600 transition border border-zinc-300 dark:border-zinc-600">
                        My Saved Projects
                    </a>
                    @if (!empty($this->selectedParts))
                        <button
                            wire:click="openSaveModal"
                            class="px-3 md:px-4 py-1.5 md:py-2 rounded text-xs md:text-sm font-semibold bg-[#1c0a00] hover:bg-[#2d1507] text-[#f5ede8] dark:bg-[#fff8f5] dark:hover:bg-white dark:text-[#1c0a00] transition border border-[#1c0a00] dark:border-[#fff8f5]"
                        >
                            Save Bike
                        </button>
                    @endif
                </div>
            </div>
            @if (empty($this->selectedParts))
                <p class="text-zinc-500 dark:text-zinc-400">No parts selected yet</p>
            @else
                <div class="space-y-2">
                    @foreach ($this->selectedParts as $category => $partId)
                        @php
                            $allParts = $this->allParts;

                            if (is_array($allParts)) {
                                $allParts = collect($allParts);
                            }

                            $part = $allParts->firstWhere('id', $partId);
                        @endphp
                        @if ($part)
                            <div wire:transition.opacity.delay.150ms class="bg-white dark:bg-zinc-800 p-3 rounded-lg flex justify-between items-center border border-zinc-200 dark:border-zinc-700">
                                <div>
                                    <p class="text-zinc-900 dark:text-white font-semibold text-sm md:text-base">{{ $part->name }}</p>
                                    <p class="text-zinc-500 dark:text-zinc-400 text-xs md:text-sm capitalize">{{ $part->category }}</p>
                                </div>
                                <button
                                    wire:click="removePart('{{ $category }}')"
                                    class="bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600 text-white px-2 md:px-3 py-1 rounded text-xs md:text-sm font-semibold transition"
                                >
                                    Remove
                                </button>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <!-- 2nd Div: Incompatibility Warnings -->
        <div class="bg-zinc-100 dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-3 md:p-4 overflow-y-auto">
            <h2 class="text-base md:text-lg font-bold text-zinc-900 dark:text-white mb-4">Compatibility</h2>
            @if (empty($this->incompatibilities))
                <p class="text-green-600 dark:text-green-400 font-semibold text-sm">✓ All parts compatible</p>
            @else
                <div class="space-y-2">
                    @foreach ($this->incompatibilities as $incomp)
                        <div wire:transition.opacity.delay.150ms class="bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-700 p-3 rounded-lg">
                            <p class="text-red-900 dark:text-red-300 font-semibold text-xs md:text-sm">{{ $incomp['part_name'] }}</p>
                            @foreach ($incomp['issues'] as $issue)
                                <p class="text-red-800 dark:text-red-400 text-xs mt-1">• {{ $issue }}</p>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- 3rd Div: Parts Catalog -->
    <div class="bg-zinc-100 dark:bg-zinc-900 h-max rounded-xl border border-zinc-200 dark:border-zinc-800 p-3 md:p-4 overflow-hidden flex flex-col">
        <div class="mb-4">
            <h2 class="text-base md:text-lg font-bold text-zinc-900 dark:text-white mb-3">Parts Catalog</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-3">
                <!-- Search Bar -->
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live="searchQuery"
                        placeholder="Search parts..."
                        class="w-full px-3 md:px-4 py-2 rounded-lg bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-500 dark:placeholder-zinc-400 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-[#1c0a00] dark:focus:ring-[#fff8f5]"
                    />
                    <svg class="absolute right-3 top-2.5 w-4 h-4 md:w-5 md:h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                <!-- Category Filter -->
                <select
                    wire:model.live="selectedCategory"
                    class="px-3 md:px-4 py-2 rounded-lg bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-[#1c0a00] dark:focus:ring-[#fff8f5]"
                >
                    <option value="">All Categories</option>
                    @foreach ($this->getCategories() as $category)
                        <option value="{{ $category }}">{{ ucfirst($category) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Catalog Grid -->
        <div wire:init="loadParts" class="overflow-y-auto flex-1">
            @if (!$this->loaded)
                <div class="flex flex-col items-center justify-center h-full text-zinc-500 dark:text-zinc-400">
                    <p class="text-sm">Loading parts... please wait.</p>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-2 md:gap-3">
                    @forelse ($this->getFilteredParts() as $part)
                        @php
                            $isSelected = $part->category &&
                                isset($this->selectedParts[$part->category]) &&
                                $this->selectedParts[$part->category] === $part->id;
                        @endphp
                        <div
                            wire:key="part-{{ $part->id }}"
                            wire:transition.opacity.delay.100ms
                            wire:transition.scale.duration.150ms
                            class="{{ \Illuminate\Support\Arr::toCssClasses([
                                'bg-white dark:bg-zinc-800 rounded-lg p-2 md:p-3 border-2 transition',
                                'border-[#1c0a00] dark:border-[#fff8f5]' => $isSelected,
                                'border-zinc-200 dark:border-zinc-700 hover:border-zinc-300 dark:hover:border-zinc-600' => !$isSelected,
                            ]) }}"
                        >
                            @if ($part->image)
                                <img
                                    src="{{ $part->image }}"
                                    alt="{{ $part->name }}"
                                    loading="lazy"
                                    class="w-full h-16 md:h-20 object-cover rounded"
                                />
                            @else
                                <div class="w-full h-16 md:h-20 bg-zinc-300 dark:bg-zinc-700 rounded flex items-center justify-center">
                                    <span class="text-zinc-500 dark:text-zinc-400 text-xs">No image</span>
                                </div>
                            @endif
                            <p class="text-zinc-900 dark:text-white text-xs md:text-sm font-semibold truncate">{{ $part->name }}</p>
                            <p class="text-zinc-600 dark:text-zinc-400 text-xs capitalize mb-2">{{ $part->category }}</p>
                            <button
                                wire:click="addPart({{ $part->id }})"
                                class="{{ \Illuminate\Support\Arr::toCssClasses([
                                    'w-full px-2 py-1 rounded text-xs font-semibold transition border',
                                    'bg-[#1c0a00] hover:bg-[#2d1507] text-[#f5ede8] border-[#1c0a00] dark:bg-[#fff8f5] dark:hover:bg-white dark:text-[#1c0a00] dark:border-[#fff8f5]' => $isSelected,
                                    'bg-zinc-200 hover:bg-zinc-300 text-zinc-900 border-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 dark:text-white dark:border-zinc-600' => !$isSelected,
                                ]) }}"
                            >
                                {{ $isSelected ? '✓ Selected' : 'Add' }}
                            </button>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-4">
                            <p class="text-zinc-500 dark:text-zinc-400 text-sm">No parts found</p>
                        </div>
                    @endforelse
                </div>
            @endif
        </div>
    </div>

    <!-- Save Modal -->
    @if ($this->showSaveModal)
        <div wire:transition.opacity.duration.200ms class="fixed inset-0 bg-black/50 dark:bg-black/70 flex items-center justify-center z-50 p-4">
            <div wire:transition.scale.duration.200ms class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 max-w-md w-full p-6 shadow-2xl">
                <h3 class="text-xl md:text-2xl font-bold text-zinc-900 dark:text-white mb-4">Save Your Bike Build</h3>

                <form wire:submit="saveBike" class="space-y-4">
                    <!-- Title Input -->
                    <div>
                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Bike Title *</label>
                        <input
                            type="text"
                            wire:model="title"
                            placeholder="e.g., My Trail Bike"
                            class="w-full px-4 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-500 dark:placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-[#1c0a00] dark:focus:ring-[#fff8f5]"
                        />
                        @error('title')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description Textarea -->
                    <div>
                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Description</label>
                        <textarea
                            wire:model="description"
                            placeholder="Add a description of your bike build..."
                            rows="4"
                            class="w-full px-4 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-500 dark:placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-[#1c0a00] dark:focus:ring-[#fff8f5] resize-none"
                        ></textarea>
                        @error('description')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3 pt-4">
                        <button
                            type="button"
                            wire:click="closeSaveModal"
                            class="flex-1 px-4 py-2 rounded-lg bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-900 dark:text-white font-semibold transition"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="flex-1 px-4 py-2 rounded-lg bg-[#1c0a00] hover:bg-[#2d1507] text-[#f5ede8] dark:bg-[#fff8f5] dark:hover:bg-white dark:text-[#1c0a00] font-semibold transition border border-[#1c0a00] dark:border-[#fff8f5]"
                        >
                            Save Bike
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>