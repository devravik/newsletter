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
                <form method="GET" action="{{ route('campaign_mails.index') }}" class="mb-4">
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
                            <th>Email</th>
                            <th>Status</th>
                            <th>Subject</th>
                            <th>From Name</th>
                            <th>From Email</th>
                            <th>Template</th>
                            <th>Reply To</th>
                            <th>Scheduled At</th>
                            <th>Sent At</th>
                            <th>Opened At</th>
                            <th>Unsubscribed At</th>
                            <th>Is Bounced</th>                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mails as $mail)
                        <tr class="text-left">
                            <td>{{ $mail->email }}</td>
                            <td>{{ ucwords($mail->status) }}</td>
                            <td>{{ $mail->subject }}</td>
                            <td>{{ $mail->from_name }}</td>
                            <td>{{ $mail->from_email }}</td>
                            <td>{{ $mail->template }}</td>
                            <td>{{ $mail->reply_to }}</td>
                            <td>{{ $mail->scheduled_at }}</td>
                            <td>{{ $mail->sent_at }}</td>
                            <td>{{ $mail->opened_at }}</td>
                            <td>{{ $mail->unsubscribed_at }}</td>
                            <td>{{ $mail->is_bounced ? 'Yes' : 'No' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                <x-pagination :pagination="$mails" />
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