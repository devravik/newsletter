<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Contacts') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg text-xs">
            <div class="mx-auto sm:px-6 lg:px-8 space-y-6 py-4">

                {{-- Search and Filter Form --}}
                <form method="GET" action="{{ route('contacts.index') }}" class="mb-4">
                    <div class="flex space-x-4 items-center">
                        {{-- Search Input --}}
                        <div class="w-1/2">
                            <input
                                type="text"
                                name="search"
                                placeholder="Search by name, email, or phone"
                                value="{{ request('search') }}"
                                class="w-full px-2 py-1 bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 rounded-md" />
                        </div>

                        {{-- Source Filter --}}
                        <div class="w-1/4">
                            <input
                                type="text"
                                name="source"
                                placeholder="Source"
                                value="{{ request('source') }}"
                                class="w-full px-2 py-1 bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 rounded-md" />
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex items-end">
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Search</button>
                            <button type="button" id="clear-filters" class="ml-2 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Clear</button>
                        </div>
                    </div>
                </form>

                {{-- Contacts Table --}}
                <table class="table-fixed text-white w-full border-collapse border border-slate-500">
                    <thead>
                        <tr class="text-left border-collapse border border-slate-300">
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Source</th>
                            <th>Country</th>
                            <th>City</th>
                            <th>Job Title</th>
                            <th>Company</th>
                            <th>Address</th>
                            <th>Postal Code</th>
                            <th>Website</th>
                            <th>Notes</th>
                            <th>Status</th>
                            <th>Metas</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contacts as $contact)
                        <tr class="text-left">
                            <td>{{ $contact->name ?? $contact->first_name.' '.$contact->last_name  }}</td>
                            <td>{{ $contact->email }}</td>
                            <td>{{ $contact->phone }}</td>
                            <td>{{ $contact->source }}</td>
                            <td>{{ $contact->country }}</td>
                            <td>{{ $contact->city }}</td>
                            <td>{{ $contact->job_title }}</td>
                            <td>{{ $contact->company }}</td>
                            <td>{{ $contact->address }}</td>
                            <td>{{ $contact->postal_code }}</td>
                            <td>{{ $contact->website }}</td>
                            <td>{{ $contact->notes }}</td>
                            <td>{{ $contact->status }}</td>
                            <!-- Add metas values -->
                            <td>
                                @foreach ($contact->metas as $meta)
                                <span class="text-xs bg-gray-200 dark:bg-gray-700 dark:text-gray-400 rounded-lg px-2 py-1 mr-1">{{ $meta->key }}: {{ $meta->value }}</span>
                                @endforeach
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex space-x-2">
                                    {{-- Edit Button --}}
                                    <a href="{{ route('contacts.edit', $contact->id) }}" class="px-2 py-1 bg-green-500 text-white rounded">Edit</a>

                                    {{-- Delete Form --}}
                                    <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this contact?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                <x-pagination :pagination="$contacts" />
            </div>
        </div>
    </div>
    <script>
        document.getElementById('clear-filters').addEventListener('click', function() {
            const form = this.closest('form');
            
            form.querySelectorAll('input').forEach(input => input.value = ''); // To clear all input values

            form.submit(); // To trigger a form submission with cleared values
        });
    </script>
</x-app-layout>