<?php

namespace Stokoe\MailToNotes\Jobs;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Stokoe\MailToNotes\Models\Message;
use Stokoe\MailToNotes\Models\Payload;

class ProcessContextEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    protected $payload;

    /**
     * Create a new job instance.
     */
    public function __construct(Payload $payload)
    {
        $this->payload = json_decode($payload->payload);
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $message_data = $this->payload->message_data;

        $message_id = self::removeChevrons($message_data->email_message_id);
        $recipients = self::getRecipientList($message_data->addresses);
        $uri = self::buildUri($this->payload);
        $body = self::getMessageBody($uri);

        $message = Message::create([
            'message_id' => $message_id,
            'context_id' => $message_data->message_id,
            'sender' => $message_data->addresses->from->email,
            'subject' => $message_data->subject,
            'recipients' => $recipients,
            'in_reply_to' => !empty($message_data->in_reply_to) ? self::removeChevrons($message_data->in_reply_to[0]) : null,
            'references' => !empty($message_data->references) ? self::removeChevrons($message_data->references[0]) : null,
            'body' => $body,
        ]);

        SendMessageToCrm::dispatch($message);
    }

    public static function getRecipientList($recipients)
    {
        $recips = [];
        foreach ($recipients->to as $address) {
            array_push($recips, $address->email);
        }

        return implode(', ', $recips);
    }

    public static function removeChevrons($string)
    {
        return str_replace(['<', '>'], '', $string);
    }

    public static function buildUri($json)
    {
        $account_id = $json->account_id;
        $account_label = $json->message_data->email_accounts[0]->label;
        $folder = 'INBOX';
        $message_id = self::removeChevrons($json->message_data->email_message_id);

        return 'lite/users/'.$account_id.'/email_accounts/'.$account_label.'/folders/'.$folder.'/messages/'.$message_id;
    }

    public static function getMessageBody(string $uri)
    {
        $stack = HandlerStack::create();

        $middleware = new Oauth1([
            'consumer_key' => env('CONTEXT_KEY'),
            'consumer_secret' => env('CONTEXT_SECRET'),
            'token_secret' => '',
            'token' => '',
        ]);

        $stack->push($middleware);

        $contextClient = new Client([
            'base_uri' => 'https://api.context.io/',
            'handler' => $stack,
        ]);

        try {
            $full_uri = $uri.'/body';
            $request = $contextClient->get($full_uri, ['auth' => 'oauth']);
            $response = json_decode($request->getBody()->getContents());
        } catch (Exception $e) {
        }

        $email_body = $response->bodies[0]->content;

        return trim($email_body);
    }
}
