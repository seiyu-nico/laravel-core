# Laravelのベース機能
## インストール方法
```
composer config repositories.seiyu-nico/laravel-core vcs https://github.com/seiyu-nico/laravel-core
composer require seiyu-nico/laravel-core
```
## config/app.phpへProviderクラスを追加
```
SeiyuNico\LaravelCore\Providers\LaravelCoreProviders::class
```

