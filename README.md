Release with the flow.
======================

[![Build Status](https://secure.travis-ci.org/bonndan/release-manager.png?branch=master)](https://travis-ci.org/bonndan/release-manager)

Release Manager is a PHP command line tool to help you keeping track of release versions. 

* It uses your composer file to store and retrieve information.
* It enforces [semantic versions] (http://semver.org).
* Works closely together with [git flow](http://nvie.com/posts/a-successful-git-branching-model/).

![screenshot](https://github.com/bonndan/release-manager/raw/develop/docs/screen-flow.png "Using git flow")


This is a fork of Liip's Relase Management Tool [RMT](https://github.com/liip/RMT). Kudos to the original authors for this tool.

Use with git-flow
------------------

    ./RMT release
    ./RMT finish

or to hotfix (patch-bump version based on master branch)

    ./RMT hotfix 
    ./RMT finish


Installation
------------

In order to use RMT your project should use [Composer](http://getcomposer.org/) as RMT will be installed as a dev-dependency. Just go on your project root directory and execute:

    php composer.phar require --dev bonndan/release-manager 0.5.*         # lastest stable
    # or
    php composer.phar require --dev bonndan/release-manager dev-develop    # lastest unstable

Then you must initialize RMT by running the following command:

    php vendor/bonndan/release-manager/command.php init

This command will create for you a `extra/rmt` section in your composer.json. You
should adapt the configuration to your needs. A good example is the [composer file
of this project] (https://github.com/bonndan/release-manager/blob/master/composer.json).

From that point on you can start using it, just execute it:

    ./RMT


Usage with manual workflow
--------------------------

Using RMT is very straightforward, you just have to run the command:

    ./RMT release

RMT will then do the following tasks:

* Execute the prerequisites checks
* Ask the user to answers potentials questions
* Generate a new version number
* Execute the pre-release actions
* Persist the new version number
* Execute the post-release actions

![screenshot](https://github.com/bonndan/release-manager/raw/master/docs/screen.png "In-Dev Screenshot")

### Additional commands

The `release` command is the main behavior of the tool, but some extra commands are available:

* `current` will show your project current version number
* `changes` will show the last which would be part of the next release
* `init` create a config section in your composer.json file

Configuration
-------------

All RMT configuration have to be done in the `composer.json`. You can optionally define a list of actions that will be executed and before or after the release of a new version
 and where you want to store the version (in a changelog file, as a VCS tag, etc…). The file is divided in 5 root elements:

* `vcs`: The type of VCS you are using, can be `git`, `svn` or `none`
* `prerequisites`: A list `[]` of prerequisites that must be matched before starting the release process
* `preReleaseActions`: A list `[]` of actions that will be executed before the release process
* `versionPersister`: The persister to use to store the versions
* `postReleaseActions`: A list `[]` of actions that will be executed after the release

All the entries of this config are working the same way: You have to specify the class you want
 to handle the action or provide an abbrevation for classes provided by Release Manager.:


### Semantic Version Generator

Release Manager only allows semantic versions without prefixes. See (Semantic versioning)[http://semver.org].
The release version can be increased by:

* major
* minor
* patch
* build number

### Version persister

Class is charged of saving/retrieving the version number

* composer: uses the version from the composer file (default and recommended)
* vcs-tag: Save the version as a VCS tag
* changelog: Save the version in the changelog file

### Prerequisite actions

Prerequisite actions are executed before the interactive part.

* working-copy-check: Check that you don't have any VCS local changes before release
* display-last-changes: display your last changes before release

### Actions

Actions can be used for pre or post release parts.

* execute: Execute any script via system() call. Return values greather than zero cause exceptions to be thrown.
* changelog-update: Update a changelog file
* vcs-commit: Process a VCS commit
* vcs-tag: Tag the last commit
* vcs-publish: Publish the changes (commit and tags)
* composer-update: Update the version number in a composer file




Configuration examples
----------------------
Most of the time, it will be easier for you to pick up and example bellow and to adapt it to your needs.

### The configuration for Release Manager explained
```
{
    "rmt": {
         "vcs": "git",
         "prerequisites": [
            "working-copy-check",
            "display-last-changes"
         ],
         "preReleaseActions": [
            {
               "name": "execute",
               "script": "phpunit test"
            },
            {
               "name": "version-stamp",
               "const": "RMT_VERSION"
            },
            {
               "name": "changelog-update"
            },
            {
               "name": "changelog-render"
            }
         ],
         "postReleaseActions": [
            "vcs-commit"
         ]
      }
}
```

* Git is the version control system
* Before releasing it is checked that the working copy is clean and the last changes are displayed.
* The version stamp (autogenerated php file) and the changelog are updated with the new version.
* PHPUnit (generic call) is executed and stops releasing if it fails.
* Not configured here: The composer version persister updates the composer.json file.
* After the release all changes are committed.


### Pushing automatically
```
{
    /* ... */
    "postReleaseActions": [
       "vcs-commit",
       "vcs-publish"
    ],
}
```

### No VCS, changelog updater only

```
{
    "versionPersister": "changelog"
}
```


Contributing
------------
If you would like to help, to submit one of your action script or just to report a bug:
 just go on the project page: https://github.com/bonndan/release-manager

Requirements
------------

PHP 5.3
Composer

Authors
-------

* Laurent Prodon Liip AG
* David Jeanmonod Liip AG
* Daniel Pozzi

License
-------

RMT is licensed under the MIT License - see the LICENSE file for details
