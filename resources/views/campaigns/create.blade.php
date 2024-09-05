<!-- resources/views/campaigns/create.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Campaign') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                <!-- Show Errors -->
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Holy smokes!</strong>
                        <span class="block sm:inline">Something went wrong. Please check your form.</span>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                <!-- Create Campaign Form -->


                <form action="{{ route('campaigns.store') }}" method="POST">
                    @csrf

                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                        <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" value="{{ old('name') }}" required>
                    </div>

                    <!-- Subject -->
                    <div class="mb-4">
                        <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subject</label>
                        <input type="text" name="subject" id="subject" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" value="{{ old('subject') }}" required>
                    </div>

                    <!-- From Name -->
                    <div class="mb-4">
                        <label for="from_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">From Name</label>
                        <input type="text" name="from_name" id="from_name" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" value="{{ old('from_name') }}" required>
                    </div>

                    <!-- From Email -->
                    <div class="mb-4">
                        <label for="from_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">From Email</label>
                        <input type="email" name="from_email" id="from_email" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" value="{{ old('from_email') }}" required>
                    </div>

                    <!-- Reply-To -->
                    <div class="mb-4">
                        <label for="reply_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reply-To Email</label>
                        <input type="email" name="reply_to" id="reply_to" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" value="{{ old('reply_to') }}">
                    </div>

                    <!-- Sent At -->
                    <div class="mb-4">
                        <label for="sent_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sent At</label>
                        <input type="datetime-local" name="sent_at" id="sent_at" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" value="{{ old('sent_at') }}">
                    </div>

                    <!-- Template -->
                    <div class="mb-4">
                        <label for="template" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Template</label>
                        <!-- Get select options -->
                        <select name="template" id="template" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            <option value="">Select a template</option>
                            @foreach ($templates as $template)
                                <option value="{{ $template }}">{{ $template }}</option>
                            @endforeach
                        </select>
                        <!-- <input type="text" name="template" id="template" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" value="{{ old('template') }}"> -->
                    </div>

                    <!-- Content -->
                    <div class="mb-4">
                        <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Content</label>
                        <textarea name="content" id="content" rows="5" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">{{ old('content') }}</textarea>
                    </div>


                    <!-- Contact Filters -->
                    <div class="mb-4">
                        <label for="contact_filters" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contact Filters</label>
                        <div id="contact-filters-container">
                            <div class="mb-2 flex">
                                <input type="text" name="contact_filters[0][key]" placeholder="Key" class="form-input block w-1/2 mt-1 rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 mr-2">
                                <input type="text" name="contact_filters[0][value]" placeholder="Value" class="form-input block w-1/2 mt-1 rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            </div>
                        </div>
                        <button type="button" class="add-row bg-green-500 text-white px-2 py-1 rounded-md text-sm" data-target="#contact-filters-container">Add Filter</button>
                    </div>

                    <!-- Meta -->
                    <div class="mb-4">
                        <label for="meta" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Meta</label>
                        <div id="meta-container">
                            <div class="mb-2 flex">
                                <input type="text" name="meta[0][key]" placeholder="Key" class="form-input block w-1/2 mt-1 rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 mr-2">
                                <input type="text" name="meta[0][value]" placeholder="Value" class="form-input block w-1/2 mt-1 rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            </div>
                        </div>
                        <button type="button" class="add-row bg-green-500 text-white px-2 py-1 rounded-md text-sm" data-target="#meta-container">Add Meta</button>
                    </div>

                    <!-- Options -->
                    <div class="mb-4">
                        <label for="options" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Options</label>
                        <div id="options-container">
                            <div class="mb-2 flex">
                                <input type="text" name="options[0][key]" placeholder="Key" class="form-input block w-1/2 mt-1 rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 mr-2">
                                <input type="text" name="options[0][value]" placeholder="Value" class="form-input block w-1/2 mt-1 rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            </div>
                        </div>
                        <button type="button" class="add-row bg-green-500 text-white px-2 py-1 rounded-md text-sm" data-target="#options-container">Add Option</button>
                    </div>

                    <!-- Settings -->
                    <div class="mb-4">
                        <label for="settings" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Settings</label>
                        <div id="settings-container">
                            <div class="mb-2 flex">
                                <input type="text" name="settings[0][key]" placeholder="Key" class="form-input block w-1/2 mt-1 rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 mr-2">
                                <input type="text" name="settings[0][value]" placeholder="Value" class="form-input block w-1/2 mt-1 rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            </div>
                        </div>
                        <button type="button" class="add-row bg-green-500 text-white px-2 py-1 rounded-md text-sm" data-target="#settings-container">Add Setting</button>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700">
                            Create Campaign
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.add-row').forEach(button => {
            button.addEventListener('click', function() {
                const target = document.querySelector(this.dataset.target);
                const index = target.children.length;
                const newRow = `
                    <div class="mb-2 flex">
                        <input type="text" name="${target.id}[${index}][key]" placeholder="Key" class="form-input block w-1/2 mt-1 rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 mr-2">
                        <input type="text" name="${target.id}[${index}][value]" placeholder="Value" class="form-input block w-1/2 mt-1 rounded-md shadow-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                    </div>
                `;
                target.insertAdjacentHTML('beforeend', newRow);
            });
        });
    </script>
</x-app-layout>