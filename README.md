# Ignore this

Context.io provides an email inbox monitoring service which will fire webhooks.
I set up a webhook to fire an event every time an email is received.
This package gets that email, separates out all the necessary data from the webhook, does another request to context.io for the email's body and finally sends that information onwards to a URL defined in the .env file.
