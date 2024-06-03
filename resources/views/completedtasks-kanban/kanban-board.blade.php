<x-filament-panels::page>
    <span class="text-slate-200 text-[12px]">Drag this sH.***</span>
        <div class="">
            <div x-data wire:ignore.self >
                    @foreach($statuses as $status)
              
        
                    @include(static::$statusView)
                    <hr>
                    @endforeach
                
        
                <div wire:ignore>
                    @include(static::$scriptsView)
                </div>
            </div>
        </div>
        
            @unless($disableEditModal)
                <x-filament-kanban::edit-record-modal/>
            @endunless
        </x-filament-panels::page>
