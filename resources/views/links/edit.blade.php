<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Link') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">
                    <div class="p-4 rounded-md bg-gray-50 border border-gray-200">
                        <p class="text-sm text-gray-600">{{ __('Short URL') }}</p>
                        <p class="text-sm text-indigo-700 break-all">{{ route('redirect.show', $link->code) }}</p>
                    </div>

                    <form method="POST" action="{{ route('links.update', $link) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="original_url" :value="__('Original URL')" />
                            <x-text-input
                                id="original_url"
                                name="original_url"
                                type="url"
                                class="mt-1 block w-full"
                                :value="old('original_url', $link->original_url)"
                                required
                                autofocus
                            />
                            <x-input-error :messages="$errors->get('original_url')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <a href="{{ route('links.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                                {{ __('Back to list') }}
                            </a>

                            <x-primary-button>
                                {{ __('Save') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
