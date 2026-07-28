@extends('layouts.admin')

@section('title', 'Platform Settings & Gateways | Admin')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="{ activeTab: '{{ session('active_tab', 'branding') }}', testMailModal: false, testSmsModal: false }">
    <div>
        <h1 class="font-serif text-3xl font-bold text-slate-900">Platform Settings & Gateways</h1>
        <p class="text-slate-500 text-sm mt-1">Manage branding, footer contacts, payment gateway credentials, SMTP mail templates, and SMS settings.</p>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl font-bold flex items-center space-x-2">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl font-bold flex items-center space-x-2">
            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="flex items-center space-x-2 border-b border-slate-200 overflow-x-auto text-xs font-bold">
        <button type="button" @click="activeTab = 'branding'" :class="activeTab === 'branding' ? 'border-amber-600 text-amber-700 bg-amber-50' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-50'" class="px-4 py-3 border-b-2 rounded-t-xl transition-all">
            🎨 Branding & Footer
        </button>
        <button type="button" @click="activeTab = 'payment'" :class="activeTab === 'payment' ? 'border-amber-600 text-amber-700 bg-amber-50' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-50'" class="px-4 py-3 border-b-2 rounded-t-xl transition-all">
            💳 Payment Gateway & Pricing
        </button>
        <button type="button" @click="activeTab = 'smtp'" :class="activeTab === 'smtp' ? 'border-amber-600 text-amber-700 bg-amber-50' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-50'" class="px-4 py-3 border-b-2 rounded-t-xl transition-all">
            ✉️ SMTP Mail & Templates
        </button>
        <button type="button" @click="activeTab = 'sms'" :class="activeTab === 'sms' ? 'border-amber-600 text-amber-700 bg-amber-50' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-50'" class="px-4 py-3 border-b-2 rounded-t-xl transition-all">
            📱 SMS Gateway & Templates
        </button>
        <button type="button" @click="activeTab = 'database'" :class="activeTab === 'database' ? 'border-amber-600 text-amber-700 bg-amber-50' : 'border-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-50'" class="px-4 py-3 border-b-2 rounded-t-xl transition-all">
            ⚡ Database & Migrations
        </button>
    </div>

    <!-- TAB 5: DATABASE MAINTENANCE & MIGRATIONS (Independent Form) -->
    <div x-show="activeTab === 'database'" class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
            <div>
                <h3 class="font-serif text-xl font-bold text-slate-900">⚡ Online Database Maintenance & Migrations</h3>
                <p class="text-xs text-slate-500 mt-0.5">Run database migrations and seeders anytime on your live server directly from the admin dashboard.</p>
            </div>
            <a href="/run-migrations.php" target="_blank" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-bold transition-all">
                Open Web Migration Script &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Git Pull Box -->
            <div class="p-6 bg-slate-900 text-white rounded-2xl border border-slate-800 space-y-4 shadow-md">
                <div class="flex items-center space-x-2 text-sky-400 font-bold text-sm">
                    <span class="material-symbols-outlined text-[22px]">sync</span>
                    <span class="text-base">Git Pull Codebase</span>
                </div>
                <p class="text-xs text-slate-300 leading-relaxed">
                    Executes <code>git pull origin main</code> online to pull the latest source code from GitHub and clear cache.
                </p>
                <form action="{{ route('admin.system.git-pull') }}" method="POST" onsubmit="return confirm('Pull latest codebase from GitHub onto live server?')">
                    @csrf
                    <input type="hidden" name="active_tab" value="database">
                    <button type="submit" class="w-full bg-sky-500 hover:bg-sky-400 text-slate-950 font-bold py-3 px-4 rounded-xl text-xs uppercase tracking-wider transition-all shadow-md flex items-center justify-center space-x-2">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        <span>Pull Latest Code Now</span>
                    </button>
                </form>
            </div>

            <!-- Migrations Box -->
            <div class="p-6 bg-slate-900 text-white rounded-2xl border border-slate-800 space-y-4 shadow-md">
                <div class="flex items-center space-x-2 text-amber-400 font-bold text-sm">
                    <span class="material-symbols-outlined text-[22px]">bolt</span>
                    <span class="text-base">Run Database Migrations</span>
                </div>
                <p class="text-xs text-slate-300 leading-relaxed">
                    Executes <code>php artisan migrate --force</code> online to create missing tables and update database column schemas.
                </p>
                <form action="{{ route('admin.database.migrate') }}" method="POST" onsubmit="return confirm('Execute database migrations on live server?')">
                    @csrf
                    <input type="hidden" name="active_tab" value="database">
                    <button type="submit" class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold py-3 px-4 rounded-xl text-xs uppercase tracking-wider transition-all shadow-md flex items-center justify-center space-x-2">
                        <span class="material-symbols-outlined text-[18px]">play_arrow</span>
                        <span>Run Migrations Now</span>
                    </button>
                </form>
            </div>

            <!-- Seeders Box -->
            <div class="p-6 bg-slate-900 text-white rounded-2xl border border-slate-800 space-y-4 shadow-md">
                <div class="flex items-center space-x-2 text-emerald-400 font-bold text-sm">
                    <span class="material-symbols-outlined text-[22px]">potted_plant</span>
                    <span class="text-base">Run Database Seeders</span>
                </div>
                <p class="text-xs text-slate-300 leading-relaxed">
                    Executes <code>php artisan db:seed --force</code> online to populate default platform settings, admin roles, and sample data.
                </p>
                <form action="{{ route('admin.database.seed') }}" method="POST" onsubmit="return confirm('Populate default database seeders?')">
                    @csrf
                    <input type="hidden" name="active_tab" value="database">
                    <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold py-3 px-4 rounded-xl text-xs uppercase tracking-wider transition-all shadow-md flex items-center justify-center space-x-2">
                        <span class="material-symbols-outlined text-[18px]">nature</span>
                        <span>Run Seeders Now</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <input type="hidden" name="active_tab" x-model="activeTab">

        <!-- TAB 1: BRANDING & FOOTER -->
        <div x-show="activeTab === 'branding'" class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
            <h3 class="font-serif text-xl font-bold text-slate-900 border-b border-slate-200 pb-3">Website Branding & Footer Info</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">Site Title</label>
                    <input type="text" name="site_title" value="{{ old('site_title', $settings['site_title']) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">Site Tagline</label>
                    <input type="text" name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline']) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">Site Logo Upload</label>
                    <div class="p-3 bg-slate-50 border border-slate-300 rounded-xl flex items-center space-x-3">
                        @if($settings['logo'])
                            <img src="{{ asset('storage/' . $settings['logo']) }}" class="h-8 object-contain">
                        @endif
                        <input type="file" name="logo" accept="image/*" class="block w-full text-xs text-slate-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">Favicon Upload</label>
                    <div class="p-3 bg-slate-50 border border-slate-300 rounded-xl flex items-center space-x-3">
                        @if($settings['favicon'])
                            <img src="{{ asset('storage/' . $settings['favicon']) }}" class="w-6 h-6 object-contain">
                        @endif
                        <input type="file" name="favicon" accept="image/*" class="block w-full text-xs text-slate-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">Footer Phone Number</label>
                    <input type="text" name="footer_phone" value="{{ old('footer_phone', $settings['footer_phone']) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">Footer Email Address</label>
                    <input type="email" name="footer_email" value="{{ old('footer_email', $settings['footer_email']) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">Physical Address / Office</label>
                    <input type="text" name="footer_address" value="{{ old('footer_address', $settings['footer_address']) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">Copyright Text</label>
                    <input type="text" name="copyright_text" value="{{ old('copyright_text', $settings['copyright_text']) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>
            </div>
        </div>

        <!-- TAB 2: PAYMENT GATEWAY & PRICING -->
        <div x-show="activeTab === 'payment'" class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
            <h3 class="font-serif text-xl font-bold text-slate-900 border-b border-slate-200 pb-3">M-Pesa STK Push Payment Gateway & Publishing Pricing</h3>

            <div class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">Publishing Fee (KES)</label>
                        <input type="number" step="0.01" min="0" name="obituary_publishing_cost" value="{{ old('obituary_publishing_cost', $settings['obituary_publishing_cost']) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-base font-bold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">Post Approval Mode</label>
                        <select name="auto_publish_obituaries" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-900">
                            <option value="0" {{ (string)$settings['auto_publish_obituaries'] === '0' ? 'selected' : '' }}>🔒 Require Admin Approval (Default)</option>
                            <option value="1" {{ (string)$settings['auto_publish_obituaries'] === '1' ? 'selected' : '' }}>⚡ Auto-Publish Immediately Upon Payment</option>
                        </select>
                        <p class="text-[11px] text-slate-500 mt-1">When set to require approval, obituaries stay pending until verified by admin.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">Public Poster Details</label>
                        <select name="show_poster_details" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-900">
                            <option value="0" {{ (string)$settings['show_poster_details'] === '0' ? 'selected' : '' }}>🙈 Hide Poster/Submitter Details (Default)</option>
                            <option value="1" {{ (string)$settings['show_poster_details'] === '1' ? 'selected' : '' }}>👁️ Show Poster/Submitter Details Publicly</option>
                        </select>
                        <p class="text-[11px] text-slate-500 mt-1">Controls whether "Submitted by {Name}" appears on public obituary pages.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-200">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">Gateway Mode</label>
                        <select name="mpesa_mock_mode" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-bold text-amber-900">
                            <option value="0" {{ (string)$settings['mpesa_mock_mode'] === '0' ? 'selected' : '' }}>🟢 Live STK Push (Sends real STK prompt to phone)</option>
                            <option value="1" {{ (string)$settings['mpesa_mock_mode'] === '1' ? 'selected' : '' }}>🧪 Mock Simulation Mode (For offline testing)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">M-Pesa Environment</label>
                        <select name="mpesa_env" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold">
                            <option value="sandbox" {{ $settings['mpesa_env'] === 'sandbox' ? 'selected' : '' }}>Sandbox (Developer Testing)</option>
                            <option value="live" {{ $settings['mpesa_env'] === 'live' ? 'selected' : '' }}>Live Production (api.safaricom.co.ke)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">Transaction Type</label>
                        <select name="mpesa_transaction_type" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold">
                            <option value="CustomerPayBillOnline" {{ $settings['mpesa_transaction_type'] === 'CustomerPayBillOnline' ? 'selected' : '' }}>PayBill Number (CustomerPayBillOnline)</option>
                            <option value="CustomerBuyGoodsOnline" {{ $settings['mpesa_transaction_type'] === 'CustomerBuyGoodsOnline' ? 'selected' : '' }}>Buy Goods / Till Number (CustomerBuyGoodsOnline)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">Paybill / Shortcode</label>
                        <input type="text" name="mpesa_shortcode" value="{{ old('mpesa_shortcode', $settings['mpesa_shortcode']) }}" placeholder="e.g. 174379 or Paybill" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">Consumer Key</label>
                        <input type="text" name="mpesa_consumer_key" value="{{ old('mpesa_consumer_key', $settings['mpesa_consumer_key']) }}" placeholder="Daraja Consumer Key" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">Consumer Secret</label>
                        <input type="password" name="mpesa_consumer_secret" value="{{ old('mpesa_consumer_secret', $settings['mpesa_consumer_secret']) }}" placeholder="Daraja Consumer Secret" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">Lipa Na M-Pesa Passkey</label>
                        <input type="password" name="mpesa_passkey" value="{{ old('mpesa_passkey', $settings['mpesa_passkey']) }}" placeholder="Lipa Na M-Pesa Online Passkey" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono">
                    </div>

                    <div class="sm:col-span-2 p-4 bg-sky-50/70 rounded-2xl border border-sky-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold uppercase tracking-wider text-sky-900">🔗 M-Pesa Callback URL</label>
                            <span class="text-[10px] font-bold uppercase tracking-wider bg-sky-200 text-sky-900 px-2 py-0.5 rounded-full">Auto-Detect Active</span>
                        </div>
                        <input type="text" name="mpesa_callback_url" value="{{ old('mpesa_callback_url', $settings['mpesa_callback_url']) }}" placeholder="{{ route('api.mpesa.callback') }}" class="w-full px-4 py-2.5 bg-white border border-sky-300 rounded-xl text-xs font-mono font-bold text-sky-950">
                        <p class="text-[11px] text-sky-800">
                            Auto-detected system callback URL: <code class="font-bold bg-white px-2 py-0.5 rounded border border-sky-300 select-all">{{ route('api.mpesa.callback') }}</code>. Set this exact URL on your Safaricom Daraja Developer app.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: SMTP MAIL & TEMPLATES -->
        <div x-show="activeTab === 'smtp'" class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <h3 class="font-serif text-xl font-bold text-slate-900">SMTP Email Server & Mail Templates</h3>
                <button type="button" @click="testMailModal = true" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold transition-all flex items-center space-x-1.5">
                    <span>📧 Send Test Email</span>
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">SMTP Host</label>
                    <input type="text" name="mail_host" value="{{ old('mail_host', $settings['mail_host']) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">SMTP Port</label>
                    <input type="text" name="mail_port" value="{{ old('mail_port', $settings['mail_port']) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">SMTP Username</label>
                    <input type="text" name="mail_username" value="{{ old('mail_username', $settings['mail_username']) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">SMTP Password</label>
                    <input type="password" name="mail_password" value="{{ old('mail_password', $settings['mail_password']) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">From Address</label>
                    <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address']) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">From Sender Name</label>
                    <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name']) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">Approval & Verification Email Template</label>
                    <textarea name="mail_template_verification" rows="4" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono leading-relaxed">{{ old('mail_template_verification', $settings['mail_template_verification']) }}</textarea>
                    <span class="text-[10px] text-slate-400 block mt-1">Available placeholders: {NAME}, {DECEASED_NAME}, {LINK}</span>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">Rejection Email Template</label>
                    <textarea name="mail_template_rejection" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono leading-relaxed">{{ old('mail_template_rejection', $settings['mail_template_rejection']) }}</textarea>
                    <span class="text-[10px] text-slate-400 block mt-1">Available placeholders: {NAME}, {DECEASED_NAME}, {REASON}</span>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">Annual Anniversary Reminder Email Template</label>
                    <textarea name="mail_template_anniversary" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono leading-relaxed">{{ old('mail_template_anniversary', $settings['mail_template_anniversary']) }}</textarea>
                    <span class="text-[10px] text-slate-400 block mt-1">Available placeholders: {NAME}, {DECEASED_NAME}, {YEARS}, {LINK}</span>
                </div>
            </div>
        </div>

        <!-- TAB 4: SMS GATEWAY & TEMPLATES -->
        <div x-show="activeTab === 'sms'" class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <h3 class="font-serif text-xl font-bold text-slate-900">SMS Gateway & SMS Templates</h3>
                <button type="button" @click="testSmsModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition-all flex items-center space-x-1.5">
                    <span>📱 Send Test SMS</span>
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">SMS Provider</label>
                    <select name="sms_provider" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold">
                        <option value="textsms" {{ $settings['sms_provider'] === 'textsms' ? 'selected' : '' }}>TextSMS Kenya (textsms.co.ke)</option>
                        <option value="africastalking" {{ $settings['sms_provider'] === 'africastalking' ? 'selected' : '' }}>Africa's Talking</option>
                        <option value="mobitech" {{ $settings['sms_provider'] === 'mobitech' ? 'selected' : '' }}>Mobitech SMS</option>
                        <option value="generic" {{ $settings['sms_provider'] === 'generic' ? 'selected' : '' }}>Generic HTTP API</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">Sender ID / Alphanumeric</label>
                    <input type="text" name="sms_sender_id" value="{{ old('sms_sender_id', $settings['sms_sender_id']) }}" placeholder="OBITUARIES" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-bold">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">SMS API Key</label>
                    <input type="password" name="sms_api_key" value="{{ old('sms_api_key', $settings['sms_api_key']) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">SMS Partner ID / User ID (TextSMS & Mobitech)</label>
                    <input type="text" name="sms_partner_id" value="{{ old('sms_partner_id', $settings['sms_partner_id']) }}" placeholder="e.g. 1234" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">Obituary Submission SMS Template</label>
                    <textarea name="sms_template_submission" rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono">{{ old('sms_template_submission', $settings['sms_template_submission']) }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">Obituary Approval SMS Template</label>
                    <textarea name="sms_template_approval" rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono">{{ old('sms_template_approval', $settings['sms_template_approval']) }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">Obituary Rejection SMS Template</label>
                    <textarea name="sms_template_rejection" rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono">{{ old('sms_template_rejection', $settings['sms_template_rejection']) }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-1.5">Annual Anniversary Reminder SMS Template</label>
                    <textarea name="sms_template_anniversary" rows="2" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono">{{ old('sms_template_anniversary', $settings['sms_template_anniversary']) }}</textarea>
                </div>
            </div>
        </div>

        <div class="pt-4 flex justify-end">
            <button type="submit" class="px-8 py-3 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-xs uppercase tracking-wider transition-all shadow-md">
                Save All Platform Settings
            </button>
        </div>
    </form>

    <!-- Test Mail Modal -->
    <div x-show="testMailModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200 space-y-6" @click.away="testMailModal = false">
            <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                <h3 class="font-serif text-xl font-bold text-slate-900">📧 Send Test Email</h3>
                <button type="button" @click="testMailModal = false" class="text-slate-400 font-bold hover:text-slate-600">&times;</button>
            </div>

            <form action="{{ route('admin.settings.test-mail') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Recipient Email Address</label>
                    <input type="email" name="test_email" required value="{{ Auth::user()->email ?? 'admin@obituaries.co.ke' }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div class="pt-2 flex justify-end space-x-3">
                    <button type="button" @click="testMailModal = false" class="px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 bg-sky-600 hover:bg-sky-500 text-white font-bold rounded-xl text-xs uppercase tracking-wider">
                        Send Test Email
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Test SMS Modal -->
    <div x-show="testSmsModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200 space-y-6" @click.away="testSmsModal = false">
            <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                <h3 class="font-serif text-xl font-bold text-slate-900">📱 Send Test SMS</h3>
                <button type="button" @click="testSmsModal = false" class="text-slate-400 font-bold hover:text-slate-600">&times;</button>
            </div>

            <form action="{{ route('admin.settings.test-sms') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1.5">Recipient Phone Number</label>
                    <input type="tel" name="test_phone" required placeholder="e.g. 0712345678" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div class="pt-2 flex justify-end space-x-3">
                    <button type="button" @click="testSmsModal = false" class="px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs uppercase tracking-wider">
                        Send Test SMS
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
