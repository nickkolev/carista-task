# Carista News Search

A Laravel application for searching news articles using the NewsAPI service.

## Features

- Search news articles by keyword
- Pagination with 20 articles per page
- 7-day trend chart
- Mobile-responsive design
- Caching to reduce API calls
- Database storage for search history

## Setup

1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env`
4. Run `php artisan key:generate`
5. Add your NewsAPI key to `.env`:
   ```
   NEWSAPI_KEY=your_api_key_here
   ```
6. Run `php artisan migrate`
7. Start the server with `php artisan serve`

## Getting a NewsAPI Key

1. Visit [NewsAPI.org](https://newsapi.org/)
2. Sign up for a free account
3. Get your API key from the dashboard
4. Add it to your `.env` file

## Usage

- Enter a keyword on the home page
- Click "Search News" to see results
- Use pagination to browse through articles
- View the 7-day trend chart for historical data

## Testing

Run tests with:
```bash
php artisan test
```

## Configuration

The app uses SQLite by default. For MySQL/PostgreSQL, update your `.env` file with database credentials.

Cache is stored in the database by default. You can change this in `.env`:
```
CACHE_STORE=file
```

## Troubleshooting

- **Invalid API key**: Check your NewsAPI key in `.env`
- **Rate limit exceeded**: Free accounts have daily limits
- **Database issues**: Run `php artisan migrate` to create tables
- **Cache issues**: Run `php artisan cache:clear`