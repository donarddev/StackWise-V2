<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 w-auto px-4 py-2 bg-gradient-to-r from-indigo-500 to-emerald-400 text-white font-semibold rounded-xl shadow-lg hover:scale-[1.02] transform transition duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-300']) }}>
    {{ $slot }}
</button>
