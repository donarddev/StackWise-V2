@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full px-4 py-3 bg-white/5 placeholder-gray-300 text-white rounded-xl border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition duration-150']) }}>
