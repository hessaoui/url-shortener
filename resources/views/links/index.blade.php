<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Short Links') }}
            </h2>

            <a
                href="{{ route('links.create') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
                {{ __('Create Link') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 p-4 rounded-md bg-green-100 text-green-800 border border-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($links->isEmpty())
                        <p class="text-gray-600">{{ __('No links yet. Create your first short URL.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Short URL') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Original URL') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Clicks') }}</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Last Used') }}</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($links as $link)
                                        <tr>
                                            <td class="px-4 py-4 align-top">
                                                @php($shortUrl = route('redirect.show', $link->code))
                                                <a href="{{ $shortUrl }}" target="_blank" rel="noopener" class="text-indigo-600 hover:text-indigo-800 break-all">
                                                    {{ $shortUrl }}
                                                </a>

                                                <div class="mt-2">
                                                    <button
                                                        type="button"
                                                        class="js-copy-short-url inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                                        data-short-url="{{ $shortUrl }}"
                                                        data-default-text="{{ __('Copy') }}"
                                                    >
                                                        {{ __('Copy') }}
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-700 align-top break-all">
                                                {{ $link->original_url }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-700 align-top">
                                                {{ number_format($link->clicks) }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-700 align-top">
                                                {{ $link->last_used_at ? $link->last_used_at->diffForHumans() : __('Never') }}
                                            </td>
                                            <td class="px-4 py-4 align-top">
                                                <div class="flex justify-end gap-2">
                                                    <a
                                                        href="{{ route('links.edit', $link) }}"
                                                        class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                                    >
                                                        {{ __('Edit') }}
                                                    </a>

                                                    <form method="POST" action="{{ route('links.destroy', $link) }}" onsubmit="return confirm('{{ __('Delete this link?') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-danger-button>
                                                            {{ __('Delete') }}
                                                        </x-danger-button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $links->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
