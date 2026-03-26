# Laravel Messenger Application

A real-time messaging application built with Laravel and Vue.js that supports private conversations, message delivery, unread tracking, file attachments, and live interaction updates.

This project demonstrates practical experience in building chat and messaging workflows using Laravel, Vue.js, and real-time broadcasting features.

---

## Project Status

Core messaging workflows, conversation handling, file attachments, unread tracking, and live updates are implemented. The project is kept as part of my portfolio to showcase real-time application development with Laravel.

---

## Overview

This project was developed as a messenger-style web application that allows users to communicate through private conversations in a dynamic and interactive environment.

It focuses on implementing real-time messaging features, user interaction workflows, and backend/frontend integration using Laravel and Vue.js.

---

## Key Features

- Private conversations between users
- Real-time message delivery
- Unread message tracking
- File attachment support
- Conversation management
- Live interaction updates
- Laravel and Vue.js integration
- Authentication-based messaging flow
- Backend logic for messaging workflows

---

## Tech Stack

- **Backend:** PHP, Laravel
- **Database:** MySQL
- **Frontend:** Vue.js, JavaScript, Blade
- **Real-Time Features:** Broadcasting, Pusher / Echo
- **Authentication:** Laravel authentication
- **Architecture:** Laravel + Vue integration

---

## Main Modules

### Messaging System
- Create and manage private conversations
- Send and receive messages
- Track unread messages
- Handle message-related workflows

### Real-Time Interaction
- Live message updates
- Dynamic messaging experience
- Real-time communication flow

### Attachments
- Upload and manage file attachments in conversations
- Associate files with messages

### Application Logic
- User authentication
- Conversation-related backend logic
- Frontend and backend integration for messaging

---

## Highlighted Technical Areas

This project demonstrates practical experience in:

- Real-time application development
- Laravel and Vue.js integration
- Chat and messaging workflows
- File upload handling
- Unread message tracking
- Backend/frontend synchronization
- Conversation-based application logic

---

## Installation

```bash
git clone https://github.com/omarmusallam/laravel-messenger.git
cd laravel-messenger
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
