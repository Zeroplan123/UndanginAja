<x-app-layout>
    <div class="max-w-7xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-4">Preview: {{ $template->title }}</h1>

        <div class="border rounded-lg shadow-lg p-8">
            {!! $template->html_content !!}
        </div>

        <div class="mt-6">
            <a href="{{ route('dashboard') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                Kembali
            </a>
        </div>
    </div>
</x-app-layout>
