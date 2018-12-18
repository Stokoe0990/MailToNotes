<?php

namespace Stokoe\MailToNotes\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Stokoe\MailToNotes\Models\Message;

class SendMessageToCrm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $uri;
    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct(Message $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $data = $this->data;
        $client = new Client();

        $response = $client->request('POST', env('MAILTONOTES_RECEIVER'), [
            'json' => [
                'message_id' => $data->message_id,
                'context_id' => $data->context_id,
                'sender' => $data->sender,
                'subject' => $data->subject,
                'recipients' => $data->recipients,
                'in_reply_to' => $data->in_reply_to,
                'references' => $data->references,
                'body' => $data->body,
            ],
        ]);

        $response = $response->getBody()->getContents();
    }
}
