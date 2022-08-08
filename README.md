# TYPO3 extension be_permissions

This extensions provides some cli commands to export and import be_groups records to/from yaml files.

## Documentation

The extension uses the config/be_groups (parallel to config/sites) folder to store the exported be_group files.
It also provides an API.

### Features

* Commands for
  * import/export be_groups from/to yaml
  * separate deploy command for your ci pipeline
  * synchronizing local be_groups from a remote system
  * initialize all your existing groups as code managed
  * merging your local groups with remote groups and export them
* Automatic export with saving a be_groups record (needs to be enabled with feature toggle)
* Deploy command respects the two options for code managed groups:
  * __extend:__ Permissions are only added, but no permission will be removed.
  * __overrule:__ Whole group will be overruled. Changes are gone after deployment.
* TYPO3 Backend module
  * to see the status/diff of your files and be_groups
  * to import/export your be_groups manually
  * to overrule as 'extend' marked groups after deployment (to remove permissions from those groups)
* Editing be_groups marked as code managed
  * __overrule:__ is not possible with Production context
  * __extend:__ will cause a warining message with Production context

### Installation

```shell
$ composer req sebastianhofer/be-permissions
```

### Quick start

1. Install the extension
2. Go to a be_groups record in TYPO3 backend
3. Enable code_manages_group, select a deploy_processing method and save (identifier should be created automatically)
4. Export with the export command (see below)
5. Add the resulting yaml file to git
6. Deploy your code and execute the deploy command (see below)

### be_groups changes

The extension adds three new fields to the be_groups.

#### code_managed_group

Activating this checkbox makes the group available for all the commands for import and export.

#### identifier

This field connects the yaml file to the br_groups record. This was introduced, because the uid is an autoincrement and
therefore in case of newly created be_groups on a remote system using it as identifier would fail. The identifier
is used to create a folder within all files for a be_group are placed.

#### deploy_processing

There are two ways to import a be_group.yaml:

##### overrule

In this case simply the record fields in database are overruled by the values in the be_group.yaml.

##### extend

In this case the permissions are merged. Means, that permissions only added in case they are not set, but nothing is
removed.

### Commands

#### Export command

```shell
$ ./vendor/bin/typo3cms bepermission:export [arguments]
```

This command exports either all as code_managed_group marked be_groups records to a yaml file or only the one with the
as argument given identifier.

#### Extend command

```shell
$ ./vendor/bin/typo3cms bepermission:extend [arguments]
```

This command imports the yaml file with the given identifier with the deploy processing 'extend' (see above).

#### Overrule command

```shell
$ ./vendor/bin/typo3cms bepermission:overrule [arguments]
```

This command imports the yaml file with the given identifier with the deploy processing 'overrule' (see above).

#### Synchronize command

```shell
$ ./vendor/bin/typo3cms bepermission:syncprodbegroups [arguments]
```

This command synchronizes all as code_managed_group marked records from the given remote (prod) host to the local system.
This happens via a REST API (see configuration section). This overrules your local records!

Argument: group identifier

#### Merge and export command

```shell
$ ./vendor/bin/typo3cms bepermission:mergeprodandexport [arguments]
```

This command merges all as code_managed_group marked records from the given remote (prod) host with the local records.
This happens via a REST API (see configuration section). Afterwards it exports the result to the yaml files.

Argument: group identifier

#### Deploy command

```shell
$ ./vendor/bin/typo3cms bepermission:deploy
```

This command imports all yaml files based on the selected deploy_processing. Can be used for a deployment recipe.

#### Init command

```shell
$ ./vendor/bin/typo3cms bepermission:init [deploy_processing] -e
```

This command initializes all existing be_groups as code managed. Default deploy processing is 'extend', but you can
give 'overrule' as argument

Option '-e' triggers an export of all groups after initializing to yaml files.

#### Initialize Identifiers command

```shell
$ ./vendor/bin/typo3cms bepermission:initIdentifiers
```

This command initializes all existing be_groups with an identifier if not set yet.

### Configuration

To configure the extensions api there are some properties in the extension configuration:

#### apiToken

This is at the moment a simple api authentication. So the apiToken needs to be the same on local and remote system. For security
reason it is not recommended to hold this in your git repository, but set this e.g. via an environment variable.

#### basicAuthUser and basicAuthPassword

If filled this holds basic auth credentials for the remote system.

#### remoteHost

This holds the host to the remote repository from where the synchronization should be used.

### Feature Toggles

see https://docs.typo3.org/m/typo3/reference-coreapi/11.5/en-us/Configuration/FeatureToggles.html

#### be_permissions.automaticBeGroupsExportWithSave

If enabled an automatic export of a be_groups record is performed with saving the record.

## Todo

* Add a tsconfig file to export to also store user TSconfig for be_groups in vcs.
* Add backend module for remote synchronize feature.

## Migrations

### Update to > 0.6.0

Execute SQL script:

```sql
UPDATE be_groups SET code_managed_group = bulk_export;
```
