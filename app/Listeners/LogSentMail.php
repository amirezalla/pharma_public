<?php
namespace App\Listeners;

use Illuminate\Mail\Events\MessageSent;
use App\Models\EmailLog;
use Botble\Ecommerce\Models\Customer;

class LogSentMail
{
    public function handle(MessageSent $event)
    {
        $recipients = $event->message->getTo();

        $emailContent = '';

        if ($event->message->getBody() instanceof \Symfony\Component\Mime\Part\Multipart\AlternativePart) {
            $parts = $event->message->getBody()->getParts();

            foreach ($parts as $part) {
                if ($part instanceof \Symfony\Component\Mime\Part\TextPart && $part->getMediaType() === 'text/plain') {
                    $emailContent = quoted_printable_decode($part->bodyToString());
                    break;
                }
            }
        } elseif ($event->message->getBody() instanceof \Symfony\Component\Mime\Part\TextPart) {
            $emailContent = quoted_printable_decode($event->message->getBody()->bodyToString());
        }

        if (empty($emailContent)) {
            $strippedContent = strip_tags($event->message->getBody()->bodyToString());

            // Split content into lines and filter out empty or whitespace-only lines
            $lines = array_filter(array_map('trim', explode("\n", $strippedContent)));

            // Rejoin the lines to form the content
            $emailContent = implode("\n", $lines);
        }

    foreach ($recipients as $recipient) {
        $recipientEmail = $recipient->getAddress();
        $user=Customer::where('email',$recipientEmail)->first();
        if($user!=null){
            $codice_cliente=$user->codice;
            $nome_cliente=$user->name;

            EmailLog::create([
                'codice_cliente' => isset($codice_cliente) && $codice_cliente ? $codice_cliente : ' ',
                'nome_cliente' => $nome_cliente,
                'email_destinatario' => $recipientEmail,
                'oggetto' => $event->message->getSubject(),
                'email' => $emailContent,
                'data_invio' => now(),
            ]);
        }else{
            // dd($recipientEmail,'doesnt has an account!',$event->message->getSubject());
            // dd($recipientEmail,'doesnt has an account!',$event->message->getSubject());
        }

    }

    }
}
