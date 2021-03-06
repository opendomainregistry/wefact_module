Open Domain Registry module for WeFact/HostFact
===============================================

Adds support of Open Domain Registry to WeFact/HostFact.
Maintained by Open Domain Registry team.

Installation
------------

 1. Be sure that WeFact/HostFact is already installed on the server;
 2. Download the latest release and unzip it to **Pro/3rdparty/domain/opendomainregistry**. Be sure, that all files are located inside **opendomainregistry** folder;
 3. After extracting, visit your WeFact/HostFact installation and go to *Management* > *Services* > *Registrars*, find **opendomainregistry** and edit it.
 4. You need to modify the *Username* and *Password* in **Integration settings** and paste your *API Key* and *API Secret* from Open Domain Registry.
 5. In case you don't want to waste money or just want to check something, be sure to enable *Test Mode*.
 6. Start using it!

> **Note:**

> Depending on the Test mode setting, different URLs to access API will be used. Even if you have user on both staging and live, they will probably have completely different API key and API secret.

Support
-------

If you want to get help right away, please use contact data in either WeFact/HostFact module page or on main [Open Domain Registry](https://www.opendomainregistry.net/) page.
You can also [create issue on Github](https://github.com/opendomainregistry/whmcs_module/issues/new), we check those too!

Important
---------

Please, be sure to add "State" input field. [How to do that?](https://github.com/opendomainregistry/wefact_module/wiki/How-to-add-"State"%3F)
