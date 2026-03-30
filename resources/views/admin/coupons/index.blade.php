@extends('layouts.admin')
@section('title', 'Cupons')
@section('content')

<div class="flex justify-between items-center mb-4">
    <h1 class="text-3xl font-bold">Cupons</h1>
    <a href="{{ route('admin.coupons.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
        <i class="fa-solid fa-plus mr-1"></i> Criar Cupom
    </a>
</div>

@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg">{{ session('error') }}</div>
@endif

<form method="GET" class="mb-6 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Buscar por código</label>
        <input type="text" name="search" value="{{ $search }}" placeholder="Ex: PROMO10"
            class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <select name="status" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            <option value="">Todos</option>
            <option value="1" {{ $status === '1' ? 'selected' : '' }}>Ativo</option>
            <option value="0" {{ $status === '0' ? 'selected' : '' }}>Inativo</option>
        </select>
    </div>
    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
        <i class="fa-solid fa-magnifying-glass mr-1"></i> Filtrar
    </button>
    @if($search || $status !== null && $status !== '')
        <a href="{{ route('admin.coupons.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">Limpar</a>
    @endif
</form>

<div class="overflow-x-auto">
    <table class="min-w-full border border-gray-400 border-collapse">
        <thead class="bg-gray-100">
            <tr>
                <th class="border border-gray-300 px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Código</th>
                <th class="border border-gray-300 px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Tipo</th>
                <th class="border border-gray-300 px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Valor</th>
                <th class="border border-gray-300 px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Pedido mínimo</th>
                <th class="border border-gray-300 px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Validade</th>
                <th class="border border-gray-300 px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Status</th>
                <th class="border border-gray-300 px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Ações</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($coupons as $coupon)
                <tr class="hover:bg-gray-50 transition">
                    <td class="border border-gray-300 px-4 py-3 text-sm font-mono font-semibold">{{ $coupon->code }}</td>
                    <td class="border border-gray-300 px-4 py-3 text-sm">
                        {{ $coupon->discount_type === 'percent' ? 'Percentual' : 'Fixo' }}
                    </td>
                    <td class="border border-gray-300 px-4 py-3 text-sm">
                        @if($coupon->discount_type === 'percent')
                            {{ number_format($coupon->discount_value, 2, ',', '.') }}%
                        @else
                            R$ {{ number_format($coupon->discount_value, 2, ',', '.') }}
                        @endif
                    </td>
                    <td class="border border-gray-300 px-4 py-3 text-sm">
                        {{ $coupon->min_order_value ? 'R$ ' . number_format($coupon->min_order_value, 2, ',', '.') : '—' }}
                    </td>
                    <td class="border border-gray-300 px-4 py-3 text-sm">
                        @if($coupon->expires_at)
                            {{ $coupon->expires_at->format('d/m/Y') }}
                        @else
                            Sem validade
                        @endif
                    </td>
                    <td class="border border-gray-300 px-4 py-3 text-sm">
                        @if($coupon->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Ativo</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Inativo</span>
                        @endif
                    </td>
                    <td class="border border-gray-300 px-4 py-3 text-sm">
                        <div class="flex items-center gap-1 flex-wrap">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}"
                               class="px-3 py-1.5 text-sm font-medium text-yellow-600 bg-yellow-50 rounded-lg hover:bg-yellow-100">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>

                            <form method="POST" action="{{ route('admin.coupons.toggle', $coupon) }}" style="display:inline;">
                                @csrf
                                <button type="submit"
                                    class="px-3 py-1.5 text-sm font-medium rounded-lg {{ $coupon->is_active ? 'text-orange-600 bg-orange-50 hover:bg-orange-100' : 'text-green-600 bg-green-50 hover:bg-green-100' }}"
                                    title="{{ $coupon->is_active ? 'Desativar' : 'Ativar' }}">
                                    <i class="fa-solid {{ $coupon->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    onclick="return confirm('Tem certeza que deseja excluir o cupom {{ $coupon->code }}?')"
                                    class="px-3 py-1.5 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-6 text-center text-gray-500">Nenhum cupom encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $coupons->links() }}</div>

@endsection
