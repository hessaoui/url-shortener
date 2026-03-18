<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Short Link') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('links.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="original_url" :value="__('Original URL')" />
                            <x-text-input
                                id="original_url"
                                name="original_url"
                                type="url"
                                class="mt-1 block w-full"
                                :value="old('original_url')"
                                placeholder="https://example.com/very/long/link"
                                required
                                autofocus
                            />
                            <x-input-error :messages="$errors->get('original_url')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('links.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                                {{ __('Cancel') }}
                            </a>

                            <x-primary-button>
                                {{ __('Create') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
