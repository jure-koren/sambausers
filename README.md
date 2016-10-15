## Read Me ##

Web interface to add/edit/delete samba users and groups.

This is supposed to be run on a secured system. Use apache config to restrict access to the app.

## Configuration ##

1. Secure the server an allow only authorized access (ie apache ldap or pam)

2. Add sudo access for apache with visudo (huge risk if there are other apps on this server!)

# this is probably the default
Defaults    requiretty
 
# allow access to samba tool without a tty
Defaults!/usr/bin/samba-tool !requiretty

# allow www-data to run the tool without a pass
www-data ALL=NOPASSWD: /usr/bin/samba-tool

