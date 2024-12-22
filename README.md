## About Filament

Filament is a collection of tools for rapidly building beautiful TALL stack (Tailwind CSS, Alpine.js, Laravel, Livewire) applications. It provides a set of components and utilities to help you build modern web applications with ease.

## Support the Project

If you find this project helpful, consider supporting me by buying me a coffee!

[![Buy Me A Coffee](https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png)](https://buymeacoffee.com/miteshviras329)

## System Requirements

Before installing Filament, ensure your server meets the following requirements:

-   PHP >= 8.2
-   Composer
-   Laravel >= 11.0
-   Node.js & NPM (for frontend assets)
-   A database (MySQL, PostgreSQL, SQLite, etc.)

## Installation

To get started with Filament, follow these steps:

1. **Install Composer Dependencies**: Install the necessary dependencies using Composer.

    ```bash
    composer install
    ```

2. **Install NPM Dependencies**: Install the necessary dependencies using NPM.

    ```bash
    npm install
    ```

3. **Run Migrations**: Run the database migrations.

    ```bash
    php artisan migrate
    ```

4. **Build Frontend Assets**: Compile the frontend assets for development.

    ```bash
    npm run dev
    ```

5. **Serve the Application**: Start the Laravel development server.

    ```bash
    php artisan serve
    ```

6. **Create Admin User**: execute below given command to create admin user.

    ```bash
    php artisan user:make-admin-user
    ```

You can now access the Filament admin panel by navigating to `/admin` in your browser.

## Building for Production

To compile the frontend assets for production, use the following command:

```bash
npm run build
```

## Learning Filament

Filament has comprehensive [documentation](https://filamentphp.com/docs) and a growing community of developers. You can also find tutorials and guides on various topics related to Filament and the TALL stack.

## Contributing

Thank you for considering contributing to the Filament framework! The contribution guide can be found in the [Filament documentation](https://filamentphp.com/docs/contributing).

## Code of Conduct

To ensure that the Filament community is welcoming to all, please review and abide by the [Code of Conduct](https://filamentphp.com/docs/contributing#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Filament, please send an e-mail to Dan Harrin via [dan@filamentphp.com](mailto:dan@filamentphp.com). All security vulnerabilities will be promptly addressed.

## License

The Filament framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Author

For more information about the author, visit [miteshviras.vercel.app](https://miteshviras.vercel.app).
