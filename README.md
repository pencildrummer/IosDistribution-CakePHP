IosDistribution-CakePHP
=======================

iOS Distribution plugin for CakePHP

Features
===
### v.0.1

- **Dropbox support**: IosDistribution supports Dropbox "Drop-ins" to enable you to provide the plist manifest through an https connection even if your domain doesn't support that
- **Provisioning profile check**: IosDistribution check if the porofile provided for distribution match the correct bundle
- **Automatic metadata**: IosDistribution fill in the build information for you extracting them from the .ipa file uploaded.


Roadmap
===

##### v. 0.2
- Async IPA upload

##### v. 0.3
- CakePHP ACL integration
- User's device connection
- Check provisioned UDID and users' devices UDID

##### v. 0.4
- Download stats

Installation
==========

Install schema:
```
$ Console/cake schema create
```
If schema is correctly installed you will see a new table named *'ios_builds'*.

Load plugin in **app/Config/bootstrap.php**
```
CakePlugin::load(array(
  'IosDistribution' => array(
    'bootstrap' => true,
    'routes' => true
  )
));
```

You can start uploading builds at **yourdomain.com/ios_distribution/ios_builds**

Dropbox integration
===

To use Dropbox integration you need to register a new app from Dropbox developer website.

https://www.dropbox.com/developers/apps

- Click on the upper right button "Create new app"
- Select "Drop-ins app"
- Choose a valid name, then click "Create app"
- Copy your new **app key** and add to your Cake app configuration file

in **app/Config/bootstrap.php**
```
Configure::write('IosDistribution.Dropbox.AppKey', 'your_app_key');
```

### DropboxHelper

IosDistribution provide Dropbox integration through the DropboxHelper. Here are the main methods:

#### scripts( $appKey = null, $options = array() )

This method let you initialize all the script needed for Dropbox app to work without manually adding it.

If *$appKey* is left null, DropboxHelper will retrieve your app key in configuration **IosDistribution.Dropbox.AppKey**.

```
$this->Dropbox->scripts();
```

#### chooser( $options =  array() )

This method return the code to initialize the **Dropbox Chooser**.
You need to provide a target where to set the returned file link.

**TODO:**
- Fully configurable options, now is forced to work only with IosDistribution
