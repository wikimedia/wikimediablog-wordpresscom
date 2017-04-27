Wikimedia Blog WordPress Theme
==============================

Development Setup
-----------------

1. Set up WordPress VIP environment per the [general documentation](https://vip.wordpress.com/documentation/vip/developers-guide-to-wordpress-com-vip/) 
2. Clone this theme repo into `www/wp-content/themes/vip/`

Deployment 
----------
## Via the GitHub mirror ##
Merge change to Master in GitHub, and then sync it to the production SVN as described at https://meta.wikimedia.org/wiki/Wikimedia_Blog/SVN-GitHub_mirror_of_the_WordPress_theme

## Directly to the Production SVN ##

* Setup existing theme directory as VIP svn repo, by doing an svn checkout within your working directory

		$ cd www/wp-content/themes/vip/wikimedia-blog
		$ svn checkout https://vip-svn.wordpress.com/wikimedia ./

* Make sure you're on the master branch in git... this is the one we want to keep in sync with VIP

		$ git checkout master

* Commit svn changes to deploy to VIP

		$ svn commit -m "New commit for deployment on WordPress VIP"

* After review and deployment by Automattic, sync the GitHub mirror as described at https://meta.wikimedia.org/wiki/Wikimedia_Blog/SVN-GitHub_mirror_of_the_WordPress_theme
