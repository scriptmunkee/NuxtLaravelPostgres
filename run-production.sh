#!/bin/bash

cd backend && php artisan serve --host=0.0.0.0 --port=8000 &
LARAVEL_PID=$!

cd frontend && node .output/server/index.mjs &
NUXT_PID=$!

wait $LARAVEL_PID $NUXT_PID
