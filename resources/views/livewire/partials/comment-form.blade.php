<form class="" wire:submit="{{$method}}">
    @if (session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
            role="alert">
            <span class="font-medium">Success!</span> {{session('message')}}
        </div>
    </div>
    @endif
    @csrf
    <div class="flex">
        <div class="py-2 px-4 mb-4 bg-white rounded-lg rounded-t-lg border border-gray-200
    dark:bg-gray-800 dark:border-gray-700 w-full">
            <label for="{{$inputId}}" class="sr-only">{{$inputLabel}}</label>
            {{-- <textarea id="{{$inputId}}" rows="1" class="px-0 w-full text-sm text-gray-900 border-0 focus:ring-0 focus:outline-none
                              dark:text-white dark:placeholder-gray-400 dark:bg-gray-800 @error($state.'.body')
                              border-red-500 @enderror" placeholder="Schrijf iets ..."
                wire:model.live="{{$state}}.body" oninput="detectAtSymbol()"></textarea> --}}

            <input type="text" class="px-0 w-full text-sm text-gray-900 border-0 focus:ring-0 focus:outline-none
                              dark:text-white dark:placeholder-gray-400 dark:bg-gray-800 @error($state.'.body')
                              border-red-500 @enderror" placeholder="{{ $quote }}" wire:model.live="{{$state}}.body"
                oninput="detectAtSymbol()" @if(!empty($users) && $users->count()
            > 0)
            @include('commentify::livewire.partials.dropdowns.users')
            @endif
            {{-- @error($state.'.body')
            <p class="mt-2 text-sm text-red-600">
                {{$message}}
            </p>
            @enderror --}}
        </div>
        <div class="ms-2">
            <x-action-button type="submit" action="{{$method}}" icon="far-comment-dots" title="Post"
                title_saving="Post" />
            {{-- <button wire:loading.attr="disabled" type="submit"
                class="inline-flex items-center py-2.5 px-4 text-xs font-medium text-center text-white bg-blue-700 rounded-lg focus:ring-4 focus:ring-primary-200 dark:focus:ring-primary-900 hover:bg-primary-800">
                <div wire:loading wire:target="{{$method}}">
                    @include('commentify::livewire.partials.loader')
                </div>
                {{$button}}
            </button> --}}
        </div>
    </div>
</form>
