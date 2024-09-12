# tawk.to Live Chat

Free live chat widget for your site

***
**Development and releases have been moved to this [fork](https://github.com/tawk/tawk-magento-2-package) because the package has been published on Packagist under a new module name. To maintain support for previous installations, we have decided to archive this repository. Please visit the fork for the latest releases and support.**
***

## Description

The tawk.to Live Chat app makes it easy to monitor and chat with visitors on your website. Be there when they need you with unlimited messaging, ticketing and your own Knowledge Base — all 100% FREE.

Compatible with all modern browsers, tawk.to was created in response to the growing need for businesses to respond in real time, with real people.

Never lose another lead or sale again — tawk.to offers iOS, Android, Windows and Mac OSX apps to keep you connected wherever you go.

Don’t have a tawk.to account yet? [Create one here.](https://tawk.to/?utm_source=zencart&utm_medium=link&utm_campaign=signup)

## Installation

### Pre-requisites
- Be sure Composer is installed. You can install it by entering in your website root directory and executing this command: `curl -sS https://getcomposer.org/installer | php`

### Standard Installation (Recommended)
1. Add these repositories to your Composer repositories by executing the following commands:
	- `php composer.phar config repositories.tawk-url-utils vcs "https://github.com/tawk/tawk-url-utils.git"`
	- `php composer.phar config repositories.tawk vcs "https://github.com/tawk/tawk-magento-2.git"`
2. Install the extension by executing `php composer.phar require tawk/widget`.

### Composer Artifact Installation
1. Download the latest zip file [here](https://github.com/tawk/tawk-magento-2/releases).
2. Create a folder in `<magento-installation-root-folder>` called `artifacts`.
3. Copy the zip file to `<magento-installation-root-folder>/artifacts`.
4. Add the repositories to your Composer repositories by executing the following commands
	- `php composer.phar config repositories.tawk-url-utils vcs "https://github.com/tawk/tawk-url-utils.git"`
	- `php composer.phar config repositories.tawk artifact "<magento-installation-root-folder>/artifacts"`
5. Install the extension by executing `php composer.phar require tawk/widget`.

### Manual Installation
1. Download the latest zip file [here](https://github.com/tawk/tawk-magento-2/releases).
2. Extract the package.
3. Copy the contents to `<magento-installation-root-folder>/app/code/Tawk/Widget` folder of your website (create a new folder if necessary).
4. Add the dependency repository to your Composer repositories by executing `php composer.phar config repositories.tawk-url-utils vcs "https://github.com/tawk/tawk-url-utils.git"`.
5. Install the dependency `tawk-url-util` by executing `php composer.phar require tawk/url-utils`.

## Updating to v1.6.0
For users using previous versions than v1.6.0, there are additional setups that needs to be made before running the `php bin/magento setup:upgrade` command.

### Standard Installation
1. Add this new repository by running `php composer.phar config repositories.tawk-url-utils vcs "https://github.com/tawk/tawk-url-utils.git"`.
2. Run `php composer.phar require tawk/widget` to update the extension and install the new dependencies.
3. Then run the following magento commands to upgrade the extension.
```
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
php bin/magento cache:clean
```

### Manual Installation
1. Download the latest zip file [here](https://github.com/tawk/tawk-magento-2/releases).
2. Extract the package.
3. Copy the contents to `<magento-installation-root-folder>/app/code/Tawk/Widget` folder of your website (create a new folder if necessary).
4. Add the dependency repository to your Composer repositories by executing `php composer.phar config repositories.tawk-url-utils vcs "https://github.com/tawk/tawk-url-utils.git"`.
5. Install the dependency `tawk-url-util` by executing `php composer.phar require tawk/url-utils`.
3. Then run the following magento commands to upgrade the extension.
```
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
php bin/magento cache:clean
```

## Enabling the Extension
Once the extension is installed, you will need to execute the following command lines from your website root directory:
```
php bin/magento module:enable Tawk_Widget
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
php bin/magento cache:clean
```

## Widget Configuration
1. Go to `Dashboard` -> `System` -> `tawk.to widget` -> `Select your widget`.
2. Log in to your tawk.to account.
3. Select the property and the widget you want to place on your store and click `Use selected widget`.
4. The widget will now appear on your store.

## Frequently Asked Questions
Visit our [Help Center](https://help.tawk.to/) for answers to FAQs
