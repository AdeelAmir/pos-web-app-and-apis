# POS Web App and APIs (Laravel Project)

## Setup Instructions

Follow these steps to set up and run the project:

### 1. Create and Configure `.env` File

- Copy `.env.example` to `.env`:
  ```sh
  cp .env.example .env
  ```
- Update the following values in your `.env` file:
  ```
  DB_DATABASE=pos_app
  DB_USERNAME=root
  DB_PASSWORD=
  ```

### 2. Import Database

- Import the `pos_app.sql` file located in the `db` folder into your MySQL server.

### 3. Import Postman Collection and Environment

- Import the following files from the `db` folder into Postman:
  - `Pos App.postman_collection.json`
  - `Pos App.postman_environment.json`

### 4. Extract Public Assets

- Extract `public.zip` from the `db` folder to the root of your project directory. This will overwrite or add files to the `public/` directory.

### 5. Install Composer Dependencies

- Run the following command to install PHP dependencies:
  ```sh
  composer install
  ```

### 6. Run the Project

- Start your local development server:
  ```sh
  php artisan serve
  ```
- Open your browser and navigate to [http://localhost:8000](http://localhost:8000) (or your configured URL).

---

## Additional Notes

- Make sure your MySQL server is running and accessible.
- For any issues, check your `.env` configuration and ensure all required PHP extensions are installed.
- Refer to the [Laravel Documentation](https://laravel.com/docs) for further guidance.