@extends('layouts.admin')
@section('title', 'Cupons')
@section('content')

    <div class="mb-6 flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Cupons</h1>
            <p class="text-sm text-gray-500 mt-0.5">Crie e gerencie cupons de desconto.</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}"
            class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition whitespace-nowrap">
            <i class="fas fa-plus text-xs"></i> Criar Cupom
        </a>
    </div>

    @if(session('success'))
        <div class="mb-5 flex items-center gap-2 rounded-xl bg-green-50 px-4 py-3 text-sm font-medium text-green-700 ring-1 ring-green-200">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 flex items-center gap-2 rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700 ring-1 ring-red-200">
            <i class="fas fa-circle-xmark"></i> {{ session('error') }}
        </div>
    @endif

    <div class="mb-5 rounded-xl bg-white shadow-sm ring-1 ring-black/5 px-4 py-3">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Código</label>
                <div class="relative">
                    <i class="fas fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Ex: PROMO10"
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 pl-9 pr-4 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-green-500 transition">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Status</label>
                <select name="status"
                    class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-green-500 transition">
                    <option value="">Todos</option>
                    <option value="1" {{ $status === '1' ? 'selected' : '' }}>Ativo</option>
                    <option value="0" {{ $status === '0' ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>
            <button type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-green-700 transition shadow-sm">
                <i class="fas fa-filter text-xs"></i> Filtrar
            </button>
            @if($search || ($status !== null && $status !== ''))
                <a href="{{ route('admin.coupons.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                    <i class="fas fa-xmark text-xs"></i> Limpar
                </a>
            @endif
        </form>
    </div>

    <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 overflow-hidden overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead>
                <tr class="bg-green-50">
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-green-800 uppercase tracking-wide">Código</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-green-800 uppercase tracking-wide">Tipo</th>
                    <th class="hidden sm:table-cell px-5 py-3.5 text-left text-xs font-semibold text-green-800 uppercase tracking-wide">Valor</th>
                    <th class="hidden md:table-cell px-5 py-3.5 text-left text-xs font-semibold text-green-800 uppercase tracking-wide">Pedido mín.</th>
                    <th class="hidden lg:table-cell px-5 py-3.5 text-left text-xs font-semibold text-green-800 uppercase tracking-wide">Validade</th>
                    <th class="hidden md:table-cell px-5 py-3.5 text-left text-xs font-semibold text-green-800 uppercase tracking-wide">Visib.</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-green-800 uppercase tracking-wide">Status</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-green-800 uppercase tracking-wide">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($coupons as $coupon)
                    <tr class="hover:bg-green-50/40 transition-colors">
                        <td class="px-5 py-4 text-sm font-mono font-semibold text-gray-900 tracking-wider">{{ $coupon->code }}</td>
                        <td class="px-5 py-4 text-sm">
                            @if($coupon->discount_type === 'percent')
                                <span class="inline-flex items-center gap-1 rounded-full bg-purple-100 px-2.5 py-1 text-xs font-medium text-purple-700">
                                    <i class="fas fa-percent text-xs"></i> Percentual
                                </span>
                            @elseif($coupon->discount_type === 'fixed')
                                <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-700">
                                    <i class="fas fa-tag text-xs"></i> Fixo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-cyan-100 px-2.5 py-1 text-xs font-medium text-cyan-700">
                                    <i class="fas fa-truck text-xs"></i> Frete Grátis
                                </span>
                            @endif
                        </td>
                        <td class="hidden sm:table-cell px-5 py-4 text-sm text-gray-700">
                            @if($coupon->discount_type === 'percent')
                                {{ number_format($coupon->discount_value, 2, ',', '.') }}%
                            @elseif($coupon->discount_type === 'fixed')
                                R$ {{ number_format($coupon->discount_value, 2, ',', '.') }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="hidden md:table-cell px-5 py-4 text-sm text-gray-600">
                            {{ $coupon->min_order_value ? 'R$ ' . number_format($coupon->min_order_value, 2, ',', '.') : '—' }}
                        </td>
                        <td class="hidden lg:table-cell px-5 py-4 text-sm text-gray-600">
                            {{ $coupon->expires_at ? $coupon->expires_at->format('d/m/Y') : 'Sem validade' }}
                        </td>
                        <td class="hidden md:table-cell px-5 py-4">
                            @if($coupon->is_public)
                                <span class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-medium text-indigo-700">Público</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-500">Privado</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($coupon->is_active)
                                <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                                    <i class="fas fa-circle text-[6px]"></i> Ativo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-500">
                                    <i class="fas fa-circle text-[6px]"></i> Inativo
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('admin.coupons.edit', $coupon) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-amber-700 bg-amber-50 hover:bg-amber-100 transition">
                                    <i class="fas fa-pen-to-square text-xs"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.coupons.toggle', $coupon) }}">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition {{ $coupon->is_active ? 'text-orange-600 bg-orange-50 hover:bg-orange-100' : 'text-green-700 bg-green-50 hover:bg-green-100' }}"
                                        title="{{ $coupon->is_active ? 'Desativar' : 'Ativar' }}">
                                        <i class="fas {{ $coupon->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }} text-sm"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition"
                                        onclick="return confirm('Tem certeza que deseja excluir o cupom {{ $coupon->code }}?')">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-14 text-center">
                            <i class="fas fa-ticket text-4xl text-gray-200 block mb-3"></i>
                            <span class="text-sm text-gray-400">Nenhum cupom encontrado.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $coupons->links() }}</div>

@endsection
