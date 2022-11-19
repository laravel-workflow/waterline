# Waterline

An elegant UI for monitoring [Laravel Workflows](https://github.com/laravel-workflow/laravel-workflow).

## Installation

This UI is installable via [Composer](https://getcomposer.org).

```bash
composer require laravel-workflow/waterline

php artisan waterline:install
```

## Authorization

Waterline exposes a dashboard at the `/waterline` URL. By default, you will only be able to access this dashboard in the local environment. However, within your `app/Providers/WaterlineServiceProvider.php` file, there is an authorization gate definition. This authorization gate controls access to Waterline in non-local environments.

## Upgrading Waterline

After upgrading Waterline you must publish the latest assets.

```bash
composer require laravel-workflow/waterline

php artisan waterline:publish
```

## Dashboard View

![waterline_dashboard](https://user-images.githubusercontent.com/1130888/202864399-0bf0a3e7-4454-4a30-8fd2-e330b2460b76.png)

## Workflow View

![workflow](https://user-images.githubusercontent.com/1130888/202864523-edd88fce-0ce9-4e5a-a24c-38afeae4e057.png)
