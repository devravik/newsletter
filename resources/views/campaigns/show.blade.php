<!-- resources/views/campaigns/show.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Campaign Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-200">Campaign: {{ $campaign->name }}</h3>

                <div class="mb-4">
                    <strong class="text-gray-800 dark:text-gray-300">Subject:</strong> {{ $campaign->subject }}
                </div>

                <div class="mb-4">
                    <strong class="text-gray-800 dark:text-gray-300">From Name:</strong> {{ $campaign->from_name }}
                </div>

                <div class="mb-4">
                    <strong class="text-gray-800 dark:text-gray-300">From Email:</strong> {{ $campaign->from_email }}
                </div>

                <div class="mb-4">
                    <strong class="text-gray-800 dark:text-gray-300">Reply To:</strong> {{ $campaign->reply_to ?? 'N/A' }}
                </div>

                <div class="mb-4">
                    <strong class="text-gray-800 dark:text-gray-300">Status:</strong> {{ ucfirst($campaign->status) }}
                </div>

                <div class="mb-4">
                    <strong class="text-gray-800 dark:text-gray-300">Sent At:</strong> {{ $campaign->sent_at ? $campaign->sent_at->format('d M Y, h:i A') : 'Not Sent' }}
                </div>

                <div class="mb-4">
                    <strong class="text-gray-800 dark:text-gray-300">Template:</strong> {{ $campaign->template ?? 'No template selected' }}
                </div>

                <div class="mb-4">
                    <strong class="text-gray-800 dark:text-gray-300">Content:</strong>
                    <p class="text-gray-700 dark:text-gray-400">{{ $campaign->content ?? 'No content provided' }}</p>
                </div>

                <!-- Contact Filters -->
                <div class="mb-4">
                    <strong class="text-gray-800 dark:text-gray-300">Contact Filters:</strong>
                    <ul class="text-gray-700 dark:text-gray-400">
                        @if(!empty($campaign->contact_filters))
                            @foreach ($campaign->contact_filters as $key => $value)
                                <li><strong>{{ $key }}:</strong> {{ $value }}</li>
                            @endforeach
                        @else
                            <li>No contact filters set</li>
                        @endif
                    </ul>
                </div>

                <!-- Meta -->
                <div class="mb-4">
                    <strong class="text-gray-800 dark:text-gray-300">Meta:</strong>
                    <ul class="text-gray-700 dark:text-gray-400">
                        @if(!empty($campaign->meta))
                            @foreach ($campaign->meta as $key => $value)
                                <li><strong>{{ $key }}:</strong> {{ $value }}</li>
                            @endforeach
                        @else
                            <li>No meta information available</li>
                        @endif
                    </ul>
                </div>

                <!-- Options -->
                <div class="mb-4">
                    <strong class="text-gray-800 dark:text-gray-300">Options:</strong>
                    <ul class="text-gray-700 dark:text-gray-400">
                        @if(!empty($campaign->options))
                            @foreach ($campaign->options as $key => $value)
                                <li><strong>{{ $key }}:</strong> {{ $value }}</li>
                            @endforeach
                        @else
                            <li>No options set</li>
                        @endif
                    </ul>
                </div>

                <!-- Report -->
                <div class="mb-4">
                    <strong class="text-gray-800 dark:text-gray-300">Report:</strong>
                    <ul class="text-gray-700 dark:text-gray-400">
                        @if(!empty($campaign->report))
                            @foreach ($campaign->report as $key => $value)
                                <li><strong>{{ $key }}:</strong> {{ $value }}</li>
                            @endforeach
                        @else
                            <li>No report data available</li>
                        @endif
                    </ul>
                </div>

                <!-- Settings -->
                <div class="mb-4">
                    <strong class="text-gray-800 dark:text-gray-300">Settings:</strong>
                    <ul class="text-gray-700 dark:text-gray-400">
                        @if(!empty($campaign->settings))
                            @foreach ($campaign->settings as $key => $value)
                                <li><strong>{{ $key }}:</strong> {{ $value }}</li>
                            @endforeach
                        @else
                            <li>No settings configured</li>
                        @endif
                    </ul>
                </div>

                <!-- Back Button -->
                <div class="flex justify-end">
                    <a href="{{ route('campaigns.index') }}" class="px-4 py-2 bg-gray-600 dark:bg-gray-500 text-white rounded-md shadow-sm hover:bg-gray-700 dark:hover:bg-gray-600">Back to Campaigns</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
