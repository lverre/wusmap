Wusmap: get the route map for your whatusea account.

This program uses infobox from http://google-maps-utility-library-v3.googlecode.com/
This project is hosted by Github: http://lverre.github.com/wusmap

1) Requirement
- have a whatusea.com account
- have a web hosting with php 5 (with imap and mysqli modules), a mysql database and the option to create scheduled tasks
	check your phpinfo: Configure Command should have '--with-imap' and '--with-mysqli'
	I personally use OVH but most hosting companies should do
- have an email address that has imap access

2) Installation
a) In your whatusea.com account, go to Alert / Contacts for alerts / Add contact; there, enter your email address (my advice is to create an email address for that... don't use your personal one) and tick the "position as XML" checkbox
b) upload the files (https://github.com/lverre/wusmap/zipball/master) to a folder accessible to the web, for example www/wusmap (ake sure that the php files have the correct rights: 755)
c) go to yoursite.com/wusmap/install.php and fill in the details of your database and email.
d) go to yoursite.com/wusmap/manage_assets.php and add your asset. To know what your asset id is, check an email you received from advanced tracking with the position as xml. It should be 
    <asset-identifier>
      <name>Asset name</name>
      <value>42</value>
    </asset-identifier>
Here the asset id is 42.
e) go to yoursite.com/wusmap/ and fill the form to get the url you want. If you don't know between Script and IFrame, you probably want IFrame.
d) add a daily scheduled task that launches /www/wusmap/checkemails.php (for OVH, in the manager/hosting/scheduled tasks)

3) Use
If you choose the IFrame output, just put the link as the source of an iframe, e.g. <iframe src="yoursite.com/wusmap/getmap.php?blablablah"></iframe>.
If you choose the Script output, you have to include it in the head of your page and you need to know the id of the element on your page where the map will be. You can study the iframe output, it should be straightforward.
