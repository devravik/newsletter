<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Campaigns') }}
        </h2>
        <!-- Add Create action -->
        <a href="{{ route('campaigns.create') }}" class="text-blue-500 hover:text-blue-600">Create</a>
    </x-slot>

    <div class="py-12">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg text-xs">
            <div class="mx-auto sm:px-6 lg:px-8 space-y-6 py-4">

                {{-- Search and Filter Form --}}
                <form method="GET" action="{{ route('campaigns.index') }}" class="mb-4">
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

                        <!-- Add Status Filter -->
                        <div class="w-1/4">
                            <select name="status" class="w-full px-2 py-1 bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 rounded-md">
                                <option value="">Select Status</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Sent</option>
                            </select>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex items-end">
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Search</button>
                            <button type="button" id="clear-filters" class="ml-2 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Clear</button>
                        </div>
                    </div>
                </form>

                {{-- Campaigns Table --}}
                <table class="table-fixed text-black dark:text-white  w-full border-collapse border border-slate-500">
                    <thead>
                        <tr class="text-left border-collapse border border-slate-300">
                            <th>Name</th>
                            <th>Subject</th>
                            <th>From Name</th>
                            <th>From Email</th>
                            <th>Reply To</th>
                            <th>Status</th>
                            <th>Sent At</th>
                            <th>Template</th>
                            <th>Content</th>
                            <th>Contact Filters</th>
                            <th>Meta</th>
                            <th>Options</th>
                            <th>Settings</th>
                            <th>Report</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($campaigns as $campaign)
                        <tr class="text-left">
                            <td>{{ $campaign->name }}</td>
                            <td>{{ $campaign->subject }}</td>
                            <td>{{ $campaign->from_name }}</td>
                            <td>{{ $campaign->from_email }}</td>
                            <td>{{ $campaign->reply_to }}</td>
                            <td>{{ $campaign->status }}</td>
                            <td>{{ $campaign->sent_at }}</td>
                            <td>{{ $campaign->template }}</td>
                            <td>{{ $campaign->content }}</td>
                            <td>
                                @if ($campaign->contact_filters)
                                @foreach ($campaign->contact_filters as $key => $value)
                                <div>{{ $key }}: {{ $value }}</div>
                                @endforeach
                                @endif
                            </td>
                            <td>
                                @if ($campaign->meta)
                                @foreach ($campaign->meta as $key => $value)
                                <div>{{ $key }}: {{ $value }}</div>
                                @endforeach
                                @endif
                            </td>
                            <td>
                                @if ($campaign->options)
                                @foreach ($campaign->options as $key => $value)
                                <div>{{ $key }}: {{ $value }}</div>
                                @endforeach
                                @endif
                            </td>
                            <td>
                                @if ($campaign->settings)
                                @foreach ($campaign->settings as $key => $value)
                                <div>{{ $key }}: {{ $value }}</div>
                                @endforeach
                                @endif
                            </td>
                            <td>
                                @if ($campaign->report)
                                @foreach ($campaign->report as $key => $value)
                                <div>{{ $key }}: {{ $value }}</div>
                                @endforeach
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('campaigns.show', $campaign) }}" class="text-blue-500 hover:text-blue-600 block">View</a>
                                <a href="{{ route('email.view', $campaign->template) }}" class="text-blue-500 hover:text-blue-600 block" target="_blank">Template</a>
                                <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-600">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                <x-pagination :pagination="$campaigns" />
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