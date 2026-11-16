<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - BANFANB Ruta de Crédito</title>
    <link rel="icon" type="image/png" href="{{ asset('images/banfanb/banfanb_favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        brand: {
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full flex items-center justify-center p-6 bg-gradient-to-br from-slate-950 via-slate-900 to-brand-950 text-slate-100 font-sans">
    <div class="w-full max-w-4xl grid md:grid-cols-2 bg-slate-900/90 backdrop-blur-xl border border-slate-800 rounded-3xl shadow-2xl overflow-hidden">
        
        <!-- Panel Izquierdo: Formulario -->
        <div class="p-8 md:p-10 flex flex-col justify-between">
            <div>
                <div class="mb-6 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/banfanb/banfanb_icon.png') }}" alt="BANFANB Icon" class="w-12 h-12 rounded-2xl shadow-lg border border-amber-400/30 shrink-0">
                        <img src="{{ asset('images/banfanb/banfanb_logo_white.png') }}" alt="BANFANB" class="h-10 w-auto object-contain">
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400 bg-amber-400/10 border border-amber-400/30 px-2.5 py-1 rounded-lg shrink-0">Ruta de Crédito</span>
                </div>

                <h2 class="text-2xl font-bold text-white mb-1">Acceso al Sistema</h2>
                <p class="text-xs text-slate-400 mb-6">Custodia, control y trazabilidad de expedientes crediticios BANFANB.</p>

                @if($errors->any())
                    <div class="mb-5 p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Correo Electrónico</label>
                        <input type="email" name="email" id="input-email" required
                               value="{{ old('email', 'admin@empresa.com') }}"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Contraseña</label>
                        <input type="password" name="password" id="input-password" required
                               value="password123"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all">
                    </div>

                    <button type="submit" 
                            class="w-full py-3 rounded-xl bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 text-white font-semibold text-sm shadow-lg shadow-brand-600/30 transition-all flex items-center justify-center gap-2 mt-2">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        <span>Ingresar al Sistema</span>
                    </button>
                </form>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-800 text-xs text-slate-500 text-center">
                Arquitectura Desacoplada &bull; PostgreSQL 3FN &bull; Laravel 12
            </div>
        </div>

        <!-- Panel Derecho: Cuentas de Demostración Rápida -->
        <div class="p-8 md:p-10 bg-slate-950/60 border-t md:border-t-0 md:border-l border-slate-800 flex flex-col justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-brand-400 block mb-1">Simulador de Acceso</span>
                <h3 class="text-lg font-bold text-white mb-2">Cuentas Pre-configuradas</h3>
                <p class="text-xs text-slate-400 mb-4">Haz clic en cualquier rol para auto-completar el formulario y probar las distintas bandejas:</p>

                <div class="space-y-2.5 max-h-96 overflow-y-auto pr-1">
                    @foreach($demoUsers as $du)
                        <button type="button" 
                                onclick="fillCredentials('{{ $du->email }}', 'password123')"
                                class="w-full p-3 rounded-xl bg-slate-900 border border-slate-800 hover:border-brand-500/50 hover:bg-slate-800/50 text-left transition-all group flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-slate-200 block group-hover:text-brand-400 transition-colors">{{ $du->name }}</span>
                                <span class="text-[11px] text-slate-400">{{ $du->email }}</span>
                            </div>
                            <span class="px-2 py-0.5 text-[10px] font-semibold rounded-md bg-slate-800 text-brand-400 border border-slate-700/60">
                                {{ $du->role?->name ?? 'Usuario' }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>

            <p class="text-[11px] text-slate-500 mt-4 text-center">Contraseña predeterminada para todos: <code class="text-slate-300 font-mono bg-slate-800 px-1 py-0.5 rounded">password123</code></p>
        </div>

    </div>

    <script>
        lucide.createIcons();
        function fillCredentials(email, password) {
            document.getElementById('input-email').value = email;
            document.getElementById('input-password').value = password;
        }
    </script>
</body>
</html>
