@props(['record'])

<div id="{{ $record->getKey() }}" wire:click="recordClicked('{{ $record->getKey() }}', {{ @json_encode($record) }})"
    class="record flex flex-col {{ $record->bg_color }} dark:bg-gray-700 rounded-md p-4 cursor-grab  dark:text-gray-200 border-l-8 shadow-md border-slate-300 
    {{ $record->text_color }} justify-between mb-3"
    @if ($record->timestamps && now()->diffInSeconds($record->{$record::UPDATED_AT}) < 3) x-data x-init="
            $el.classList.add('animate-pulse-twice', 'bg-primary-100', 'dark:bg-primary-800')
            $el.classList.remove('bg-white', 'dark:bg-gray-700')
            setTimeout(() => {
                $el.classList.remove('bg-primary-100', 'dark:bg-primary-800')
                $el.classList.add('{{ $record->bg_color }}', 'dark:bg-gray-700')
            }, 3000)
        " @endif>
       
    <div class="flex flex-col justify-between mb-2">

        <div class="flex flex-row items-center gap-x-2">
            @if ($record['urgent'])
                <x-heroicon-s-star class="top-0 right-0 {{ $record->text_color }} w-5 h-5 inline-block" />
            @endif
            <div class="text-balance">

                {{ $record->{static::$recordTitleAttribute} }}
            </div>


        </div>
        <div class="flex flex-row gap-x-2 my-2">
            <div
                class="w-8 h-8 rounded-full shadow shadow-gray-400 {{ $record->bg_color }} border-2 border-white pt-[2px] text-center items-center">
                <span class="text-sm">{{ strtoupper($record->user?->name[0]) }}</span>
            </div>
            <div>
                <div class="font-light text-sm">
                    {{ $record->user->name ?? 'No Owner' }}
                </div>
                <div class="font-light text-xs">
                    Created {{ $record->created_at->diffForHumans() }}
                </div>
            </div>
        </div>
    </div>
    <div class=" text-xs font-light border-l-2 border-slate-200 pl-2 mb-2">
        {{ $record->getTrim() ?? 'No Description' }}
    </div>

    <div class="flex flex-row hover:space-x-1 -space-x-2 mb-2 ">
        @foreach ($record['team'] as $member)
            <div
                class="w-6 h-6 rounded-full shadow shadow-gray-400 {{ $record->bg_color }} border-2 border-white pt-[2px] text-center items-center transition-all ease-in-out group relative">
                <span class="text-xs text-center absolute -translate-x-1/2">{{ strtoupper($member->name[0]) }}</span>
                <span
                    class="group-hover:opacity-100 transition-opacity bg-slate-300 p-2 text-sm text-slate-600 rounded-md absolute left-1/2 
            -translate-x-1/2 translate-y-1/3 opacity-0 m-4 mx-auto z-10 block w-[100px]">{{ $member->name }}</span>
            </div>
        @endforeach


    </div>
    <span class="text-xs font-light m-y-5"> {{ $record->progress }}% Progress</span>
    <div class="h-3 w-full relative">
        <div class="h-1 bg-gray-200 rounded-full absolute w-full"></div>
        <div class="h-1 absolute rounded-full bg-blue-600 " style="width: {{ $record->progress }}%"></div>
    </div>

    <div class="font-light text-xs">
        Updated {{ $record->updated_at->diffForHumans() }}

    </div>

    

    {{-- <button wire:click="deleteRecord('{{ $record->id }}')" class="delete-btn">Delete</button> --}}
</div>
