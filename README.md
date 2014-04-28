ifttt-webhook / Instagram to Little Printer
=============

A webhook middleware for the ifttt.com service which allows printing direct to Little Printer.

#How To Use

If you want to simply print your Instgram photos via Little Printer, just follow the instructions below.

1. Setup a new WordPress channel.
2. Set blog url to iflpthenprint.nfshost.com 
3. Go here http://remote.bergcloud.com/developers/littleprinter/direct_print_codes and get your Direct Print Code. Set this to be the username in the Channel settings. 
4. Set the password to be anything :)
5. Activate this recipe https://ifttt.com/recipes/167179-if-lp-then-send-to-littleprinter

Any problems Tweet [@deplorableword](https://twitter.com/@deplorableword)

#How It Works

Based of an existing project called [ifttt webhook](https://github.com/captn3m0/ifttt-webhook), it essentitally presents a fake 
fake-xmlrpc interface on the webadress, which causes ifttt to be fooled into thinking of this as a genuine wordpress blog. The only action that ifttt allows for wordpress is posting, which is used for powering webhooks. All the other fields (title, categories) along with the username/password credentials are passed along by the webhook. This hack simply posts these values to the [Little Printer Direct Print API](http://remote.bergcloud.com/developers/littleprinter/direct_print_codes)

#Licence
Licenced under GPL. Some portions of the code are from wordpress itself. 