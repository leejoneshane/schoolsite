<main class="col-span-10 mb-auto">
    <div class="m-5 relative bg-white dark:bg-gray-700 text-black dark:text-gray-200">
        <div class="p-2">
            @if (isset($error))
            <div class="m-5 border-red-500 bg-red-100 dark:bg-red-700 border-b-2" role="alert">
                {{ $error }}
            </div>
            @endif
            @if (isset($success))
            <div class="m-5 border-green-500 bg-green-100 dark:bg-green-700 border-b-2" role="alert">
                {{ $success }}
            </div>
            @endif
            @if (isset($message))
            <div class="m-5 border-blue-500 bg-blue-100 dark:bg-blue-700 border-b-2" role="alert">
                {{ $message }}
            </div>
            @endif
        </div>
        @yield('content')
    </div>
    <div class="mb-10"></div>
</main>