<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Welcome</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-100 flex items-center justify-center">
        <div class="text-center space-y-4">
            <h1 class="text-3xl font-semibold text-slate-900">Welcome</h1>
            <p class="text-slate-700 text-sm">
                Please login or create an account to continue.
            </p>
            <a
                href="{{ route('login') }}"
                class="inline-flex items-center justify-center rounded-md bg-sky-600 px-5 py-2 text-sm font-semibold text-white hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2"
            >
                Go to Login / Register
            </a>
        </div>
    </body>
</html>
