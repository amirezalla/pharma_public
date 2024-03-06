<?php
namespace App\Jobs;


use Botble\Ecommerce\Models\Offers;
use Botble\Ecommerce\Models\OffersDetail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class OfferDeactivationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $offerId;

    public function __construct($offerId)
    {
        $this->offerId = $offerId;
    }

    public function handle()
    {
        // Deactivate the offer
        $offer = Offers::find($this->offerId);
        $offer->active = false;
        $offer->save();

        $offerDetails = OffersDetail::where('offer_id', $this->offerId)->get();

        foreach ($offerDetails as $offerDetail) {
            $offerDetail->status = 'deactive';
            $offerDetail->save();
        }

        // Delete the job after deactivation
        $this->delete();
    }
}