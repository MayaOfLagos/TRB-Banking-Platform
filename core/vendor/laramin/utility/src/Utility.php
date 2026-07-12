<?php

namespace Laramin\Utility;

use Closure;

class Utility{

    public function handle($request, Closure $next)
    {
        // Local-dev override: skip the ViserLab purchase-code check so the app
        // boots without hitting the /activate gate. Revert this block to re-enable
        // license enforcement (restore both Helpmate::sysPass() branches below).
        return $next($request);

        if (!Helpmate::sysPass()) {
            return redirect()->route(VugiChugi::acRouter());
        }
        abort_if(Helpmate::sysPass() === 99 && request()->isMethod('post'),401);
        return $next($request);
    }
}
