<?php

use Livewire\Component;

new class extends Component {
    //
};

?>
@props([
    'close' => false,
])


<flux:badge {{$attributes->except('text')}} class="cursor-pointer hover:bg-zinc-600" size="sm"
            inset="top bottom">
    {{$slot}}
    @if($close)
        <flux:badge.close/>
    @endif
</flux:badge>

