# Coil Monetize Content

This is the source code repository for the Coil WordPress plugin. Coil Monetize Content allows you to monetize content for all readers, subscribers only, or if you are using modern WordPress, at the block level.

---

* [Onboarding](#onboarding)
* [Local Development Environment](#local-development-environment)
* [Development Process](#development-process)
* [Deployment](#deployment)

---

# Onboarding

To be onboarded to the project you'll need the following:

## From the Scrum Master

* Access to [Jira](https://jira.pragmatic.agency/secure/RapidBoard.jspa?rapidView=871&projectKey=CWP).
* Access to [Confluence](https://confluence.pragmatic.agency/display/COIL/Coil+Home).

## From the Project Lead or Team Lead

The below can be granted by contacting your team lead, or asking in #support-internal on Pragmatic Slack.

* Join #int-coil on Pragmatic Slack.
* Write access to the Git repository on [Bitbucket](https://bitbucket.org/pragmaticweb/coil-monetize-content/).
* TBC: WP Engine environment(s) access.


# Local Development Environment

It's recommended that you use [Local by Flywheel](https://localbyflywheel.com/) for your local development environment.

The nature of this project (a plugin) means that it is environment-agnostic, so if you prefer to install and administer WordPress using another method, then please do so.

## Setting up the Development Environment Using Flywheel

Ensure you have the prerequisite software installed:

* [PHP](https://php.net/) 7.1+
* [Composer](https://getcomposer.org/) 1.8+
* [Local By Flywheel](https://localbyflywheel.com/community/t/local-by-flywheel-3-3-0/13527) 3.3.0+
* [Node](https://nodejs.org/) 10.15+

Install the development environment:

1. Set up a new site in Flywheel:
	 - Name the new site `coil`.
	 - Choose the latest PHP version available.
	 - This project does not require a specific Web Server or MySQL version; if in doubt, choose nginx and MySQL 5.6.
1. After Flywheel has created the site, from inside the root of where you chose to create the site, clone this repository into the plugins folder:
	 - `git clone git@bitbucket.org:pragmaticweb/coil-monetize-content.git app/public/wp-content/plugins/coil-monetize-content`
1. When the machine has finished provisioning, install the development dependencies:
	 - `cd app/public/wp-content/plugins/coil-monetize-content && composer install && npm install`

Flywhell will give you the URL of where you can access your environment, and you'll have supplied the initial user account's username and password during Flywheel set up.


# Development Process

Before you begin committing code, [double check that you have the correct email address configured for this particular Git repo](https://help.github.com/articles/setting-your-email-in-git/#setting-your-email-address-for-a-single-repository). It's likely that this should be your **work** email address instead of a **personal** email address.

The development process mostly follows Pragmatic's standard take on [Git Flow](http://jeffkreeftmeijer.com/2010/why-arent-you-using-git-flow/).

Development happens in feature branches, which are merged into `develop`, and then eventually merged into `master` for deployment to production. When making changes, a feature branch should be created that branches from `develop` (and the corresponding merge request on BitBucket should use `develop` as the target branch).

## Assets (CSS & JS)

### `npm start`
- Use to compile and run the block in development mode.
- Watches for any changes and reports back any errors in your code.

### `npm run build`
- Use to build production code for your block inside `dist` folder.
- Runs once and reports back the gzip file sizes of the produced code.


# Deployment

TBC.
