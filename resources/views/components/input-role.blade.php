{{-- resources/views/components/input-role.blade.php --}}
@props(['hasAdmin' => false, 'value' => ''])

{{-- Debug output - hapus setelah testing --}}
{{-- @if(config('app.debug'))
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-3 py-2 rounded mb-2">
        <strong>Debug:</strong> hasAdmin = {{ $hasAdmin ? 'true' : 'false' }}
        <br>Admin count: {{ \App\Models\User::where('role', 'admin')->count() }}
    </div>
@endif --}}

<select id="role" name="role" {{ $attributes->merge(['class' => 'block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }} required>
    <option value="">{{ __('Pilih Role') }}</option>
    
    {{-- Tampilkan option admin hanya jika belum ada admin --}}
    @unless($hasAdmin)
        <option value="admin" {{ old('role', $value) === 'admin' ? 'selected' : '' }}>
            {{ __('Admin') }}
        </option>
    @endunless
    
    <option value="user" {{ old('role', $value) === 'user' ? 'selected' : '' }}>
        {{ __('User') }}
    </option>
</select>
