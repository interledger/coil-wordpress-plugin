# Coil Web Monetization

This is the source code repository for the Coil WordPress plugin. Coil Web Monetization allows you to monetize content for all readers, members only, or if you are using modern WordPress, at the block level.

---

* [Onboarding](#markdown-header-onboarding)
* [Local Development Environment](#markdown-header-local-development-environment)
* [Development Process](#markdown-header-development-process)
* [Deployment](#markdown-header-deployment)

---

# Onboarding

To be onboarded to the project you'll need the following:

## From the Scrum Master

* Access to [Jira](https://jira.pragmatic.agency/secure/RapidBoard.jspa?rapidView=871&projectKey=CWP).
* Access to [Confluence](https://confluence.pragmatic.agency/display/COIL/Coil+Home).

## From the Project Lead or Team Lead

The below can be granted by contacting your team lead, or asking in #support-internal on Pragmatic Slack.

* Join #int-coil on Pragmatic Slack.
* Write access to the Git repository on [Bitbucket](https://bitbucket.org/pragmaticweb/coil-web-monetization/).
* WP Engine environment(s) access.


# Local Development Environment

It's recommended that you use [Local by Flywheel](https://localbyflywheel.com/) for your local development environment.

The nature of this project (a plugin) means that it is environment-agnostic, so if you prefer to install and administer WordPress using another method, then please do so.

## Setting up the Development Environment using Flywheel

Ensure you have the prerequisite software installed:

* [PHP](https://php.net/) 7.1+
* [Composer](https://getcomposer.org/) 1.8+, installed globally.
* [Local By Flywheel](https://localbyflywheel.com/community/t/local-by-flywheel-3-3-0/13527) 3.3.0
* [Node](https://nodejs.org/) 10.15+
* An account on [Coil.com](https://coil.com/).
	- Register with your Pragmatic email address, and sign up as a "Content Creator".

Install the development environment:

1. Set up a new site in Flywheel:
	- Name the new site `coil`.
	- Choose the latest PHP version available.
	- This project does not require a specific Web Server or MySQL version; if in doubt, choose nginx and MySQL 5.6.
1. After Flywheel has created the site, from inside the root of where you chose to create the site, clone this repository into the plugins folder:
	- `git clone git@bitbucket.org:pragmaticweb/coil-web-monetization.git app/public/wp-content/plugins/coil-web-monetization`
1. When the machine has finished provisioning, install the development dependencies:
	- `cd app/public/wp-content/plugins/coil-web-monetization && composer install && npm install`
1. To configure Flywheel for PHPUnit:
	- Download the following script to the base of your`app/` folder:
		- `curl -o setup-phpunit.sh https://gist.githubusercontent.com/keesiemeijer/a888f3d9609478b310c2d952644891ba/raw/`
	- In Flywheel, right-click your "Coil" site and select "Open Site SSH". This will open a new terminal, and inside it, do: `bash /app/setup-phpunit.sh`
	- The script wil take several minutes to install PHPUnit. Once it's done, you can close the terminal.

There is no sample database to import.

Flywheel will give you the URL of where you can access your environment, and you'll have supplied the initial user account's username and password during Flywheel set up.


# Development Process

Before you begin committing code, [double check that you have the correct email address configured for this particular Git repo](https://help.github.com/articles/setting-your-email-in-git/#setting-your-email-address-for-a-single-repository). It's likely that this should be your **work** email address instead of a **personal** email address.

The development process mostly follows Pragmatic's standard take on [Git Flow](http://jeffkreeftmeijer.com/2010/why-arent-you-using-git-flow/).

Development happens in feature branches, which are merged into `develop`, and then eventually merged into `master` for deployment to production. When making changes, a feature branch should be created that branches from `develop` (and the corresponding merge request on BitBucket should use `develop` as the target branch).

## Plugin Structure

This plugin maintains a basic file structure:

* `plugin.php` – The main plugin file.
* `includes/` – The plugin's backend/PHP code.
* `assets/` – Directory containing any static assets:
	* `css/` – Compiled CSS to be served to the frontend.
	* `fonts/` – Web fonts, if any.
	* `images/` – Images.
	* `js/` – JavaScript files.
	* `scss/` – SCSS source files.
* `tests/` – Test files.
* `README.md` – Plugin developer readme.

Within the `includes/` directory, files should be organised hierarchically based on namespaces. If a namespace only contains functions, the filename should be `functions.php`.

If a namespace also contains classes, group those classes together with the namespace file in a `{sub-namespace}/` directory. The main functions file for this namespace would be`{sub-namespace}/functions.php`, while classes should be in a file prefixed with `class-` with a "slugified" class name.

This plugin also has some complex JavaScript frontends, so there is additional structure to better match the ecosystem tooling:

* `src/` – Source JavaScript and CSS files.
* `build/` – Built assets, typically generated by Webpack and Babel

## Assets (CSS & JS)

Some of the tooling for this plugin came from the `create-guten-block` project, so we have two different ways to manage CSS and JS.

### Block Assets (`src/`)
#### `npm start`
- Use to compile and run the block in development mode.
- Watches for any changes and reports back any errors in your code.

#### `npm run build`
- Use to build production code for your block inside the `dist/` folder.
- Runs once and reports back the gzip file sizes of the produced code.

### Other Assets (`assets/{js,scss}/`)
#### `npx grunt watch`
- Use to compile and run the assets in development mode.
- Watches for any changes and reports back any errors in your code.

#### `npx grunt build`
- Use to build production code for your assets inside the `assets/{css,js}` folders.

## Integration Tests
New tests should be written where it makes sense. With Flywheel, the PHPUnit tests have to be run inside the Flywheel environment. To do this:

- In Flywheel, right-click your "Coil" site and select "Open Site SSH". This will open a new terminal.
- Inside it, change into the plugin folder: `cd /app/public/wp-content/plugins/coil-web-monetization`.
- To run all tests, simply do: `phpunit`.

At this moment in time, the tests are not run automatically on a Bitbucket workflow.


# Deployment

TBC.
