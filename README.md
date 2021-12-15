# iTrack

A project developed for the fulfillment of the requirements of a final year project in University developed using Laravel, PostgreSQL and GitHub's REST API.

## About iTrack?

iTrack is a productivity assistance tool for developers that aims to enable them to manage their online repositories. Users can also track multiple repositories by grouping them together under projects. iTrack aims to enhance developer's productivity by allowing them to have all their ideas, task lists and resources pertaining to their online repositories in a single iTrack project. This is achieved through use of notes, reminders and task lists.

## Project Requirements

1. PHP ^7.4
2. Composer
3. NPM
4. Postgres

## Project Setup

1. Clone the repository
2. Create a db with your name of choice in postgres
3. Change the .env.example to .env
4. Setup DB credentials in .env
5. Email Server is set to SMTP, You can use the service of your choice.
6. For SMTP Users, setup your credentials
7. For SMTP Users, Once you have setup your credentials, proceed on to your google account and enable less secure apps
8. Setup the pusher environment variables using random variables. Learn more [here](https://christoph-rumpel.com/2020/11/laravel-real-time-notifications)
9. Setting it up will require you to rebundle the echo code with the main JS File or you can set it to default which is 12345 For all three parameters(PUSHER_APP_ID, PUSHER_APP_KEY, PUSHER_APP_SECRET)
10. Find the app_echo.js file and search for "authEndpoint: '/itrack/public/broadcasting/auth'" and change it depending on your server configuration. If you are running a single app, i.e if your app is running on the root of localhost or an online server, change to /public/broadcasting/auth.
11. Setup a new OAuth App On GitHub and set the scope to [notifications, repo, user]. Setup the Client ID and The Secret Token in the .env file. Learn more about OAuth Apps [here](https://docs.github.com/en/developers/apps/building-oauth-apps/creating-an-oauth-app)
12. Run composer install

## Run The Project

With the project setup, there are a few things that do need to be running. The following steps, if in a production environment would need supervisor for items 1 and 2 and a cron job for item 3. The instructions below are how to accomplish them in a development environment with the processes running in the background.

1. Start Queue Worker: **php artisan queue:work**
2. Start Websocket Server **php artisan websockets:serve --port=6002**
3. Start Schedule Worker **php artisan schedule:work**

### Happy Coding
