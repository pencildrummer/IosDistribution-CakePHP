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
