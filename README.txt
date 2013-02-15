Wusmap: get the route map for your whatusea account.

This program uses infobox from http://google-maps-utility-library-v3.googlecode.com/

1) Requisites
- have an Whatusea account
- avoir un hébergement accessible sur internet avec php 5 (compilé avec imap mysql), mysql, possibilités de créer des taches planifiées (cron) - OVH mutualisé marche très bien
	check your phpinfo: Configure Command should have '--with-imap' and '--with-mysqli'
- avoir une adresse email qui peut être consultée en imap

2) Installation
a) In your whatusea.com account, add an email alert ticking "position as XML".
b) upload the files to a folder accessible to the web, for example www/wusmap. Make sure that the php files have the correct rights (755).
c) go to yoursite.com/wusmap/install.php and fill in the details of your database and email.
d) go to yoursite.com/wusmap/manage_assets.php and add your asset. To know what your asset id is, check an email you received from advanced tracking with the position as xml. It should be 
    <asset-identifier>
      <name>Asset name</name>
      <value>42</value>
    </asset-identifier>
Here the asset id is 42.
e) go to yoursite.com/wusmap/ and fill the form to get the url you want. If you don't know between Script and IFrame, you probably want IFrame.
d) add a daily cron task that launches /www/wusmap/checkemails.php (for ovh, in the manager/hosting/scheduled tasks)

3) Use
If you choose the IFrame output, just put the link as the source of an iframe, e.g. <iframe src="yoursite.com/wusmap/getmap.php?blablablah"></iframe>.
If you choose the Script output, you have to include it in the head of your page and you need to know the id of the element on your page where the map will be. You can study the iframe output, it should be straightforward.
