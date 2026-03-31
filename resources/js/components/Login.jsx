import React, { useState, useEffect } from 'react';

const Login = () => {
    const [csrfToken, setCsrfToken] = useState('');
    const [errors, setErrors] = useState([]);
    const [oldUsername, setOldUsername] = useState('');

    useEffect(() => {
        // Get CSRF Token from meta tag
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (token) setCsrfToken(token);

        // Get Laravel errors and old input if any
        if (window.LaravelData) {
            if (window.LaravelData.errors) setErrors(window.LaravelData.errors);
            if (window.LaravelData.oldInput?.username) setOldUsername(window.LaravelData.oldInput.username);
        }
    }, []);

    return (
        <div className="min-h-screen w-full flex items-center justify-center p-4 relative overflow-hidden bg-gradient-to-b from-[#b3e0ff] to-[#e6f5ff]">
            {/* Background Clouds / Sky Elements */}
            <div className="absolute top-0 left-0 w-full h-full pointer-events-none overflow-hidden">
                {/* Simulate cloud shapes using large blurred circles */}
                <div className="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-white/40 rounded-full blur-3xl opacity-70"></div>
                <div className="absolute top-[20%] right-[-10%] w-[60%] h-[60%] bg-white/50 rounded-full blur-3xl opacity-60"></div>
                <div className="absolute bottom-[-20%] left-[20%] w-[70%] h-[70%] bg-white/60 rounded-full blur-3xl opacity-80"></div>
            </div>

            {/* Arch Background Graphic (from Reference) */}
            <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] border border-white/30 rounded-full pointer-events-none opacity-50 hidden md:block"></div>

            {/* Main Login Card */}
            <div className="relative z-10 w-full max-w-md bg-white/70 backdrop-blur-2xl rounded-[32px] p-8 md:p-10 shadow-[0_8px_32px_rgba(0,0,0,0.08)] border border-white/60">

                {/* Header Icon */}
                <div className="flex justify-center mb-6">
                    <div className="w-20 h-20 bg-white rounded-2xl flex items-center justify-center shadow-sm border border-gray-100 overflow-hidden">
                        <img src="/pdam-logo.png" alt="Logo Tirta Ardhia Rinjani" className="w-[85%] h-[85%] object-contain scale-110 aspect-square" />
                    </div>
                </div>

                {/* Title & Description */}
                <div className="text-center mb-8">
                    <h1 className="text-2xl font-bold text-gray-900 mb-2 font-jakarta">Masuk ke Dashboard</h1>
                    <p className="text-gray-500 text-sm leading-relaxed">
                        Welcome to Tiara Smart Assistant.<br />
                        Please enter your username and password.
                    </p>
                </div>

                {/* Form */}
                <form action="/portal-internal" method="POST" className="space-y-4">
                    <input type="hidden" name="_token" value={csrfToken} />

                    {errors.length > 0 && (
                        <div className="bg-red-50/80 backdrop-blur-sm border border-red-100 text-red-600 px-4 py-3 rounded-2xl text-sm text-center">
                            {errors[0]}
                        </div>
                    )}

                    {/* Username Input */}
                    <div className="relative">
                        <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i className="ph-fill ph-envelope-simple text-gray-400 text-lg"></i>
                        </div>
                        <input
                            type="text"
                            name="username"
                            defaultValue={oldUsername}
                            required
                            className="block w-full pl-11 pr-4 py-3.5 bg-gray-50/80 border border-gray-200/50 rounded-2xl text-gray-900 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition-all font-medium"
                            placeholder="Username"
                        />
                    </div>

                    {/* Password Input */}
                    <div className="relative">
                        <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i className="ph-fill ph-lock-key text-gray-400 text-lg"></i>
                        </div>
                        <input
                            type="password"
                            name="password"
                            required
                            className="block w-full pl-11 pr-12 py-3.5 bg-gray-50/80 border border-gray-200/50 rounded-2xl text-gray-900 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition-all font-medium"
                            placeholder="Password"
                        />
                        <button type="button" className="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                            <i className="ph-bold ph-eye-slash text-lg"></i>
                        </button>
                    </div>

                    {/* Forgot Password Link */}
                    <div className="flex justify-end pt-1 pb-2">
                        <a href="#" className="text-xs font-semibold text-gray-600 hover:text-gray-900 transition-colors">
                            Forgot password?
                        </a>
                    </div>

                    {/* Submit Button */}
                    <button
                        type="submit"
                        className="w-full bg-[#1a1c23] hover:bg-black text-white font-medium py-3.5 rounded-2xl transition-all shadow-[0_4px_14px_rgba(0,0,0,0.15)] hover:shadow-[0_6px_20px_rgba(0,0,0,0.23)] hover:-translate-y-0.5"
                    >
                        Get Started
                    </button>
                </form>



            </div>



        </div>
    );
};

export default Login;
