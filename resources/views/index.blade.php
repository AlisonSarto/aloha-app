@extends('layouts.auth')

@section('head')
<title>Aloha App</title>
<style>
    /* ── Slider ── */
    #slider-wrapper {
        position: fixed;
        inset: 0;
        overflow: hidden;
        touch-action: pan-y;
    }

    #slides-track {
        display: flex;
        width: calc(7 * 100vw);
        height: 100%;
        transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        will-change: transform;
    }

    .slide {
        width: 100vw;
        height: 100%;
        flex-shrink: 0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }

    /* ── Keyframes ── */
    @keyframes logoEntry {
        from { opacity: 0; transform: scale(0.55); }
        to   { opacity: 1; transform: scale(1); }
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(28px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    @keyframes shimmer {
        0%   { background-position: -200% center; }
        100% { background-position: 200% center; }
    }

    @keyframes pulseBadge {
        0%, 100% { transform: scale(1); }
        50%       { transform: scale(1.06); }
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50%       { transform: translateY(-9px); }
    }

    /* ── Logo (sempre ativa, anima só na carga) ── */
    .logo-entry {
        animation: logoEntry 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) both;
    }

    /* ── Animate items (gated em .active) ── */
    .animate-item {
        opacity: 0;
    }

    .slide.active .animate-item {
        animation: slideUp 0.5s ease-out both;
    }

    .slide.active .animate-item:nth-child(1) { animation-delay: 0.05s; }
    .slide.active .animate-item:nth-child(2) { animation-delay: 0.17s; }
    .slide.active .animate-item:nth-child(3) { animation-delay: 0.29s; }
    .slide.active .animate-item:nth-child(4) { animation-delay: 0.41s; }
    .slide.active .animate-item:nth-child(5) { animation-delay: 0.53s; }
    .slide.active .animate-item:nth-child(6) { animation-delay: 0.65s; }

    /* ── Ícone flutuante ── */
    .slide.active .icon-float {
        animation: float 3.2s ease-in-out infinite;
        animation-delay: 0.7s;
    }

    /* ── Badge Em breve ── */
    .badge-soon {
        animation: pulseBadge 2.2s ease-in-out infinite;
    }

    /* ── Shimmer no CTA final ── */
    .btn-shimmer {
        background: linear-gradient(90deg, #fff 0%, #d1fae5 45%, #fff 55%, #fff 100%);
        background-size: 250% auto;
    }

    .slide.active .btn-shimmer {
        animation: shimmer 2.8s linear infinite;
        animation-delay: 1s;
    }

    /* ── PWA Gate ── */
    #pwa-gate {
        position: fixed;
        inset: 0;
        z-index: 100;
        background: linear-gradient(160deg, #f0fdf4 0%, #ffffff 60%, #dcfce7 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem 1.5rem;
        text-align: center;
    }

    #pwa-gate.hidden {
        display: none;
    }

    @keyframes gateFadeIn {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    #pwa-gate .gate-content {
        animation: gateFadeIn 0.5s ease-out both;
        max-width: 320px;
        width: 100%;
    }

    #pwa-gate .gate-icon-wrap {
        width: 96px;
        height: 96px;
        border-radius: 24px;
        box-shadow: 0 8px 32px rgba(22,163,74,0.18);
        margin: 0 auto 1.75rem;
        overflow: hidden;
    }

    @keyframes iosArrow {
        0%, 100% { transform: translateY(0); }
        50%       { transform: translateY(5px); }
    }

    #pwa-ios-hint .arrow-anim {
        animation: iosArrow 1.6s ease-in-out infinite;
    }

    /* ── Nav footer ── */
    #nav-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1.25rem 1.5rem;
        z-index: 50;
    }

    /* ── Dots (agora inline dentro do footer) ── */
    #dots-container {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #bbf7d0;
        transition: all 0.35s ease;
        border: none;
        cursor: pointer;
        padding: 0;
    }

    .dot.dot-active {
        background: #16a34a;
        width: 22px;
        border-radius: 4px;
    }

    /* ── Setas de navegação ── */
    .nav-arrow {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: none;
        background: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #16a34a;
        font-size: 0.85rem;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        flex-shrink: 0;
    }

    .nav-arrow:active {
        transform: scale(0.88);
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
    }

    .nav-arrow.hidden-arrow {
        visibility: hidden;
        pointer-events: none;
    }

    /* No último slide (verde), dots ficam brancos/transparentes */
    #nav-footer.on-last .dot          { background: rgba(255,255,255,0.35); }
    #nav-footer.on-last .dot-active   { background: white; }
    #nav-footer.on-last .nav-arrow    { background: rgba(255,255,255,0.2); color: white; box-shadow: none; }
</style>
@endsection

@section('content')

{{-- ══ PWA Gate: só aparece se não estiver rodando como app instalado ══ --}}
<div id="pwa-gate">
    <div class="gate-content">

        <div class="gate-icon-wrap">
            <img src="{{ asset('icons/icon-192.png') }}" alt="Aloha App" class="w-full h-full object-cover" />
        </div>

        <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">
            Instale o Aloha App
        </h1>
        <p class="mt-2 text-gray-500 text-sm leading-relaxed">
            Para continuar, adicione o app na sua tela inicial.<br>
            É rápido, gratuito e sem precisar de loja de apps.
        </p>

        {{-- Botão de instalação (Android / Chrome - via beforeinstallprompt) --}}
        <button id="pwa-install-btn"
            class="hidden mt-8 w-full flex justify-center items-center gap-2 rounded-xl bg-green-600 px-4 py-4 text-base font-bold text-white shadow-lg shadow-green-300/50 active:scale-95 transition-transform">
            <i class="fa-solid fa-download"></i> Adicionar à tela inicial
        </button>

        {{-- Instruções para iOS (Safari) --}}
        <div id="pwa-ios-hint" class="hidden mt-8">
            <div class="rounded-2xl border border-green-100 bg-green-50 px-5 py-4 text-left space-y-3">
                <p class="text-xs font-bold text-green-700 uppercase tracking-wide">Como instalar no iPhone</p>
                <div class="flex items-start gap-3">
                    <span class="mt-0.5 flex-shrink-0 w-6 h-6 rounded-full bg-white border border-green-200 flex items-center justify-center text-green-600 text-xs font-bold">1</span>
                    <span class="text-sm text-gray-700">
                        Toque no ícone de compartilhamento
                        <span class="arrow-anim inline-block ml-1 text-green-600"><i class="fa-solid fa-arrow-up-from-bracket"></i></span>
                        no Safari
                    </span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="mt-0.5 flex-shrink-0 w-6 h-6 rounded-full bg-white border border-green-200 flex items-center justify-center text-green-600 text-xs font-bold">2</span>
                    <span class="text-sm text-gray-700">Selecione <strong>"Adicionar à Tela de Início"</strong></span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="mt-0.5 flex-shrink-0 w-6 h-6 rounded-full bg-white border border-green-200 flex items-center justify-center text-green-600 text-xs font-bold">3</span>
                    <span class="text-sm text-gray-700">Confirme tocando em <strong>"Adicionar"</strong></span>
                </div>
            </div>
        </div>

        {{-- Fallback genérico (outros navegadores) --}}
        <div id="pwa-generic-hint" class="hidden mt-8 rounded-2xl border border-green-100 bg-green-50 px-5 py-4 text-sm text-gray-700 text-left">
            <p class="font-bold text-green-700 mb-1">Como instalar</p>
            No menu do seu navegador, procure por <strong>"Adicionar à tela inicial"</strong> ou <strong>"Instalar app"</strong>.
        </div>

        <p class="mt-6 text-xs text-gray-400">
            Já instalou? Abra o Aloha App pela sua tela inicial.
        </p>

        <button id="pwa-skip-btn"
            class="mt-5 w-full flex justify-center items-center rounded-xl border border-gray-200 bg-white px-4 py-3.5 text-sm font-medium text-gray-500 active:scale-95 transition-transform">
            Continuar sem instalar
        </button>

    </div>
</div>

<div id="slider-wrapper">
    <div id="slides-track">

        {{-- ══ SLIDE 1: Abertura + CTA ══ --}}
        <div class="slide bg-gradient-to-b from-white to-green-50" data-slide="0">
            <div class="max-w-sm w-full text-center">

                <div class="logo-entry flex justify-center mb-7">
                    <img src="{{ asset('favicon.ico') }}" alt="Aloha App"
                        class="h-24 w-24 rounded-2xl" />
                </div>

                <h1 class="animate-item text-3xl font-extrabold text-gray-900 tracking-tight">
                    Aloha App
                </h1>
                <p class="animate-item mt-2 text-gray-500 text-base leading-relaxed">
                    Peça gelo Aloha em segundos,<br>
                    direto pelo Aloha App.
                </p>

                <div class="animate-item mt-10 space-y-3 w-full">
                    <button id="btn-start"
                        class="w-full flex justify-center items-center gap-2 rounded-xl bg-green-600 px-4 py-3.5 text-base font-semibold text-white shadow-lg shadow-green-300/50 active:scale-95 transition-transform">
                        Conhecer o app <i class="fa-solid fa-arrow-right"></i>
                    </button>
                    <a href="{{ route('login') }}"
                        class="w-full flex justify-center items-center rounded-xl border border-gray-200 bg-white px-4 py-3.5 text-base font-medium text-gray-600 active:scale-95 transition-transform">
                        Já tenho conta &rarr; Entrar
                    </a>
                </div>

            </div>
        </div>

        {{-- ══ SLIDE 2: Peça em segundos ══ --}}
        <div class="slide bg-gradient-to-b from-green-50 to-white" data-slide="1">
            <div class="max-w-sm w-full text-center">

                <div class="animate-item flex justify-center mb-8">
                    <div class="icon-float w-24 h-24 rounded-full bg-green-100 flex items-center justify-center shadow-inner">
                        <i class="fa-solid fa-cart-plus text-5xl text-green-600"></i>
                    </div>
                </div>

                <h2 class="animate-item text-2xl font-bold text-gray-900">
                    Peça seu gelo Aloha, em segundos
                </h2>
                <p class="animate-item mt-2 text-sm text-gray-500 mb-8">
                    Gelo feito para drink, com entrega rápida
                </p>

                <ul class="animate-item space-y-4 text-left">
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-6 h-6 rounded-full bg-green-100 flex-shrink-0 flex items-center justify-center">
                            <i class="fa-solid fa-check text-xs text-green-600"></i>
                        </span>
                        <span class="text-sm text-gray-700">
                            <strong class="text-gray-900">Escolha o sabor</strong><br>
                            Coco, melancia, maracujá e muito mais
                        </span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-6 h-6 rounded-full bg-green-100 flex-shrink-0 flex items-center justify-center">
                            <i class="fa-solid fa-check text-xs text-green-600"></i>
                        </span>
                        <span class="text-sm text-gray-700">
                            <strong class="text-gray-900">Defina a entrega</strong><br>
                            Escolha data e horário
                        </span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-6 h-6 rounded-full bg-green-100 flex-shrink-0 flex items-center justify-center">
                            <i class="fa-solid fa-check text-xs text-green-600"></i>
                        </span>
                        <span class="text-sm text-gray-700">
                            <strong class="text-gray-900">Acompanhe tudo</strong><br>
                            Status do pedido em tempo real
                        </span>
                    </li>
                </ul>

            </div>
        </div>

        {{-- ══ SLIDE 3: Fornecedores ══ --}}
        <div class="slide bg-gradient-to-b from-white to-green-50" data-slide="2">
            <div class="max-w-sm w-full text-center">

                <div class="animate-item flex justify-center mb-8">
                    <div class="icon-float w-24 h-24 rounded-full bg-green-100 flex items-center justify-center shadow-inner">
                        <i class="fa-solid fa-store text-5xl text-green-600"></i>
                    </div>
                </div>

                <h2 class="animate-item text-2xl font-bold text-gray-900">
                    Gerencie todos seus pedidos em um só lugar
                </h2>
                <p class="animate-item mt-2 text-sm text-gray-500 mb-8">
                    Ideal para quem tem mais de um estabelecimento.
                </p>

                <ul class="animate-item space-y-4 text-left">
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-6 h-6 rounded-full bg-green-100 flex-shrink-0 flex items-center justify-center">
                            <i class="fa-solid fa-check text-xs text-green-600"></i>
                        </span>
                        <span class="text-sm text-gray-700">
                            <strong class="text-gray-900">Vincule suas lojas</strong><br>
                            Registre todos os seus estabelecimentos
                        </span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-6 h-6 rounded-full bg-green-100 flex-shrink-0 flex items-center justify-center">
                            <i class="fa-solid fa-check text-xs text-green-600"></i>
                        </span>
                        <span class="text-sm text-gray-700">
                            <strong class="text-gray-900">Histórico completo</strong><br>
                            Pedidos separados por comércio
                        </span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-6 h-6 rounded-full bg-green-100 flex-shrink-0 flex items-center justify-center">
                            <i class="fa-solid fa-check text-xs text-green-600"></i>
                        </span>
                        <span class="text-sm text-gray-700">
                            <strong class="text-gray-900">Controle financeiro</strong><br>
                            Extrato detalhado por estabelecimento
                        </span>
                    </li>
                </ul>

            </div>
        </div>

        {{-- ══ SLIDE 4: Em breve — Pontos ══ --}}
        <div class="slide bg-gradient-to-b from-green-50 to-white" data-slide="3">
            <div class="max-w-sm w-full text-center">

                <div class="animate-item flex justify-center mb-2">
                    <span class="badge-soon inline-flex items-center gap-1.5 rounded-full bg-green-100 px-4 py-1.5 text-xs font-bold text-green-700 uppercase tracking-wide">
                        <i class="fa-solid fa-clock text-xs"></i> Em breve
                    </span>
                </div>

                <div class="animate-item flex justify-center my-5">
                    <div class="icon-float w-24 h-24 rounded-full bg-yellow-50 border border-yellow-100 flex items-center justify-center shadow-inner">
                        <i class="fa-solid fa-star text-5xl text-yellow-500"></i>
                    </div>
                </div>

                <h2 class="animate-item text-2xl font-bold text-gray-900">Pontos que viram prêmios</h2>
                <p class="animate-item mt-2 text-sm text-gray-500 mb-8">
                    Cada pedido te aproxima de recompensas incríveis.
                </p>

                <ul class="animate-item space-y-4 text-left">
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-6 h-6 rounded-full bg-yellow-100 flex-shrink-0 flex items-center justify-center">
                            <i class="fa-solid fa-plus text-xs text-yellow-600"></i>
                        </span>
                        <span class="text-sm text-gray-700">
                            <strong class="text-gray-900">Ganhe pontos</strong> a cada pedido realizado
                        </span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-6 h-6 rounded-full bg-yellow-100 flex-shrink-0 flex items-center justify-center">
                            <i class="fa-solid fa-arrow-up text-xs text-yellow-600"></i>
                        </span>
                        <span class="text-sm text-gray-700">
                            <strong class="text-gray-900">Suba de nível</strong> no programa fidelidade Aloha
                        </span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-6 h-6 rounded-full bg-yellow-100 flex-shrink-0 flex items-center justify-center">
                            <i class="fa-solid fa-trophy text-xs text-yellow-600"></i>
                        </span>
                        <span class="text-sm text-gray-700">
                            <strong class="text-gray-900">Troque por recompensas</strong> exclusivas
                        </span>
                    </li>
                </ul>

            </div>
        </div>

        {{-- ══ SLIDE 5: Em breve — Cupons ══ --}}
        <div class="slide bg-gradient-to-b from-white to-green-50" data-slide="4">
            <div class="max-w-sm w-full text-center">

                <div class="animate-item flex justify-center mb-2">
                    <span class="badge-soon inline-flex items-center gap-1.5 rounded-full bg-green-100 px-4 py-1.5 text-xs font-bold text-green-700 uppercase tracking-wide">
                        <i class="fa-solid fa-clock text-xs"></i> Em breve
                    </span>
                </div>

                <div class="animate-item flex justify-center my-5">
                    <div class="icon-float w-24 h-24 rounded-full bg-orange-50 border border-orange-100 flex items-center justify-center shadow-inner">
                        <i class="fa-solid fa-ticket text-5xl text-orange-500"></i>
                    </div>
                </div>

                <h2 class="animate-item text-2xl font-bold text-gray-900">Descontos exclusivos Aloha</h2>
                <p class="animate-item mt-2 text-sm text-gray-500 mb-8">
                    Benefícios que só clientes do app têm acesso.
                </p>

                <ul class="animate-item space-y-4 text-left">
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-6 h-6 rounded-full bg-orange-100 flex-shrink-0 flex items-center justify-center">
                            <i class="fa-solid fa-percent text-xs text-orange-500"></i>
                        </span>
                        <span class="text-sm text-gray-700">
                            <strong class="text-gray-900">Cupons especiais</strong> direto no aplicativo
                        </span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-6 h-6 rounded-full bg-orange-100 flex-shrink-0 flex items-center justify-center">
                            <i class="fa-solid fa-bolt text-xs text-orange-500"></i>
                        </span>
                        <span class="text-sm text-gray-700">
                            <strong class="text-gray-900">Ofertas relâmpago</strong> por tempo limitado
                        </span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-6 h-6 rounded-full bg-orange-100 flex-shrink-0 flex items-center justify-center">
                            <i class="fa-solid fa-layer-group text-xs text-orange-500"></i>
                        </span>
                        <span class="text-sm text-gray-700">
                            <strong class="text-gray-900">Desconto por volume</strong> de pedidos
                        </span>
                    </li>
                </ul>

            </div>
        </div>

        {{-- ══ SLIDE 6: Em breve — Brindes ══ --}}
        <div class="slide bg-gradient-to-b from-green-50 to-white" data-slide="5">
            <div class="max-w-sm w-full text-center">

                <div class="animate-item flex justify-center mb-2">
                    <span class="badge-soon inline-flex items-center gap-1.5 rounded-full bg-green-100 px-4 py-1.5 text-xs font-bold text-green-700 uppercase tracking-wide">
                        <i class="fa-solid fa-clock text-xs"></i> Em breve
                    </span>
                </div>

                <div class="animate-item flex justify-center my-5">
                    <div class="icon-float w-24 h-24 rounded-full bg-purple-50 border border-purple-100 flex items-center justify-center shadow-inner">
                        <i class="fa-solid fa-gift text-5xl text-purple-500"></i>
                    </div>
                </div>

                <h2 class="animate-item text-2xl font-bold text-gray-900">Fidelidade tem recompensa</h2>
                <p class="animate-item mt-2 text-sm text-gray-500 mb-8">
                    Quanto mais você pede, mais você merece.
                </p>

                <ul class="animate-item space-y-4 text-left">
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-6 h-6 rounded-full bg-purple-100 flex-shrink-0 flex items-center justify-center">
                            <i class="fa-solid fa-box-open text-xs text-purple-500"></i>
                        </span>
                        <span class="text-sm text-gray-700">
                            <strong class="text-gray-900">Brindes exclusivos</strong> para clientes fiéis
                        </span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-6 h-6 rounded-full bg-purple-100 flex-shrink-0 flex items-center justify-center">
                            <i class="fa-solid fa-crown text-xs text-purple-500"></i>
                        </span>
                        <span class="text-sm text-gray-700">
                            <strong class="text-gray-900">Status VIP</strong> com vantagens diferenciadas
                        </span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-6 h-6 rounded-full bg-purple-100 flex-shrink-0 flex items-center justify-center">
                            <i class="fa-solid fa-bell text-xs text-purple-500"></i>
                        </span>
                        <span class="text-sm text-gray-700">
                            <strong class="text-gray-900">Acesso antecipado</strong> a novidades e promoções
                        </span>
                    </li>
                </ul>

            </div>
        </div>

        {{-- ══ SLIDE 7: CTA Final ══ --}}
        <div class="slide bg-green-600" data-slide="6">
            <div class="max-w-sm w-full text-center">

                <div class="animate-item text-6xl mb-6 select-none">🎉</div>

                <h2 class="animate-item text-3xl font-extrabold text-white leading-tight">
                    Comece agora mesmo
                </h2>

                <p class="animate-item mt-3 text-green-100 text-base leading-relaxed">
                    Crie sua conta e peça seu primeiro gelo Aloha<br>
                    <strong class="text-white">Rápido, prático e feito para o seu drink.</strong>
                </p>

                <div class="animate-item mt-10 space-y-3 w-full">
                    <a href="{{ route('register') }}"
                        class="btn-shimmer w-full flex justify-center items-center gap-2 rounded-xl bg-white px-4 py-4 text-base font-bold text-green-700 shadow-xl active:scale-95 transition-transform">
                        <i class="fa-solid fa-user-plus"></i> Criar conta agora
                    </a>
                    <a href="{{ route('login') }}"
                        class="w-full flex justify-center items-center rounded-xl border border-green-500 px-4 py-3.5 text-sm font-medium text-green-100 active:scale-95 transition-transform">
                        Já tenho conta &rarr; Entrar
                    </a>
                </div>

                <p class="animate-item mt-8 text-green-200 text-xs">
                    Junte-se a quem já pede pelo Aloha App
                </p>

            </div>
        </div>

    </div>{{-- /slides-track --}}
</div>{{-- /slider-wrapper --}}

{{-- ── Nav footer (setas + dots) ── --}}
<div id="nav-footer">
    <button id="btn-prev" class="nav-arrow" aria-label="Slide anterior">
        <i class="fa-solid fa-chevron-left"></i>
    </button>

    <div id="dots-container">
        @for ($i = 0; $i < 7; $i++)
            <button class="dot {{ $i === 0 ? 'dot-active' : '' }}"
                data-index="{{ $i }}"
                aria-label="Ir para slide {{ $i + 1 }}"></button>
        @endfor
    </div>

    <button id="btn-next" class="nav-arrow" aria-label="Próximo slide">
        <i class="fa-solid fa-chevron-right"></i>
    </button>
</div>

<script>
// ── PWA Gate ──────────────────────────────────────────────────────────────────
(function () {
    const gate = document.getElementById('pwa-gate');

    // Se já está rodando como PWA instalado (standalone), oculta o gate
    const isStandalone =
        window.matchMedia('(display-mode: standalone)').matches ||
        window.navigator.standalone === true; // iOS Safari

    if (isStandalone) {
        gate.classList.add('hidden');
        return;
    }

    const isIOS = /iphone|ipad|ipod/i.test(navigator.userAgent);
    const isAndroidChrome = /android/i.test(navigator.userAgent) && /chrome/i.test(navigator.userAgent);

    let deferredPrompt = null;

    window.addEventListener('beforeinstallprompt', (e) => {
        if (isIOS) return; // iOS não suporta — nunca mostra o botão
        e.preventDefault();
        deferredPrompt = e;
        const btn = document.getElementById('pwa-install-btn');
        btn.classList.remove('hidden');

        btn.addEventListener('click', async () => {
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            if (outcome === 'accepted') {
                gate.classList.add('hidden');
            }
            deferredPrompt = null;
        });
    });

    // App instalado via beforeinstallprompt
    window.addEventListener('appinstalled', () => {
        gate.classList.add('hidden');
    });

    // Botão "Continuar sem instalar"
    document.getElementById('pwa-skip-btn').addEventListener('click', () => {
        gate.classList.add('hidden');
    });

    // Se iOS não tem beforeinstallprompt — mostra instruções manuais
    if (isIOS) {
        document.getElementById('pwa-ios-hint').classList.remove('hidden');
    } else if (!isAndroidChrome) {
        // Outros navegadores que podem não ter o evento: mostra dica genérica
        // O botão ainda aparecerá se o evento for disparado; caso contrário, mostra hint
        setTimeout(() => {
            if (!deferredPrompt && document.getElementById('pwa-install-btn').classList.contains('hidden')) {
                document.getElementById('pwa-generic-hint').classList.remove('hidden');
            }
        }, 1500);
    }
})();
</script>

<script>
(function () {
    const track   = document.getElementById('slides-track');
    const dots    = document.querySelectorAll('.dot');
    const slides  = document.querySelectorAll('.slide');
    const footer  = document.getElementById('nav-footer');
    const btnPrev = document.getElementById('btn-prev');
    const btnNext = document.getElementById('btn-next');
    const TOTAL   = 7;
    let   current = 0;
    let   startX  = 0;
    let   startY  = 0;
    let   dragging = false;

    function updateDots() {
        dots.forEach((dot, i) => dot.classList.toggle('dot-active', i === current));
        btnPrev.classList.toggle('hidden-arrow', current === 0);
        btnNext.classList.toggle('hidden-arrow', current === TOTAL - 1);
        footer.classList.toggle('on-last', current === TOTAL - 1);
    }

    function activateSlide(index) {
        slides.forEach(s => s.classList.remove('active'));
        void slides[index].offsetWidth;
        slides[index].classList.add('active');
    }

    function goTo(index) {
        if (index < 0 || index >= TOTAL) return;
        current = index;
        track.style.transform = `translateX(calc(-${current} * 100vw))`;
        updateDots();
        activateSlide(current);
    }

    // Botão slide 1 → avança para slide 2
    document.getElementById('btn-start').addEventListener('click', () => goTo(1));

    // Setas
    btnPrev.addEventListener('click', () => goTo(current - 1));
    btnNext.addEventListener('click', () => goTo(current + 1));

    // Dot clicks
    dots.forEach((dot, i) => dot.addEventListener('click', () => goTo(i)));

    // Touch swipe
    document.addEventListener('touchstart', e => {
        startX   = e.touches[0].clientX;
        startY   = e.touches[0].clientY;
        dragging = true;
    }, { passive: true });

    document.addEventListener('touchend', e => {
        if (!dragging) return;
        const dx = e.changedTouches[0].clientX - startX;
        const dy = e.changedTouches[0].clientY - startY;
        if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 42) {
            if (dx < 0) goTo(current + 1);
            else         goTo(current - 1);
        }
        dragging = false;
    }, { passive: true });

    // Keyboard (desktop)
    document.addEventListener('keydown', e => {
        if (e.key === 'ArrowRight') goTo(current + 1);
        if (e.key === 'ArrowLeft')  goTo(current - 1);
    });

    // Init
    activateSlide(0);
    updateDots();
})();
</script>

@endsection
