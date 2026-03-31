<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Tiara Smart Assistant</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="h-full gradient-bg antialiased">
    
    <div class="min-h-full flex items-center justify-center px-4 py-12">
        
        <div class="w-full max-w-md">
            
            <!-- Logo & Branding -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-2xl shadow-lg mb-4 p-2">
                    <img src="{{ asset('pdam-logo.png') }}" alt="Logo PDAM" class="w-full h-full object-contain" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <i class="ph-fill ph-sparkle text-4xl text-purple-600 hidden"></i>
                </div>
                <h1 class="text-3xl font-extrabold text-white mb-1">Tiara Smart Assistant</h1>
                <p class="text-purple-100 font-semibold text-base mb-1">Perumdam Tirta Ardhia Rinjani</p>
                <p class="text-purple-200 font-medium text-xs uppercase tracking-widest">Smart Secretary v2.0</p>
            </div>

            <!-- Login Card -->
            <div class="glass-effect rounded-3xl shadow-2xl p-8 border border-white/20">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-1">Selamat Datang Kembali</h2>
                    <p class="text-gray-500">Silakan login untuk melanjutkan</p>
                </div>

                <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                        {{ $errors->first() }}
                    </div>
                    @endif
                    
                    <!-- Username Input -->
                    <div>
                        <label for="username" class="block text-sm font-bold text-gray-700 mb-2">
                            Username
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="ph ph-user text-gray-400 text-xl"></i>
                            </div>
                            <input 
                                type="text" 
                                id="username" 
                                name="username" 
                                required
                                class="block w-full pl-12 pr-4 py-3.5 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all bg-white/50"
                                placeholder="Masukkan username"
                            >
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label for="password" class="block text-sm font-bold text-gray-700 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="ph ph-lock text-gray-400 text-xl"></i>
                            </div>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                class="block w-full pl-12 pr-4 py-3.5 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all bg-white/50"
                                placeholder="••••••••"
                            >
                        </div>
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 focus:ring-2 cursor-pointer">
                            <span class="ml-2 text-sm font-medium text-gray-700 group-hover:text-gray-900">Ingat Saya</span>
                        </label>
                        <a href="#" class="text-sm font-bold text-purple-600 hover:text-purple-700 transition">
                            Lupa Password?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold py-4 rounded-xl hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transform hover:scale-[1.02] transition-all shadow-lg shadow-purple-500/30"
                    >
                        <span class="flex items-center justify-center gap-2">
                            <span>Masuk</span>
                            <i class="ph-bold ph-arrow-right"></i>
                        </span>
                    </button>

                </form>

            </div>

            <!-- Footer Info -->
            <p class="mt-6 text-center text-sm text-purple-100">
                © {{ date('Y') }} Perumdam Tirta Ardhia Rinjani. All rights reserved.
            </p>

        </div>
        
    </div>

</body>
</html>
