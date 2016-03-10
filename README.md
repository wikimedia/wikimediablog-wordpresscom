Wikimedia Blog WordPress Theme
==============================

Development Setup
-----------------

1. Setup WordPress [VIP quickstart](https://github.com/Automattic/vip-quickstart) environment
2. Clone this theme repo into `www/wp-content/themes/vip/`

Deployment
----------

## Staging ###

Use standard git deployment

* Add your public ssh key to the authorized_keys file on the staging server: `/home/git/.ssh/authorized_keys`
* Add the staging server as a git remote

		$ git remote add staging git@wikimediablog.staging.exygy.com:/home/git/wikimedia-blog

* Push your changes, as normal

		$ git push staging master


## Production ##

* Setup existing theme directory as VIP svn repo, by doing an svn checkout within your working directory

		$ cd www/wp-content/themes/vip/wikimedia-blog
		$ svn checkout https://vip-svn.wordpress.com/wikimedia ./

* Make sure you're on the master branch in git... this is the one we want to keep in sync with VIP

		$ git checkout master

* Commit svn changes to deploy to VIP

		$ svn commit -m "New commit for deployment on WordPress VIP"
