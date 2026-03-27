<?php

use Livewire\Component;
use App\Models\Bike;

new class extends Component
{
    public $bikes = [];
    public $loaded = false;

    public function mount()
    {
        // bikes will lazy load via wire:init in the view
    }

    public function loadBikes()
    {
        if ($this->loaded) {
            return;
        }

        $this->bikes = Bike::with('user')->latest()->get();
        $this->loaded = true;
    }

    public function deleteBike(int $bikeId)
    {
        $bike = Bike::find($bikeId);

        if (!$bike || auth()->id() !== $bike->user_id) {
            session()->flash('error', 'You are not authorized to delete this bike.');
            return;
        }

        $bike->delete();
        $this->bikes = Bike::with('user')->latest()->get();
        session()->flash('success', 'Bike project deleted successfully.');
    }

    public function editBike(int $bikeId)
    {
        $bike = Bike::find($bikeId);

        if (!$bike || auth()->id() !== $bike->user_id) {
            session()->flash('error', 'You are not authorized to edit this bike.');
            return;
        }

        return redirect()->route('builder', ['bike' => $bikeId]);
    }
};
?>

<div class="p-4 min-h-screen bg-white dark:bg-neutral-900 text-zinc-800 dark:text-zinc-100">
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl md:text-3xl font-bold">Bike Showcase</h1>
            <a href="{{ route('builder') }}" class="text-sm md:text-base font-semibold px-4 py-2 rounded-lg bg-[#1c0a00] text-[#f5ede8] hover:bg-[#2d1507] dark:bg-[#fff8f5] dark:text-[#1c0a00] dark:hover:bg-white">Create New Bike</a>
        </div>

        @if (session()->has('success'))
            <div class="mb-4 px-4 py-2 rounded-lg bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-4 px-4 py-2 rounded-lg bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200">
                {{ session('error') }}
            </div>
        @endif

        <div wire:init="loadBikes" class="grid gap-4 md:grid-cols-2">
            @if (!$this->loaded)
                <div class="col-span-full p-8 text-center text-zinc-500 dark:text-zinc-400">
                    Loading bike projects...
                </div>
            @else
                @forelse ($this->bikes as $bike)
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-lg font-bold">{{ $bike->title }}</h2>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">by {{ $bike->user->name ?? 'Unknown' }} · {{ $bike->created_at->diffForHumans() }}</p>
                        </div>
                        @if (auth()->id() === $bike->user_id)
                            <div class="flex gap-2">
                                <button wire:click="editBike({{ $bike->id }})" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-600 hover:bg-blue-700 text-white">Edit</button>
                                <button wire:click="deleteBike({{ $bike->id }})" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-600 hover:bg-red-700 text-white">Delete</button>
                            </div>
                        @endif
                    </div>

                    @if ($bike->description)
                        <p class="mt-3 text-sm text-zinc-700 dark:text-zinc-300">{{ $bike->description }}</p>
                    @endif

                    @if (is_array($bike->parts) && count($bike->parts) > 0)
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($bike->parts as $category => $partId)
                                <span class="px-2 py-1 text-xs rounded-md bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200">{{ ucfirst($category) }}: #{{ $partId }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">No parts stored yet.</p>
                    @endif
                </div>
            @empty
                    <div class="p-6 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900">
                        <p class="text-zinc-600 dark:text-zinc-400">No bike projects yet. Save a new bike from the builder.</p>
                    </div>
                @endforelse
            @endif
        </div>
    </div>
</div>