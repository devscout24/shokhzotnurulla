<?php
namespace App\Services\Integrations;

use App\Models\Dealership\Dealer;
use Illuminate\Support\Collection;

class ScriptInjector
{
    protected Dealer $dealer;
    protected Collection $operational;

    public function __construct(Dealer $dealer)
    {
        $this->dealer      = $dealer;
        $this->operational = $dealer->integrations()
            ->operational()
            ->get()
            ->keyBy('provider');
    }

    /**
     * Google Analytics 4 — <head> snippet.
     */
    public function ga4Head(): string
    {
        $integration = $this->operational->get('ga4');
        if (! $integration) {
            return '';
        }

        $measurementId = $integration->getSetting('measurement_id', '');
        if (! $measurementId) {
            return '';
        }

        return <<<HTML
    <!-- Google Analytics 4 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={$measurementId}"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{$measurementId}');
    </script>
    HTML;
    }

    /**
     * Google Tag Manager — <head> snippet.
     */
    public function gtmHead(): string
    {
        $integration = $this->operational->get('gtm');
        if (! $integration) {
            return '';
        }

        $containerId = $integration->getSetting('container_id', '');
        if (! $containerId) {
            return '';
        }

        return <<<HTML
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{$containerId}');</script>
HTML;
    }

    /**
     * Google Tag Manager — <body> noscript fallback.
     */
    public function gtmBody(): string
    {
        $integration = $this->operational->get('gtm');
        if (! $integration) {
            return '';
        }

        $containerId = $integration->getSetting('container_id', '');
        if (! $containerId) {
            return '';
        }

        return <<<HTML
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={$containerId}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
HTML;
    }

    /**
     * CarNow embed script (if configured as a script-type integration).
     */
    public function carnowScript(): string
    {
        $integration = $this->operational->get('carnow');
        if (! $integration) {
            return '';
        }

        $embedUrl = $integration->getSetting('embed_script', '');
        if (! $embedUrl) {
            return '';
        }

        return <<<HTML
<!-- CarNow -->
<script src="{$embedUrl}" async></script>
HTML;
    }
}
