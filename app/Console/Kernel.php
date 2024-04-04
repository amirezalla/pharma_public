<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->command('queue:work --timeout=60 --tries=2 --once')
//            ->everyMinute()
//            ->withoutOverlapping();
        // $schedule->command('inspire')->hourly();

        $schedule->command('queue:work --timeout=60 --tries=2 --once')
        ->everyMinute()
        ->withoutOverlapping();
        try {

            // Your task code
                $schedule->call(function () {
                    // Code for the second request
                    $client = new Client();
                    $response = $client->request('GET', 'https://marigopharma.it/productImportSchedule');

                // Get the status code of the response.
                $statusCode = $response->getStatusCode();

                // Get the body of the response.
                $body = $response->getBody()->getContents();
                // Get the status code of the response.
                $statusCode = $response->getStatusCode();
                $body = $response->getBody()->getContents();

                // Get the body of the response.
                $message = "product scheduled task run successfully at " . now() .
                                ". The status code was " . $statusCode .
                                " and the response was " . $body;
                        Log::info($message);

                        // Send the email
                        $receipents=['a.allahverdi@icoa.it','allahverdiamirreza@gmail.com'];
                        Mail::to($receipents)->send(new \App\Mail\ScheduledTaskCompleted($message));
                })->everyMinute();

        } catch (\Exception $e) {
            Log::error("Error in scheduled task: " . $e->getMessage());
        }



        try {

            // Your task code
            $schedule->call(function () {
                // Code for the second request

                $client = new Client();
                $response = $client->request('GET', 'https://marigopharma.it/pricelistImportSchedule');
                // Optional: Handle the response

                // Get the status code of the response.
                $statusCode = $response->getStatusCode();
                $body = $response->getBody()->getContents();

                // Get the body of the response.
                $message = "PriceList scheduled task run successfully at " . now() .
                                ". The status code was " . $statusCode .
                                " and the response was " . $body;

                Log::info($message);

                // Send the email
                $receipents=['a.allahverdi@icoa.it','allahverdiamirreza@gmail.com'];
                Mail::to($receipents)->send(new \App\Mail\ScheduledTaskCompleted($message));
            })->everyMinute();

        } catch (\Exception $e) {
            Log::error("Error in scheduled task: " . $e->getMessage());
        }

        // try {

        //     // Your task code
        //     $schedule->call(function () {
        //         // Code for the second request
        //         $client = new Client();
        //         $response = $client->request('GET', '//marigolab.it/clientiImportSchedule');
        //         // Optional: Handle the response

        //         // Get the status code of the response.
        //         $statusCode = $response->getStatusCode();
        //         $body = $response->getBody()->getContents();

        //         // Get the body of the response.
        //         $message = "clienti scheduled task run successfully at " . now() .
        //                         ". The status code was " . $statusCode .
        //                         " and the response was " . $body;

        //                 Log::info($message);

        //                 // Send the email
        //                 $receipents=['a.allahverdi@icoa.it','s.akbarzadeh@icoa.it','d.mazzucchi@icoa.it','pdileva@marigoitalia.it'];
        //                 Mail::to($receipents)->send(new \App\Mail\ScheduledTaskCompleted($message));

        //     })->dailyAt('02:00');

        //     } catch (\Exception $e) {
        //         Log::error("Error in scheduled task: " . $e->getMessage());
        //     }






            // try {
            //     // Your task code
            //         $schedule->call(function () {
            //             // Code for the second request
            //             $client = new Client();
            //             $response = $client->request('GET', 'https://marigopharma.it/ExpiringImportSchedule');

            //         // Get the status code of the response.
            //         $statusCode = $response->getStatusCode();

            //         // Get the body of the response.
            //         $body = $response->getBody()->getContents();
            //         // Get the status code of the response.
            //         $statusCode = $response->getStatusCode();
            //         $body = $response->getBody()->getContents();

            //         // Get the body of the response.
            //         $message = "Expiring Update scheduled task run successfully at " . now() .
            //                         ". The status code was " . $statusCode .
            //                         " and the response was " . $body;
            //                 Log::info($message);

            //                 // Send the email
            //                 $receipents=['a.allahverdi@icoa.it','s.akbarzadeh@icoa.it','d.mazzucchi@icoa.it','pdileva@marigoitalia.it'];
            //                 Mail::to($receipents)->send(new \App\Mail\ScheduledTaskCompleted($message));
            //         })->dailyAt('02:00');

            // } catch (\Exception $e) {
            //     Log::error("Error in scheduled task: " . $e->getMessage());
            // }





}
        




    

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
