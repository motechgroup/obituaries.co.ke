@if($ad)
    <div class="my-6 text-center max-w-full overflow-hidden group">
        <div class="inline-block relative">
            <span class="absolute top-2 left-2 bg-slate-900/80 backdrop-blur-xs text-slate-300 text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded border border-slate-700/60 z-10">
                Sponsored
            </span>
            <a href="{{ route('ad.click', $ad->id) }}" target="_blank" rel="noopener sponsor" class="block">
                <img src="{{ $ad->banner_url }}" alt="{{ $ad->name }}" class="max-w-full h-auto rounded-2xl shadow-lg border border-slate-200 dark:border-slate-800 hover:opacity-95 transition-all mx-auto">
            </a>
        </div>
    </div>
@endif
