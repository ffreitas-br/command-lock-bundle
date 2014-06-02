command-lock-bundle
===================
[![Build Status](https://travis-ci.org/ffreitas-br/command-lock-bundle.png?branch=master)](https://travis-ci.org/ffreitas-br/command-lock-bundle)
##
This `command-lock-bundle` when installed prevents two or more of same command runs simultaneously.
***
## Installation
#####
To install `command-lock-bundle` you will need just a few minutes.  
#####
1) Include the `command-lock-bundle` in the `required` section of `composer.json`.
### composer.json
```json
// ...
"require": {
    "ffreitas-br/command-lock-bundle": "dev-master",
},
// ...
```
After this run `composer update` or `composer intall` to refresh your dependencies.
###
2) Register the bundle in your Kernel.
### app/AppKernel.php
```php
<?php

public function registerBundles()
{
    $bundles = array(
        // ...
        new FFreitasBr\CommandLockBundle\CommandLockBundle(),
    );
    // ...
}
```
###
3) Configure the directory where the pid lock files will be stored.
### app/config/config.yml
```yaml
...

command_lock:
    pid_directory: "%kernel.root_dir%/data/command_pid_files"

...
```
`Don't worry` if you don't have the directories created yet, the bundle will take care of this for you.
###
4) [OPTIONAL] Configure a list of exceptions.
### app/config/config.yml
```yaml
...

command_lock:
    exceptions:
        - cache:warmup
        - cache:clear

...
```
The commands listed in this configurations will be allowed to run simultaneously.

***

Now you have the `command-lock-bundle` installed and configured, it will now prevents two or more of same command runs simultaneously.
