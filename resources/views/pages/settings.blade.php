@extends('layouts.nolana')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-indigo-600 dark:from-purple-400 dark:to-indigo-400">Pengaturan Sistem</h1>
    <p class="text-gray-500 text-sm mt-1">Konfigurasi pengaturan API dan integrasi sistem eksternal.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Sidebar Settings Menu -->
    <div class="lg:col-span-1 space-y-2">
        <a href="#wa-integration" class="block bg-white dark:bg-gray-800 p-4 border-l-4 border-purple-500 rounded-r-xl shadow-sm mb-2">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center text-purple-600">
                    <i class="ph-fill ph-whatsapp-logo text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white text-sm">Integrasi WhatsApp</h3>
                    <p class="text-[11px] text-gray-500">Konfigurasi Fonnte API Token</p>
                </div>
            </div>
        </a>

        <a href="#telegram-integration" class="block bg-white dark:bg-gray-800 p-4 border-l-4 border-blue-500 rounded-r-xl shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600">
                    <i class="ph-fill ph-telegram-logo text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white text-sm">Integrasi Telegram</h3>
                    <p class="text-[11px] text-gray-500">Konfigurasi Bot Telegram Token</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Main Settings Content -->
    <div class="lg:col-span-2 space-y-6">
        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            
            <div id="wa-integration" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30 flex items-center gap-3">
                    <i class="ph-bold ph-key text-purple-500 text-lg"></i>
                    <h2 class="font-bold text-gray-900 dark:text-white">Fonnte WhatsApp Token API</h2>
                </div>
                
                <div class="p-6">
                    <div class="mb-5">
                        <label for="fonnte_token" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Token API Fonnte</label>
                        <div class="relative">
                            <i class="ph ph-lock-key absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="fonnte_token" name="fonnte_token" value="{{ old('fonnte_token', $fonnteToken) }}" 
                                class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50/50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all text-sm font-medium"
                                placeholder="Masukkan Token API Fonnte (Misal: M4UyqiFMqyP...)">
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Token ini digunakan untuk mengirim pesan otomatis WhatsApp (Notifikasi Petugas & Tiket Gangguan).</p>
                    </div>
                </div>
            </div>

            <div id="telegram-integration" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-blue-50/50 dark:bg-blue-900/10 flex items-center gap-3">
                    <i class="ph-bold ph-robot text-blue-500 text-lg"></i>
                    <h2 class="font-bold text-gray-900 dark:text-white">Telegram Bot Token API</h2>
                </div>
                
                <div class="p-6">
                    <div class="mb-5">
                        <label for="telegram_bot_token" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Token Bot Telegram</label>
                        <div class="relative">
                            <i class="ph ph-lock-key absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="telegram_bot_token" name="telegram_bot_token" value="{{ old('telegram_bot_token', $telegramToken) }}" 
                                class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50/50 dark:bg-gray-700/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm font-medium"
                                placeholder="Masukkan Token Telegram (Misal: 7865955621:AAEt2g19...)">
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Token ini digunakan untuk interaksi Bot Telegram (Klaim Tugas, Broadcast Grup teknisi).</p>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-700 mt-6">
                        <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg shadow-blue-200 dark:shadow-blue-900/20 transition-all flex items-center gap-2 text-sm">
                            <i class="ph-bold ph-floppy-disk"></i>
                            Simpan Pengaturan Token
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
