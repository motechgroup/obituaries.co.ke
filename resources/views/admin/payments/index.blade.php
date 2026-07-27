@extends('layouts.admin')

@section('title', 'Payment Audit Log | Admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-serif text-3xl font-bold text-slate-900">M-Pesa Payment Audit Log</h1>
            <p class="text-slate-500 text-sm mt-1">Full transaction history and callback payloads.</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-3.5">ID</th>
                        <th class="px-6 py-3.5">M-Pesa Receipt</th>
                        <th class="px-6 py-3.5">Phone Number</th>
                        <th class="px-6 py-3.5">Obituary Notice</th>
                        <th class="px-6 py-3.5">Amount</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($payments as $pay)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-xs font-mono text-slate-500">#{{ $pay->id }}</td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-900 text-xs">
                                {{ $pay->mpesa_receipt_number ?? 'Pending' }}
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-slate-800">
                                {{ $pay->phone_number }}
                            </td>
                            <td class="px-6 py-4 text-xs">
                                @if($pay->obituary)
                                    <a href="{{ route('admin.obituaries.show', $pay->obituary->id) }}" class="text-amber-700 font-semibold hover:underline">
                                        {{ $pay->obituary->full_name }}
                                    </a>
                                @else
                                    <span class="text-slate-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-slate-900">
                                KES {{ number_format($pay->amount, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                @if($pay->status === 'completed')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Completed</span>
                                @elseif($pay->status === 'failed')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">Failed</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $pay->created_at->format('M d, Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">
                                No payment transactions recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200">
            {{ $payments->links() }}
        </div>
    </div>
</div>
@endsection
