<?php

namespace App\View\Components;

use App\Services\AdServingService;
use App\Services\AdTrackingService;
use Illuminate\View\Component;

class AdBanner extends Component
{
    public string $placement;
    public ?string $county;
    public ?object $ad = null;

    public function __construct(string $placement, ?string $county = null)
    {
        $this->placement = $placement;
        $this->county = $county;

        // Fetch matching ad using AdServingService
        $this->ad = AdServingService::getAdForPlacement($placement, $county);

        // Record genuine impression if ad found
        if ($this->ad) {
            AdTrackingService::recordImpression($this->ad);
        }
    }

    public function render()
    {
        return view('components.ad-banner');
    }
}
