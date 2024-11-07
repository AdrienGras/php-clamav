# php-clamav

This is a PHP client for the [ClamAV](https://www.clamav.net/) virus scanner.

## Features

- Uses `clamd` for scanning.
- Supports scanning of files and directories.
- Supports scanning of streams.
- Supports of TCP and Unix sockets.
- Supports nearly all the methods provided by the `clamd` daemon. (e.g. `PING`, `VERSION`, `SCAN`, `CONTSCAN`, `INSTREAM`, `RELOAD`, `SHUTDOWN`)

## Requirements

- A ClamAV `clamd` daemon running with either TCP or Unix socket enabled.
- PHP `8.0` or higher with the `sockets` extension enabled.

> You can easily run a ClamAV `clamd` daemon using the official Docker image. More information can be found [here](https://docs.clamav.net/manual/Installing/Docker.html).
 
## Installation

You can install the package via composer:

```bash
composer require adriengras/php-clamav
```

## Usage

First, you'll need an instance of the client:

```php
<?php

use AdrienGras\PhpClamAV\ClamAV;

# with a TCP socket
$client = new ClamAV::fromDSN('tcp://localhost:3310');
# or with host and port
$client = new ClamAV::fromParts('localhost', '3310');
# or with a Unix socket
$client = new ClamAV::fromDSN('unix:///var/run/clamav/clamd.sock');
```

Then, you can use nearly all the methods provided by the `clamd` daemon:

```php
<?php

# Ping the daemon, returns true if the daemon is alive, false otherwise
$isPingable = $client->ping();

# Get the ClamAV version
$version = $client->version();

# Reload the database
$client->reload();

# Scan a file, returns true if the file is clean, false otherwise
$result = $client->scan('/path/to/file.txt');

# Scan a directory, returns true if all the files are clean, false otherwise
$infectedFiles = $client->scan('/path/to/directory');

# Scan a stream, returns true if the stream is clean, false otherwise
# You can either pass a resource or a string. If you pass a string, the method will create a temporary stream.
$stream = fopen('/path/to/file.txt', 'r');
$result = $client->scanInStream($stream);
# or
$fileToStream = '/path/to/file.txt';
$result = $client->scanInStream($fileToStream);

# Scan and continue if a virus is found.
# The method will return an array of infected files, or an empty array if no virus is found.
$result = $client->continueScan('/path/to/directory');

# Shutdown the daemon
$client->shutdown();
```



