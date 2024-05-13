# Sampah Kita Jabar Backend

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Documentation](#documentation)
- [Contributing](#contributing)
- [License](#license)

## Installation

1. Clone the repository:

    ```bash
    git clone https://gitlab.com/genetik/sampahkitajabar-be.git
    ```

2. Navigate to the project directory:

    ```bash
    cd sampahkitajabar-be
    ```

3. Install the dependencies:

    ```bash
    composer install
    ```

4. Create a copy of the `.env.example` file and rename it to `.env`:

    ```bash
    cp .env.example .env
    ```

5. Generate the application key:

    ```bash
    php artisan key:generate
    ```

6. Configure the database connection in the `.env` file:

    ```dotenv
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
    ```

7. Run the database migrations:

    ```bash
    php artisan migrate:fresh --seed
    ```

## Usage

1. Start the development server:

    ```bash
    php artisan serve
    ```

2. Open your web browser and visit `http://localhost:8000` to access the application.

## Documentation
1. For API Documentation you can import this postman collection

    ```
    https://api.postman.com/collections/629881-b87e2db7-b4dd-4cab-8a5b-73c980901db0?access_key=PMAT-01HXS10MM8T0CT875ZG1G3534Z
    ```

## Contributing

If you would like to contribute to this project, please follow these steps:

1. Fork the repository.
2. Create a new branch.
3. Make your changes and commit them.
4. Push your changes to your forked repository.
5. Submit a pull request.

## License

This project is licensed under the [MIT License](LICENSE).