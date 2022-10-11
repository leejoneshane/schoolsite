<main class="col-span-10 mb-auto">
    <div class="m-5 mb-32 relative bg-white dark:bg-gray-700 text-black dark:text-gray-200">
        <div>
            @if (isset($error))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                {{ $error }}
            </div>
            @endif
            @if (isset($success))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                {{ $success }}
            </div>
            @endif
            @if (isset($message))
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                {{ $message }}
            </div>
            @endif
        </div>
        @yield('content')
    </div>
    <div class="mb-32"></div>
</main>