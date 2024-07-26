=== Noda Pay Button ===
Contributors: nodateam, cybalex
Tags: ecommerce, payments, e-commerce, checkout, button
Requires at least: 5.3
Tested up to: 6.3.1
Requires PHP: 7.0.3
Stable tag: 1.1.1
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.txt

== Description ==

Easy and safe way to process online payments using Noda payment button plugin

== Prior steps ==

Before proceeding with the plugin installation and configuration, ensure you've completed the onboarding process, signed the contract with Noda and obtained access to the Production API keys.
If you haven't completed these steps, please visit Noda HUB [https://ui.noda.live/hub/] and follow the provided step-by-step guide.

== Plugin requirements ==

PHP version

Please ensure your PHP version is a minimum of 7.0.3.
You can verify this by navigating to 'Tools' > 'Site Health' > 'Info' > 'Server' in your WordPress admin panel.
Any version of PHP between 7.0.3 and 8.2.x is supported.


Wordpress version

Please note that a WordPress version of 5.3 or newer is necessary.
To check your version, you can follow the same steps as checking the PHP version (refer to 1.1), but this time, it's located in the 'WordPress' tab under 'Tools' > 'Site Health' > 'Info' > 'WordPress'.

== Plugin requirements ==

The plugin comes fully equipped with everything you need, requiring no additional plugins or special requirements beyond the fitting versions of PHP and WordPress.

== Plugin installation ==

Installation from archive

Ensure you have the 'noda-button.zip' archive file ready for plugin installation. Follow these steps in the admin tool to install the plugin:
> Access the WordPress admin panel
> Navigate to 'Plugins' > 'Add New' > 'Upload Plugin
> Click 'Choose File' > Select 'noda-button.zip'
> Click the 'Install Now' button
After the page reloads, you will see a successful installation message. The plugin is now installed and ready for configuration.

== Configuration of plugin ==

Wordpress configuration

No additional actions are required for configuration of the plugin. Installation is fully automated

Plugin Configuration


To view the list of available settings, please navigate to Noda settings in main menu of admin dashboard.
It is required to specify currency in the settings. Supported currencies are EUR, GBP, CAD, BRL, PLN, BGN and RON

The default values for 'Api Key', 'Signature', and 'Shop Id', pre-filled upon plugin installation, are intended for testing purposes only.
In testing mode, payments are processed, but no real money transfers occur.
To transition to live, real payments, follow these steps:
> Disable the 'Is test mode' option
> Replace the default  'Api Key', 'Signature', and 'Shop Id' values with your organization's specific credentials, which can be accessed in your Noda HUB personal account [https://ui.noda.live/hub/integration].

== Plugin additional options ==

Hide for anonymous

If this option is selected, then the anonymous users will not have an access to payment buttons

== How to add a payment button ==

> Navigate to any page in your Wordpress site admin tool at Pages -> All pages
> For any page you select click on the "Edit" page link
> Press the "+" button in the content to open the add block popup
> Search for Noda pay button block (start typing in the search block input field)
> Click on the block to add it to the page
> Press update page link after the Button options are configured (see next section for  more details on Button options)

== Button options ==

When adding Noda pay button block to a page the following options can be configured:

 > Price of purchase
 > Description of purchase
 > Background color of button
 > Border color of button

Please note that currency configuration is not done here.
You can set it globally in the Noda plugin settings under the options section.
This ensures that all payment buttons on your WordPress site will process payments in the selected currency.

The button is added as a block

== Headless Wordpress ==

The Noda Payment plugin offers an API sufficient for implementing Noda payments in the headless version of WordPress.
You can utilize the following endpoints to seamlessly integrate the frontend of your choice with the WordPress backend.

> POST /wp-json/noda-button/logo: Retrieve the payment button's URL.
> POST /wp-json/noda-button/pay-url: Generate a payment link for redirecting users to complete the payment.
> GET /wp-json/noda-button/payment-notification: Obtain payment information to be notified about changes in payment status.
> PATCH /wp-json/noda-button/payment-notification: Update the payment status as needed.

== Customizations of buttons and notification modal windows

To customize the appearance of payment buttons or payment notification modals, you can easily override the styles used for these elements in your custom WordPress theme.
For instance, if you want to modify the Noda payment button, simply override the "noda-pay-button" class.
This allows you to tailor the visual aspects to seamlessly integrate with your site's theme.

== Upgrade plugin versions ==

To upgrade the plugin to a newer major version (e.g., version 2.x.x), kindly uninstall the previous version of the plugin entirely before proceeding with the installation.
