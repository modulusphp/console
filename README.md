# Modulus Console

This package contains Modulus console commands.

### Available Commands

| Command              | Description                                                          |
|:---------------------|:---------------------------------------------------------------------|
| `down`               | Put the application into maintenance mode                            |
| `help`               | Displays help for a command                                          |
| `list`               | Lists commands                                                       |
| `migrate`            | Run a migration                                                      |
| `seed`               | Run a seed                                                           |
| `serve`              | Serve the application on the PHP development serve                   |
| `shell`              | An interactive shell for modern PHP                                  |
| `test`               | Run application tests                                                |
| `up`                 | Bring the application out of maintenance mode                        |
| `clear:cache`        | Clear hibernate cache                                                |
| `clear:logs`         | Clear all logs                                                       |
| `clear:sessions`     | Clear all user sessions                                              |
| `clear:views`        | Clear all compiled view files                                        |
| `craft:abstract`     | Create a new application abstract class                              |
| `craft:class`        | Create a new application class                                       |
| `craft:command`      | Create a new Craftsman command                                       |
| `craft:controller`   | Create a new controller class                                        |
| `craft:directive`    | Create a new Medusa directive                                        |
| `craft:event`        | Create a new application event                                       |
| `craft:exception`    | Create a new custom exception class                                  |
| `craft:interface`    | Create a new application interface                                   |
| `craft:middleware`   | Create a new Middleware class                                        |
| `craft:migration`    | Create a new migration class                                         |
| `craft:model`        | Create a new Eloquent model class                                    |
| `craft:notification` | Create a new notification                                            |
| `craft:querymap`     | Create a new query map                                               |
| `craft:request`      | Create a new http request                                            |
| `craft:rule`         | Create a new validation rule                                         |
| `craft:seeder`       | Create a new seeder class                                            |
| `craft:test`         | Create a new test class                                              |
| `craft:trait`        | Create a new application trait                                       |
| `docsify:link`       | Link Docsify's resources documentations to a public resource         |
| `frontend:backup`    | Create a backup of your current Frontend                             |
| `frontend:current`   | Get the name of the current Frontend framework                       |
| `frontend:restore`   | Restore a backup                                                     |
| `frontend:switch`    | Change Frontend framework                                            |
| `key:generate`       | Set the application key                                              |
| `plugin:install`     | Verify and install a new plugin                                      |
| `queue:dispatch`     | Process a single job                                                 |
| `queue:listen`       | Listen to a given queue                                              |
| `queue:table`        | Create a migration for the queue jobs database table                 |
| `queue:work`         | Start processing jobs on the queue                                   |
| `route:list`         | List all registered routes                                           |
| `schedule:run`       | Run the scheduled commands                                           |
| `storage:link`       | Create a symbolic link from "public/storage" to "storage/app/public" |

Install
-------

This package is automatically installed with the Modulus Framework.

```
composer require modulusphp/console
```

Usage:

```
php craftsman <Command>
```

Example:

```
php craftsman list
```

Security
-------

If you discover any security related issues, please email donaldpakkies@gmail.com instead of using the issue tracker.

License
-------

The MIT License (MIT). Please see [License File](LICENSE) for more information.
