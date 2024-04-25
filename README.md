# KanPay API

## About

This project aims to import a .csv spreadsheet with 2 million rows. After processing, billing emails will be generated for the clients.

Doc in Apiary: https://kanpayapi.docs.apiary.io/#

Doc in Postman: https://www.postman.com/jeanmarinho529/workspace/kanpay-api

Example File: https://drive.google.com/file/d/1x4ryhPdXEnz7BhDj6h5Z6FWbxI81m3r6/

### Tecnologies
- PHP 8.1;
- Laravel 11;
- Docker;
- MYSQL;
- Nginx;
- Redis.

### Installation

Clone project:

```sh
$ git clone git@github.com:jeanmarinho529/kanpay-api.git
$ cd kanpay-api
```

Create your .env file:

```sh
$ cp .env.example .env
```

Start the server:

```sh
$ docker-compose up -d
```

Generate key:
```sh
$ docker exec api php artisan key:generate
```

Run test:
```sh
$ docker exec api php artisan test
```

Run migrate:
```sh
$ docker exec api php artisan migrate:refresh --seed
```

Start horizon:
```sh
$ docker exec api php artisan horizon
```

### Further details about the solution

Our application has the capability to receive files up to 150MB in size. Upon receiving a file, it is stored, and a corresponding record is created. Subsequently, the file's content is divided to enable parallel processing and optimize speed.

During processing, each line is individually validated. If any line contains invalid data, only the error pertaining to that line is saved, while the rest are persisted normally.

It's worth noting that we utilize Laravel's Horizon and Jobs to execute the process asynchronously, ensuring efficiency and scalability.

File Processing:

- Save the File;
- Split the File Content;
- Validate the Content;
- Persist Errors in the Content;
- Persist Valid Content.


In this application, it's already possible to import billing files. However, it allows us to process files with different contents. To achieve this, we need to create a new `BatchFileType`, listen to the `BatchFileUploaded` event, create the corresponding model, and create the `FormRequest` for its spreadsheet (although the `FormRequest` is optional, it's recommended).

Once the data has been persisted, we can use it in any way we want.


### Useful environments

```dotenv
# Determine the port on which the application will run
APP_PORT=85

# Disable or enable the telescope in the development environment
TELESCOPE_ENABLED=false

# Define the chunk size of the file content
BATCH_FILE_CHUNK_SIZE=1000
```

If you change any environment variable, I recommend restarting the application.

Restart:
```sh
$ docker-compose restart
```
