Context.io provides an email inbox monitoring service which will fire webhooks.

This package gets that webhook, separates out all the necessary data from it then using the message_id, does another request to context.io for the email's body and finally sends that information onwards to a URL defined in the .env file.

## config

### Setting up webhook

I usually do a simple POST to `https://api.context.io/lite/webhooks` with the following data

```php
<?php
callback_url => 'http://my-id.ngrok.io/test-incoming', // ngrok is a reverse Http proxy tunnel software that allows me to test webhooks using homestead.
filter_to => 'your@email.com', // This isn't necessary, just used for testing
include_body => 1 // Include the email body with the webhook. I don't personally use this option as I want to keep the initial payload size down. Setting this option will likely break things as I haven't *YET* added checks for this.
```

### .env

```bash
CONTEXT_KEY=your_context.io_key
CONTEXT_SECRET=your_context.io_secret
MAILTONOTES_RECEIVER='https://your-crm.com/endpoint-for-receiving-new-note
```

### Publish migrations

> php artisan vendor:publish --provider="Stokoe\MailToNotes\Providers\MailToNotesServiceProvider"

## More notes

### Regex for finding emails in body

```
From.*(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])
```
