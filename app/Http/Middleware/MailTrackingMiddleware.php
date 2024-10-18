<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class MailTrackingMiddleware
{
    public function handle($request, Closure $next)
    {
        $trackid = $request->query('trackid');
        $destid = $request->query('destid');

        // Check if the current URL is the unsubscribe page
        // If yes, set the unsubscribe flag to 1, otherwise 0
        $unsubscribe = $request->is('*disiscrizione-newsletter*') ? 1 : 0;

        // If both 'trackid' and 'destid' are present in the URL
        if ($trackid && $destid) {
            // Query the database to find an existing entry with the provided 'trackid' and 'destid'
            $entry = DB::connection('mysql2')->table('fa_mail_tracking')->where('fk_mail_id', $trackid)->where('fk_recipient_id', $destid)->first();

            // If no matching record is found in the database
            if (!$entry) {
                // Insert a new record with the provided details
                DB::connection('mysql2')->table('fa_mail_tracking')->insert([
                    'fk_mail_id' => $trackid,
                    'fk_recipient_id' => $destid,
                    'visite' => 1,
                    'unscribe' => $unsubscribe,
                    'ts_prima_visita' => now(),
                    'ts_ultima_visita' => now()
                ]);
            } else {
                // If a matching record exists, update it by incrementing the 'visite' count
                // and updating the 'ts_ultima_visita' timestamp
                DB::connection('mysql2')->table('fa_mail_tracking')->where('fk_mail_id', $trackid)->where('fk_recipient_id', $destid)->update([
                    'visite' => $entry->visite + 1,
                    'unscribe' => $unsubscribe,
                    'ts_ultima_visita' => now()
                ]);
            }
            // Extract other query parameters without 'trackid' and 'destid'
            $queryWithoutTracking = http_build_query($request->except(['trackid', 'destid']));

            // Construct the new URL
            $urlWithoutTracking = $request->url();

            // If there are other query parameters, append them
            if ($queryWithoutTracking) {
                $urlWithoutTracking .= '?' . $queryWithoutTracking;
            }          
            if($request->is('*disiscrizione-dalla-newsletter-marigo-pharma*') || $request->is('*disiscrizione-newsletter*') ){

            }
            else{
                //at the end reset the url without parameters
                return redirect($urlWithoutTracking);
            }
            

        }

        return $next($request);
    }
}
