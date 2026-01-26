## アプリケーション名

フリマアプリ

## 環境構築

Docker ビルド  
・git clone git@github.com:yuukuroda/kuroda-kadai2.git  
・docker-compose up -d --build

Laravel 環境構築  
・docker-compose exec php bash  
・composer install  
・cp .env.example .env、環境変数を以下に変更

```
　　DB_CONNECTION=mysql
　　DB_HOST=mysql
　　DB_PORT=3306
　　DB_DATABASE=laravel_db
　　DB_USERNAME=laravel_user
　　DB_PASSWORD=laravel_pass
```

・php artisan migrate  
・php artisan key:generate  
・php artisan db:seed
・php artisan storage:link

"The stream or file could not be opened"エラーが発生した場合  
src ディレクトリにある storage ディレクトリに権限を設定  
chmod -R 777 storage

## URL

・商品一覧：http://localhost

## 使用技術（実行環境）

・php:8.1.33  
・laravel:8.83.8  
・mysql:8.0.26  
・nginx:1.21.1

## ER 図

![ER図](ER.drawio.png)

