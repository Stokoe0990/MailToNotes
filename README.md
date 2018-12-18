# Ignore this

Context.io provides an email inbox monitoring service which will fire webhooks.
I set up a webhook to fire an event every time an email is received.
This package gets that email, separates out all the necessary data from the webhook, does another request to context.io for the email's body and finally sends that information onwards to a URL defined in the .env file.

## config

.env
```
CONTEXT_KEY=your_context.io_key
CONTEXT_SECRET=your_context.io_secret
MAILTONOTES_RECEIVER='https://your-crm.com/endpoint-for-receiving-new-note
```

Publish migrations:
> php artisan vendor:publish --provider="Stokoe\MailToNotes\Providers\MailToNotesServiceProvider"
