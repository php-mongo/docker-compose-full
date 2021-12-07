#!/bin/bash

function doKey {
  echo "Generating application key: php artisan key:generate --ansi"
  php artisan key:generate --ansi
}

## Handles any outstanding migrations
function doMigrate {
  echo "Running migrations: php artisan migrate"
  php artisan migrate
}

## Some of these call will repeat certain actions within Passport
function doPassport {
  echo "Setting up Passport for API tokens.."
  php artisan passport:install
  php artisan passport:keys
  php artisan passport:client --personal
}

## Runs the queue listener job - grabs the current terminal
function startQueue {
  echo "Starting laravel queue: artisan queue:work"
  php artisan queue:work
}

## Run all functions in sequence
function dosetup {
  doKey
  doMigrate
  doPassport
  startQueue
}
