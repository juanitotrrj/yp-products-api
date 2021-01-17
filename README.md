# Products API

## Requirements
- `docker`

## Installation
1. Clone the repository
   ```bash
   git clone https://github.com/juanitotrrj/yp-products-api.git && cd yp-products-api
   ```
1. Initialize the local server
   ```bash
   vendor/bin/sail up
   ```
1. Run the migrations and API Explorer
   ```bash
   vendor/bin/sail artisan migrate && \
   vendor/bin/sail artisan l5-swagger:generate
   ```
1. Visit the API Explorer @ http://localhost/api/documentation
