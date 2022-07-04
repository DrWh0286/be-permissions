# TYPO3 extension be_permissions

This extensions provides some cli commands to export and import be_groups records to/from yaml files.

## Documentation

The extension uses the config/be_groups (parallel to config/sites) folder to store the exported be_group files.
It also provides an API.

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
$ ./vendor/bin/typo3cms bepermissions:export [arguments]
```

This command exports either all as code_managed_group marked be_groups records to a yaml file or only the one with the
as argument given identifier.

#### Extend command

```shell
$ ./vendor/bin/typo3cms bepermissions:extend [arguments]
```

This command imports the yaml file with the given identifier with the deploy processing 'extend' (see above).

#### Overrule command

```shell
$ ./vendor/bin/typo3cms bepermissions:overrule [arguments]
```

This command imports the yaml file with the given identifier with the deploy processing 'overrule' (see above).

#### Synchronize command

```shell
$ ./vendor/bin/typo3cms bepermissions:syncprodbegroups [arguments]
```

This command synchronizes all as code_managed_group marked records from the given remote (prod) host to the local system.
This happens via a REST API (see configuration section). This overrules your local records!

Argument: group identifier

#### Merge and export command

```shell
$ ./vendor/bin/typo3cms bepermissions:mergeprodandexport [arguments]
```

This command merges all as code_managed_group marked records from the given remote (prod) host with the local records.
This happens via a REST API (see configuration section). Afterwards it exports the result to the yaml files.

Argument: group identifier

#### Deploy command

```shell
$ ./vendor/bin/typo3cms bepermissions:deploy
```

This command imports all yaml files based on the selected deploy_processing. Can be used for a deployment recipe.

### Configuration

To configure the extensions api there are some properties in the extension configuration:

#### apiToken

This is at the moment a simple api authentication. So the apiToken needs to be the same on local and remote system. For security
reason it is not recommended to hold this in your git repository, but set this e.g. via an environment variable.

#### basicAuthUser and basicAuthPassword

If filled this holds basic auth credentials for the remote system.

#### productionHost

This holds the host to the remote repository from where the synchronization should be used.

## Todo

* Create a backend module to provide a manual compare and overrule import after deployment to remove permissions in case of deploy_processing 'extend'.
* Respect subgroups.
* Add a tsconfig file to export to also store user TSconfig for be_groups in vcs.

## Migrations

### Update to > 0.6.0

Execute SQL script:

```sql
UPDATE table SET code_managed_group = bulk_export;
```