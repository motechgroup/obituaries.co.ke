@extends('layouts.advertiser')

@section('title', 'Pay Campaign with M-Pesa')

@section('content')
<div x-data="checkoutApp()" class="max-w-xl mx-auto py-6">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6 shadow-2xl">
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 text-emerald-400 mx-auto flex items-center justify-center font-bold">
                <span class="material-symbols-outlined text-[32px]">payments</span>
            </div>
            <h1 class="font-serif text-2xl font-bold text-white">M-Pesa STK Push Checkout</h1>
            <p class="text-xs text-slate-400">Complete payment to activate your advertisement campaign on Obituaries.co.ke.</p>
        </div>

        <div class="bg-slate-950 p-4 rounded-2xl border border-slate-800 space-y-2 text-xs">
            <div class="flex justify-between">
                <span class="text-slate-400">Campaign Name:</span>
                <strong class="text-white">{{ $campaign->name }}</strong>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400">Placement Slot:</span>
                <strong class="text-white">{{ $campaign->placement->name }}</strong>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400">Banner Size:</span>
                <strong class="text-white font-mono">{{ $campaign->bannerSize->dimensions }}</strong>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400">Duration:</span>
                <strong class="text-white">{{ $campaign->start_date->format('M d') }} &mdash; {{ $campaign->end_date->format('M d, Y') }} ({{ $campaign->total_days }} Days)</strong>
            </div>
            <div class="flex justify-between border-t border-slate-800 pt-2 text-sm font-bold">
                <span class="text-amber-400">Total Amount Payable:</span>
                <span class="text-amber-400 font-mono">KES {{ number_format($campaign->calculated_price) }}</span>
            </div>
        </div>

        <!-- Notification Message Box -->
        <div x-show="message" x-transition class="p-4 rounded-2xl text-xs font-bold flex items-center space-x-2" :class="isSuccess ? 'bg-emerald-950 border border-emerald-800 text-emerald-300' : 'bg-rose-950 border border-rose-800 text-rose-300'">
            <span class="material-symbols-outlined text-[20px]" x-text="isSuccess ? 'check_circle' : 'error'"></span>
            <span x-text="message"></span>
        </div>

        <!-- Phone input form -->
        <form @submit.prevent="submitStkPush()" class="space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">M-Pesa Phone Number <span class="text-amber-400">*</span></label>
                <input type="text" x-model="phoneNumber" required placeholder="0712345678" class="w-full px-4 py-3.5 bg-slate-950 border border-slate-800 rounded-xl text-base font-mono font-bold text-white focus:border-emerald-500 outline-none tracking-wider">
                <p class="text-[11px] text-slate-400 mt-1">An M-Pesa STK Push PIN prompt will be sent directly to this phone.</p>
            </div>

            <button type="submit" :disabled="loading" class="w-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold py-4 px-4 rounded-xl text-xs uppercase tracking-wider transition-all shadow-xl flex items-center justify-center space-x-2 disabled:opacity-50">
                <template x-if="!loading">
                    <div class="flex items-center space-x-2">
                        <span class="material-symbols-outlined text-[20px]">smartphone</span>
                        <span>Pay KES {{ number_format($campaign->calculated_price) }} via M-Pesa &rarr;</span>
                    </div>
                </template>
                <template x-if="loading">
                    <div class="flex items-center space-x-2">
                        <svg class="animate-spin h-4 w-4 text-slate-950" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Waiting for M-Pesa PIN input...</span>
                    </div>
                </template>
            </button>
        </form>
    </div>
</div>

<script>
function checkoutApp() {
    return {
        phoneNumber: '{{ $advertiser->phone_number }}',
        loading: false,
        message: '',
        isSuccess: true,

        submitStkPush() {
            this.loading = true;
            this.message = '';

            fetch('{{ route("advertiser.campaigns.stkpush", $campaign->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ phone_number: this.phoneNumber })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.isSuccess = true;
                    this.message = data.message;
                    if (data.mock) {
                        setTimeout(() => {
                            window.location.href = '{{ route("advertiser.campaigns.show", $campaign->id) }}';
                        }, 1500);
                    } else {
                        this.pollStatus();
                    }
                } else {
                    this.loading = false;
                    this.isSuccess = false;
                    this.message = data.error || 'Payment failed to initiate.';
                }
            })
            .catch(err => {
                this.loading = false;
                this.isSuccess = false;
                this.message = 'Network error initiating M-Pesa prompt.';
            });
        },

        pollStatus() {
            let attempts = 0;
            const interval = setInterval(() => {
                attempts++;
                fetch('{{ route("advertiser.campaigns.check-status", $campaign->id) }}')
                    .then(res => res.json())
                    .then(data => {
                        if (data.completed) {
                            clearInterval(interval);
                            this.message = 'Payment Confirmed! Redirecting to campaign details...';
                            setTimeout(() => {
                                window.location.href = '{{ route("advertiser.campaigns.show", $campaign->id) }}';
                            }, 1000);
                        }
                    });

                if (attempts > 30) {
                    clearInterval(interval);
                    this.loading = false;
                    this.message = 'Payment confirmation timed out. If you paid, refresh in a minute.';
                }
            }, 3000);
        }
    }
}
</script>
@endsection
