version: "3.8"

services:
  nginx:
    image: nginx:latest
    container_name: nginx-video-prod
    ports:
      - "80:80"
    volumes:
      - ./nginx/default.conf:/etc/nginx/conf.d/default.conf:ro
      - ./src:/var/www/html
    depends_on:
      - php

  php:
    image: php:8.0-apache
    container_name: php-video-prod
    volumes:
      - ./src:/var/www/html
    expose:
      - "80"

  db:
    image: mysql:8.0
    container_name: mysql-videofanlol
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: videoFanLOL
      MYSQL_USER: videofan
      MYSQL_PASSWORD: fan123
    ports:
      - "3307:3306"
    volumes:
      - db_data:/var/lib/mysql
      - ./database/init.sql:/docker-entrypoint-initdb.d/init.sql

volumes:
  db_data:
