# Laravelのベース機能
## インストール方法
```
composer config repositories.seiyu-nico/laravel-core vcs https://github.com/seiyu-nico/laravel-core
composer require seiyu-nico/laravel-core
```
[リリースノート](https://github.com/seiyu-nico/laravel-core/releases)


## 各種クラス作成
### Serviceクラス作成
```
Description:
  Create a new service class

Usage:
  make:service [options] [--] <name>

Arguments:
  name                  

Options:
  -m, --model           Generate a service for the given model
  -r, --repository      Generate a service for the given repository
  -h, --help            Display help for the given command. When no command is given display help for the list command
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi|--no-ansi  Force (or disable --no-ansi) ANSI output
  -n, --no-interaction  Do not ask any interactive question
      --env[=ENV]       The environment the command should run under
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```
#### example
```
php artisan make:service UserService
```
- 同時に対応するモデル/リポジトリを作成する場合は`-mr`オプションを使用
  - コンソール確認後作成されます。
```
php artisan make:service -mr UserService
```


### Repositoryクラス作成
```
Description:
  Create a new repository class

Usage:
  make:repository [options] [--] <name>

Arguments:
  name                  

Options:
  -m, --model           Generate a repository for the given model
  -s, --service         Generate a repository for the given repository
  -h, --help            Display help for the given command. When no command is given display help for the list command
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi|--no-ansi  Force (or disable --no-ansi) ANSI output
  -n, --no-interaction  Do not ask any interactive question
      --env[=ENV]       The environment the command should run under
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```
#### example
```
php artisan make:repository UserRepository
```
- 同時に対応するモデル/サービスを作成する場合は`-ms`オプションを使用
  - コンソール確認後作成されます。
```
php artisan make:repository -ms UserRepository
```

## 開発
### cs-fixer
- laravelありきなのでlaravel/pintを使用
```
composer pint
```
### phpstan
- laravelありきなのでnunomaduro/larastanを使用
```
composer phpstan
```
