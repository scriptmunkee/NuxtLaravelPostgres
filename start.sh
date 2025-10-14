#!/bin/bash

cd backend && php artisan serve --host=0.0.0.0 --port=8000 &
cd frontend && npm run dev -- --port 5000 --host 0.0.0.0

wait
