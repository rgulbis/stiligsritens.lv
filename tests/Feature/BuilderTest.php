<?php

use App\Models\Part;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('renders the builder page and adds a part', function () {
    $this->actingAs($this->user);

    // Ensure at least one part exists
    $part = Part::first() ?? Part::factory()->create();

    Livewire::test('pages::builder.create')
        ->assertSee('Selected Parts')
        ->assertSee('Parts Catalog')
        ->call('loadParts')
        ->call('addPart', $part->id)
        ->assertSee($part->name);
});

it('renders the showcase page and shows my saved projects link', function () {
    $this->actingAs($this->user);

    Livewire::test('pages::showcase.index')
        ->assertSee('All Projects')
        ->assertSee('My Saved');
});
