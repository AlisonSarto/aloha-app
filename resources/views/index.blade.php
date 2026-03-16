<!doctype html>
<html lang="pt-BR" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aloha App</title>
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.263.0/dist/umd/lucide.min.js"></script>
    <script src="/_sdk/element_sdk.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&amp;family=Baloo+2:wght@700;800&amp;display=swap"
        rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['Baloo 2', 'cursive'],
                        body: ['Nunito', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        /* Animated gradient bg */
        .aloha-bg {
            background: linear-gradient(160deg, #e0f7fa 0%, #ffffff 40%, #fff8f0 70%, #e0f7fa 100%);
            background-size: 300% 300%;
            animation: bgShift 12s ease-in-out infinite;
        }

        @keyframes bgShift {

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        /* Floating ice cubes */
        .ice-cube {
            position: absolute;
            opacity: 0.08;
            animation: floatCube 8s ease-in-out infinite;
            pointer-events: none;
        }

        .ice-cube:nth-child(2) {
            animation-delay: -2s;
            animation-duration: 10s;
        }

        .ice-cube:nth-child(3) {
            animation-delay: -4s;
            animation-duration: 7s;
        }

        .ice-cube:nth-child(4) {
            animation-delay: -6s;
            animation-duration: 9s;
        }

        .ice-cube:nth-child(5) {
            animation-delay: -1s;
            animation-duration: 11s;
        }

        @keyframes floatCube {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            33% {
                transform: translateY(-18px) rotate(8deg);
            }

            66% {
                transform: translateY(10px) rotate(-5deg);
            }
        }

        /* Primary button pulse */
        .pulse-ring {
            animation: pulseRing 2.5s ease-out infinite;
        }

        @keyframes pulseRing {
            0% {
                transform: scale(1);
                opacity: 0.4;
            }

            100% {
                transform: scale(1.35);
                opacity: 0;
            }
        }

        /* Staggered entrance */
        .entrance {
            opacity: 0;
            transform: translateY(24px);
            animation: slideUp 0.6s ease-out forwards;
        }

        .entrance-d1 {
            animation-delay: 0.1s;
        }

        .entrance-d2 {
            animation-delay: 0.25s;
        }

        .entrance-d3 {
            animation-delay: 0.4s;
        }

        .entrance-d4 {
            animation-delay: 0.55s;
        }

        .entrance-d5 {
            animation-delay: 0.7s;
        }

        .entrance-d6 {
            animation-delay: 0.85s;
        }

        @keyframes slideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Button press */
        .btn-press:active {
            transform: scale(0.96);
        }

        .btn-press {
            transition: transform 0.12s ease, box-shadow 0.2s ease;
        }

        /* Drink icon wobble on hover */
        .wobble-hover:hover .drink-icon {
            animation: wobble 0.5s ease;
        }

        @keyframes wobble {
            0% {
                transform: rotate(0);
            }

            25% {
                transform: rotate(-8deg);
            }

            50% {
                transform: rotate(6deg);
            }

            75% {
                transform: rotate(-3deg);
            }

            100% {
                transform: rotate(0);
            }
        }

        /* Subtle link underline */
        .link-underline {
            position: relative;
        }

        .link-underline::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 1.5px;
            background: currentColor;
            transition: width 0.3s ease;
        }

        .link-underline:hover::after {
            width: 100%;
        }
    </style>
    <style>
        body {
            box-sizing: border-box;
        }
    </style>
    <script src="/_sdk/data_sdk.js" type="text/javascript"></script>
</head>

<body class="h-full">
    <div id="app-wrapper" class="aloha-bg h-full w-full overflow-auto relative"><!-- Decorative floating ice cubes -->
        <div class="ice-cube" style="top:8%;left:10%;width:60px;height:60px;">
            <svg viewbox="0 0 60 60" fill="none">
                <rect x="5" y="5" width="50" height="50" rx="12" fill="#00acc1" stroke="#0097a7" stroke-width="2" />
                <rect x="18" y="18" width="10" height="10" rx="3" fill="#b2ebf2" opacity="0.6" />
                <rect x="34" y="30" width="8" height="8" rx="2" fill="#b2ebf2" opacity="0.4" />
            </svg>
        </div>
        <div class="ice-cube" style="top:25%;right:8%;width:45px;height:45px;">
            <svg viewbox="0 0 60 60" fill="none">
                <rect x="5" y="5" width="50" height="50" rx="14" fill="#ff8a65" stroke="#ff7043" stroke-width="2" />
                <circle cx="25" cy="25" r="6" fill="#ffe0b2" opacity="0.5" />
            </svg>
        </div>
        <div class="ice-cube" style="bottom:30%;left:5%;width:40px;height:40px;">
            <svg viewbox="0 0 60 60" fill="none">
                <rect x="8" y="8" width="44" height="44" rx="10" fill="#00bcd4" stroke="#0097a7" stroke-width="1.5" />
            </svg>
        </div>
        <div class="ice-cube" style="bottom:15%;right:12%;width:50px;height:50px;">
            <svg viewbox="0 0 60 60" fill="none">
                <rect x="6" y="6" width="48" height="48" rx="13" fill="#ffab91" stroke="#ff8a65" stroke-width="1.5" />
                <rect x="20" y="22" width="12" height="12" rx="4" fill="#fff3e0" opacity="0.4" />
            </svg>
        </div>
        <div class="ice-cube" style="top:55%;left:65%;width:35px;height:35px;">
            <svg viewbox="0 0 60 60" fill="none">
                <rect x="10" y="10" width="40" height="40" rx="11" fill="#4dd0e1" stroke="#26c6da" stroke-width="1.5" />
            </svg>
        </div><!-- Main Content -->
        <div class="flex flex-col items-center justify-center min-h-full px-5 py-10 relative z-10">
            <div class="w-full max-w-sm flex flex-col items-center"><!-- 1. Logo / Brand -->
                <div class="entrance entrance-d1 flex flex-col items-center mb-6">
                    <!-- Logo SVG: stylized ice in a glass -->
                    <div id="logo-container"
                        class="w-20 h-20 rounded-3xl flex items-center justify-center mb-3 shadow-lg"
                        style="background: linear-gradient(135deg, #00bcd4, #0097a7);">
                        <img src="/favicon.ico" class="w-12 h-12 object-contain">
                    </div>
                    <h1 id="app-name" class="font-display text-3xl tracking-tight" style="color: #00838f;">Aloha App
                    </h1>
                    <p id="tagline" class="font-body font-bold text-base mt-1" style="color: #ff7043;">Essencial no seu
                        drink.</p>
                    <p id="subtitle" class="font-body text-sm text-center mt-2 max-w-xs" style="color: #607d8b;">Faça
                        seus pedidos de gelo Aloha de forma rápida e fácil.</p>
                </div><!-- 2. Primary Action -->
                <div class="entrance entrance-d2 w-full flex flex-col items-center mb-8">
                    <div class="relative w-full flex items-center justify-center"><!-- Pulse ring behind button -->
                        <div class="pulse-ring absolute inset-0 rounded-2xl" style="background: rgba(0,188,212,0.15);">
                        </div><button id="primary-btn" onclick="handleOrder()"
                            class="wobble-hover btn-press relative w-full py-5 rounded-2xl font-body font-extrabold text-lg text-white flex items-center justify-center gap-3 shadow-xl hover:shadow-2xl"
                            style="background: linear-gradient(135deg, #00bcd4, #0097a7);"> <span class="drink-icon"> <i
                                    data-lucide="cup-soda" style="width:26px;height:26px;"></i> </span> <span
                                id="primary-btn-text">Fazer meu pedido</span> </button>
                    </div>
                    <p id="primary-subtext" class="font-body text-xs mt-3 text-center" style="color: #90a4ae;">Peça seu
                        gelo Aloha em poucos segundos.</p>
                </div><!-- Divider -->
                <div class="entrance entrance-d3 w-full flex items-center gap-3 mb-6">
                    <div class="flex-1 h-px" style="background: #b2dfdb;"></div><span
                        class="font-body text-xs font-semibold" style="color: #b0bec5;">ou</span>
                    <div class="flex-1 h-px" style="background: #b2dfdb;"></div>
                </div><!-- 3. Existing Customer -->
                <div class="entrance entrance-d4 w-full mb-4">
                    <p id="login-prompt" class="font-body text-sm text-center mb-2" style="color: #607d8b;">Já é
                        cliente?</p><button id="login-btn" onclick="handleLogin()"
                        class="btn-press w-full py-3.5 rounded-xl font-body font-bold text-sm border-2 flex items-center justify-center gap-2 hover:shadow-md"
                        style="color: #00838f; border-color: #b2dfdb; background: rgba(255,255,255,0.7);"> <i
                            data-lucide="log-in" style="width:18px;height:18px;"></i> <span id="login-btn-text">Entrar
                            na minha conta</span> </button>
                </div><!-- 4. New Customer -->
                <div class="entrance entrance-d5 w-full mb-8">
                    <p id="register-prompt" class="font-body text-sm text-center mb-2" style="color: #607d8b;">Ainda não
                        tem cadastro?</p><button id="register-btn" onclick="handleRegister()"
                        class="btn-press w-full py-3.5 rounded-xl font-body font-bold text-sm flex items-center justify-center gap-2 hover:shadow-md"
                        style="color: #ff7043; border: 2px solid #ffccbc; background: rgba(255,255,255,0.7);"> <i
                            data-lucide="user-plus" style="width:18px;height:18px;"></i> <span
                            id="register-btn-text">Criar minha conta</span> </button>
                    <p id="register-description" class="font-body text-xs text-center mt-2" style="color: #90a4ae;">
                        Cadastre-se para comprar gelo Aloha e acompanhar seus pedidos.</p>
                </div><!-- 5. Internal Access -->
                <div class="entrance entrance-d6 flex items-center justify-center gap-1.5"><i data-lucide="lock"
                        style="width:13px;height:13px;color:#b0bec5;"></i> <button id="internal-link"
                        onclick="handleInternal()" class="link-underline font-body text-xs font-semibold"
                        style="color: #b0bec5; background: none; border: none; cursor: pointer;"> Área interna </button>
                </div>
            </div>
        </div><!-- Toast notification -->
        <div id="toast"
            class="fixed bottom-6 left-1/2 -translate-x-1/2 px-5 py-3 rounded-xl font-body text-sm font-semibold text-white shadow-xl z-50 transition-all duration-300"
            style="background: #00838f; opacity: 0; transform: translate(-50%, 20px); pointer-events: none;"><span
                id="toast-text"></span>
        </div>
    </div>
    <script>
        // Toast system
        let toastTimer;
        function showToast(msg) {
            const t = document.getElementById('toast');
            const tt = document.getElementById('toast-text');
            tt.textContent = msg;
            t.style.opacity = '1';
            t.style.transform = 'translate(-50%, 0)';
            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => {
                t.style.opacity = '0';
                t.style.transform = 'translate(-50%, 20px)';
            }, 2500);
        }

        // Navigation handlers (placeholder actions for PWA routing)
        function handleOrder() { showToast('🧊 Redirecionando para pedidos...'); }
        function handleLogin() { showToast('🔑 Abrindo login...'); }
        function handleRegister() { showToast('📝 Abrindo cadastro...'); }
        function handleInternal() { showToast('🔒 Acesso interno...'); }

        // Default config
        const defaultConfig = {
            app_name: 'Aloha App',
            tagline: 'Essencial no seu drink.',
            subtitle: 'Faça seus pedidos de gelo Aloha de forma rápida e fácil.',
            primary_button_text: 'Fazer meu pedido',
            primary_subtext: 'Peça seu gelo Aloha em poucos segundos.',
            login_prompt: 'Já é cliente?',
            login_button_text: 'Entrar na minha conta',
            register_prompt: 'Ainda não tem cadastro?',
            register_button_text: 'Criar minha conta',
            register_description: 'Cadastre-se para comprar gelo Aloha e acompanhar seus pedidos.',
            internal_link_text: 'Área interna',
            background_color: '#e0f7fa',
            surface_color: '#ffffff',
            text_color: '#00838f',
            primary_action_color: '#00bcd4',
            secondary_action_color: '#ff7043',
            font_family: 'Nunito',
            font_size: 16
        };

        // Element SDK
        function applyConfig(config) {
            const c = { ...defaultConfig, ...config };
            const font = c.font_family || defaultConfig.font_family;
            const baseSize = c.font_size || defaultConfig.font_size;
            const bodyStack = `${font}, Nunito, sans-serif`;
            const displayStack = `Baloo 2, ${font}, cursive`;

            // Text content
            document.getElementById('app-name').textContent = c.app_name;
            document.getElementById('tagline').textContent = c.tagline;
            document.getElementById('subtitle').textContent = c.subtitle;
            document.getElementById('primary-btn-text').textContent = c.primary_button_text;
            document.getElementById('primary-subtext').textContent = c.primary_subtext;
            document.getElementById('login-prompt').textContent = c.login_prompt;
            document.getElementById('login-btn-text').textContent = c.login_button_text;
            document.getElementById('register-prompt').textContent = c.register_prompt;
            document.getElementById('register-btn-text').textContent = c.register_button_text;
            document.getElementById('register-description').textContent = c.register_description;
            document.getElementById('internal-link').textContent = c.internal_link_text;

            // Colors
            const bg = c.background_color;
            const surface = c.surface_color;
            const text = c.text_color;
            const primary = c.primary_action_color;
            const secondary = c.secondary_action_color;

            // Background gradient
            const wrapper = document.getElementById('app-wrapper');
            wrapper.style.background = `linear-gradient(160deg, ${bg} 0%, ${surface} 40%, #fff8f0 70%, ${bg} 100%)`;
            wrapper.style.backgroundSize = '300% 300%';

            // Logo container
            document.getElementById('logo-container').style.background = `linear-gradient(135deg, ${primary}, ${text})`;

            // App name
            document.getElementById('app-name').style.color = text;
            document.getElementById('app-name').style.fontFamily = displayStack;
            document.getElementById('app-name').style.fontSize = `${baseSize * 1.875}px`;

            // Tagline
            document.getElementById('tagline').style.color = secondary;
            document.getElementById('tagline').style.fontFamily = bodyStack;
            document.getElementById('tagline').style.fontSize = `${baseSize}px`;

            // Subtitle
            document.getElementById('subtitle').style.fontFamily = bodyStack;
            document.getElementById('subtitle').style.fontSize = `${baseSize * 0.875}px`;

            // Primary button
            const pBtn = document.getElementById('primary-btn');
            pBtn.style.background = `linear-gradient(135deg, ${primary}, ${text})`;
            pBtn.style.fontFamily = bodyStack;
            pBtn.style.fontSize = `${baseSize * 1.125}px`;

            // Primary subtext
            document.getElementById('primary-subtext').style.fontFamily = bodyStack;
            document.getElementById('primary-subtext').style.fontSize = `${baseSize * 0.75}px`;

            // Login
            document.getElementById('login-prompt').style.fontFamily = bodyStack;
            document.getElementById('login-prompt').style.fontSize = `${baseSize * 0.875}px`;
            const lBtn = document.getElementById('login-btn');
            lBtn.style.color = text;
            lBtn.style.borderColor = `${primary}55`;
            lBtn.style.fontFamily = bodyStack;
            lBtn.style.fontSize = `${baseSize * 0.875}px`;

            // Register
            document.getElementById('register-prompt').style.fontFamily = bodyStack;
            document.getElementById('register-prompt').style.fontSize = `${baseSize * 0.875}px`;
            const rBtn = document.getElementById('register-btn');
            rBtn.style.color = secondary;
            rBtn.style.borderColor = `${secondary}55`;
            rBtn.style.fontFamily = bodyStack;
            rBtn.style.fontSize = `${baseSize * 0.875}px`;
            document.getElementById('register-description').style.fontFamily = bodyStack;
            document.getElementById('register-description').style.fontSize = `${baseSize * 0.75}px`;

            // Internal link
            const iLink = document.getElementById('internal-link');
            iLink.style.fontFamily = bodyStack;
            iLink.style.fontSize = `${baseSize * 0.75}px`;
        }

        // Init Element SDK
        if (window.elementSdk) {
            window.elementSdk.init({
                defaultConfig,
                onConfigChange: async (config) => applyConfig(config),
                mapToCapabilities: (config) => {
                    const c = { ...defaultConfig, ...config };
                    return {
                        recolorables: [
                            { get: () => c.background_color, set: (v) => { c.background_color = v; window.elementSdk.setConfig({ background_color: v }); } },
                            { get: () => c.surface_color, set: (v) => { c.surface_color = v; window.elementSdk.setConfig({ surface_color: v }); } },
                            { get: () => c.text_color, set: (v) => { c.text_color = v; window.elementSdk.setConfig({ text_color: v }); } },
                            { get: () => c.primary_action_color, set: (v) => { c.primary_action_color = v; window.elementSdk.setConfig({ primary_action_color: v }); } },
                            { get: () => c.secondary_action_color, set: (v) => { c.secondary_action_color = v; window.elementSdk.setConfig({ secondary_action_color: v }); } }
                        ],
                        borderables: [],
                        fontEditable: {
                            get: () => c.font_family || defaultConfig.font_family,
                            set: (v) => { c.font_family = v; window.elementSdk.setConfig({ font_family: v }); }
                        },
                        fontSizeable: {
                            get: () => c.font_size || defaultConfig.font_size,
                            set: (v) => { c.font_size = v; window.elementSdk.setConfig({ font_size: v }); }
                        }
                    };
                },
                mapToEditPanelValues: (config) => {
                    const c = { ...defaultConfig, ...config };
                    return new Map([
                        ['app_name', c.app_name],
                        ['tagline', c.tagline],
                        ['subtitle', c.subtitle],
                        ['primary_button_text', c.primary_button_text],
                        ['primary_subtext', c.primary_subtext],
                        ['login_prompt', c.login_prompt],
                        ['login_button_text', c.login_button_text],
                        ['register_prompt', c.register_prompt],
                        ['register_button_text', c.register_button_text],
                        ['register_description', c.register_description],
                        ['internal_link_text', c.internal_link_text]
                    ]);
                }
            });
        }

        // Apply defaults on load
        applyConfig(defaultConfig);

        // Init Lucide icons
        lucide.createIcons();
    </script>
</body>

</html>
