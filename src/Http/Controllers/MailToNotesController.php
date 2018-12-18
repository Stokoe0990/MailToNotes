<?php

namespace Stokoe\MailToNotes\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Stokoe\MailToNotes\Models\Payload;
use Stokoe\MailToNotes\Jobs\ProcessContextEmail;

class MailToNotesController extends BaseController
{
    public function context(Request $request)
    {
        try {
            return response(200);
        } finally {
            $payload = file_get_contents('php://input');
            $json = json_decode($payload);

            $full_payload = Payload::create([
                'payload' => $payload ? $payload : 'Nothing - was this a test webhook?',
            ]);

            if (self::verifySignature($json->signature, $json->timestamp, $json->token)) {
                ProcessContextEmail::dispatch($full_payload);
            }
        }
    }

    public static function verifySignature($signature, $timestamp, $token)
    {
        return $signature == hash_hmac('sha256', $timestamp.$token, env('CONTEXT_SECRET'));
    }
}
