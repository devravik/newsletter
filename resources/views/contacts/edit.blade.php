<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Contact') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 py-4">
                {{-- Display Validation Errors --}}
                @if ($errors->any())
                <div class="mb-4 bg-red-100 text-red-700 border border-red-300 rounded-lg p-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Edit Contact Form --}}
                <form method="POST" action="{{ route('contacts.update', $contact->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        {{-- Dynamic Fields --}}
                        @foreach ($contact->getFillable() as $field)
                        <div>
                            <label for="{{ $field }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ ucfirst($field) }}</label>
                            @if ($field === 'email') {{-- Specific field type --}}
                            <input type="email" id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $contact->$field) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm dark:bg-gray-900 dark:text-gray-100" required>
                            @elseif ($field === 'phone') {{-- Specific field type --}}
                            <input type="text" id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $contact->$field) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm dark:bg-gray-900 dark:text-gray-100">
                            @else {{-- General text input --}}
                            <input type="text" id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $contact->$field) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm dark:bg-gray-900 dark:text-gray-100">
                            @endif
                        </div>
                        @endforeach

                        {{-- Meta Fields --}}
                        <div id="meta-fileds" class="space-y-4">
                            <label for="Additional" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Additional</label>
                            @foreach ($contact->metas as $meta)
                            <div class="flex space-x-4 items-center">
                                <input type="hidden" name="meta_ids[]" value="{{ $meta->id }}">
                                <input type="text" name="meta_keys[]" value="{{ old('meta_keys[]', $meta->key) }}" placeholder="Meta Key" class="mt-1 block w-1/3 border-gray-300 dark:border-gray-700 rounded-md shadow-sm dark:bg-gray-900 dark:text-gray-100">
                                <input type="text" name="meta_values[]" value="{{ old('meta_values[]', $meta->value) }}" placeholder="Meta Value" class="mt-1 block w-2/3 border-gray-300 dark:border-gray-700 rounded-md shadow-sm dark:bg-gray-900 dark:text-gray-100">
                                <button type="button" class="remove-meta px-2 py-1 bg-red-500 text-white rounded-md hover:bg-red-600">Remove</button>
                            </div>
                            @endforeach
                        </div>

                        {{-- Add New Meta Field --}}
                        <div class="mt-4">
                            <button type="button" id="add-meta" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">Add New Meta Field</button>
                        </div>

                        {{-- Submit Button --}}
                        <div class="mt-4">
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Update Contact</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script to handle adding/removing meta fields --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const metaContainer = document.getElementById('meta-fileds');
            document.getElementById('add-meta').addEventListener('click', function() {
                const newMetaField = document.createElement('div');
                newMetaField.classList.add('flex', 'space-x-4', 'items-center');
                newMetaField.innerHTML = `
                    <input type="hidden" name="meta_ids[]" value="">
                    <input type="text" name="meta_keys[]" placeholder="Meta Key" class="mt-1 block w-1/3 border-gray-300 dark:border-gray-700 rounded-md shadow-sm dark:bg-gray-900 dark:text-gray-100">
                    <input type="text" name="meta_values[]" placeholder="Meta Value" class="mt-1 block w-2/3 border-gray-300 dark:border-gray-700 rounded-md shadow-sm dark:bg-gray-900 dark:text-gray-100">
                    <button type="button" class="remove-meta px-2 py-1 bg-red-500 text-white rounded-md hover:bg-red-600">Remove</button>
                `;
                metaContainer.appendChild(newMetaField);
            });

            metaContainer.addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-meta')) {
                    event.target.parentElement.remove();
                }
            });
        });
    </script>
</x-app-layout>