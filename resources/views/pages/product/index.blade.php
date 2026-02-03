<?php

use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\{Component, WithPagination};

new class extends Component {

    use WithPagination;

    public string|null $tag = null;
    public string|null $search = null;

    public string $sortBy = 'date';
    public string $sortDirection = 'desc';

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function set_tag(?string $tag)
    {

        $this->tag = $tag;
    }

    #[Computed]
    public function products()
    {
        return Product::query()
            ->tap(function ($query) {
                if ($this->tag) {
                    $query->where("category", $this->tag == '/' ? null : $this->tag);
                    $this->resetPage();


                }

                if ($this->search) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                    $this->resetPage();

                }

                $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query;

                return $query;
            })
            ->paginate(25);
    }

    public function clear(): void
    {
        $this->reset();
    }
};
?>

<section wire:keydown.window.escape="clear" class="space-y-4 ml-2 h-screen overflow-y-hidden">


    <flux:heading size="xl" level="1">{{ __('Products') }}</flux:heading>

    <div class="flex justify-between">
        <div class="space-y-4">

        <flux:field class="flex items-center">

            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search orders" clearable size="sm">
                <x-slot name="iconLeading">

                <flux:label>

                    <flux:icon.magnifying-glass class="size-5"/>
                </flux:label>
                </x-slot>

            </flux:input>
        </flux:field>
            @if($tag)
                {{--        <flux:badge as="button" wire:click="set_tag(null)"><flux:badge.close/>{{$tag}}</flux:badge>--}}
                <flux:badge wire:click="set_tag(null)" class="mb-2 cursor-pointer hover:bg-zinc-600"
                >
                    {{$tag}}
                    <flux:badge.close/>
                </flux:badge>
            @endif
        </div>

        <flux:button variant="primary" icon="plus">Add product</flux:button>

    </div>


    <flux:table class="table-fixed w-full" container:class="h-4/5" :paginate="$this->products">
        <flux:table.columns sticky class="bg-zinc-800">
            <flux:table.column align="center" class="w-12 border-r">#</flux:table.column>
            <flux:table.column align="center" sortable :sorted="$sortBy === 'name'" :direction="$sortDirection"
                               wire:click="sort('name')">Name
            </flux:table.column>
            <flux:table.column align="center">Tag</flux:table.column>
            <flux:table.column align="center" sortable :sorted="$sortBy === 'current_stock'" :direction="$sortDirection"
                               wire:click="sort('current_stock')">Quantity
            </flux:table.column>

            <flux:table.column align="center">Price</flux:table.column>

            <flux:table.column align="center" sortable :sorted="$sortBy === 'date'" :direction="$sortDirection"
                               wire:click="sort('date')">Date
            </flux:table.column>
            <flux:table.column align="center">Actions</flux:table.column>


        </flux:table.columns>

        <flux:table.rows >
            @foreach ($this->products as $index => $product)
                <flux:table.row class='hover:bg-zinc-700' :key="$product->id">
                    <flux:table.cell align="center" class="border-r w-12 font-medium">
                        {{ ($this->products->currentPage() - 1) * 25 + $index + 1 }}
                    </flux:table.cell>


                    <flux:table.cell align="center" class="flex items-center gap-3">
                        {{ $product->name }}
                    </flux:table.cell>

                    <flux:table.cell align="center">
                        <flux:badge class="cursor-pointer hover:bg-zinc-600"
                                    wire:click="set_tag('{{$product->category ?? '/'}}')" size="sm"
                                    :color="$product->status_color" inset="top bottom">
                            {{ $product->category ?? "/" }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell align="center">{{ $product->current_stock }}</flux:table.cell>


                    <flux:table.cell align="center">{{ $product->price }} {{$product->currency}}</flux:table.cell>

                    <flux:table.cell align="center">
                        {{$product->created_at->format('M d, Y')}}

                    </flux:table.cell>
                    <flux:table.cell align="center" class="flex gap-2 justify-center">
                        <flux:button.group>
                            <flux:modal.trigger :name='"edit-product-$product->id"' >
                                <flux:button class="cursor-pointer" size="xs">Edit</flux:button>
                            </flux:modal.trigger>
                            <flux:modal.trigger :name='"delete-product-$product->id"'>
                                <flux:button variant="danger" class="cursor-pointer" color="rose" size="xs">Delete</flux:button>
                            </flux:modal.trigger>
                        </flux:button.group>

                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>


    @foreach ($this->products as $product)
        <flux:modal :name="'edit-product-'.$product->id" flyout position="right">
            <div class="space-y-6">
                <flux:heading size="lg">Update product</flux:heading>
                <flux:subheading>Make changes to the product details.</flux:subheading>
                <flux:input label="Name" :placeholder="$product->name" />
                <flux:textarea label="Description" type="type" :placeholder="$product->description"/>
            </div>
        </flux:modal>

        <flux:modal :name="'delete-product-'.$product->id" class="min-w-88">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Delete project?</flux:heading>
                    <flux:text class="mt-2">
                        You're about to delete this project.<br>
                        This action cannot be reversed.
                    </flux:text>
                </div>
                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="danger">Delete project</flux:button>
                </div>
            </div>
        </flux:modal>
    @endforeach
</section>
