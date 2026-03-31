# Laravel Messenger

A portfolio-ready real-time messaging application built with **Laravel** and **Vue.js**, designed to demonstrate private conversations, file attachments, unread tracking, live updates, and a cleaner end-to-end presentation flow.

This project focuses on building a practical messenger-style experience with a structured backend, interactive chat workflows, and a polished UI suitable for demos and portfolio presentation.

---

## Overview

Laravel Messenger is a web-based messaging application that allows authenticated users to communicate through private conversations in a responsive and interactive interface.

The project demonstrates how to build a conversation-based system using Laravel, Vue.js, broadcasting, attachments, and unread message tracking, while also providing a cleaner demo flow through a landing page, dashboard, and messenger workspace.

---

## Core Features

- Private one-to-one conversations
- Real-time message delivery
- Unread message tracking
- File attachment support
- Authentication-based messaging flow
- Conversation management
- Message deletion behavior
- Dashboard with quick messaging stats
- Portfolio-friendly landing and navigation flow
- Laravel + Vue.js integration

---

## Demo Flow

This project includes a simple presentation-ready flow for showcasing the product:

- **Home page** for project introduction
- **Dashboard** for quick stats and navigation
- **Messenger workspace** for the main chat experience
- **Authentication flow** for protected access
- **API-driven conversations and messages**

---

## Main Modules

### 1. Messenger Workspace
The main chat interface where users can browse conversations, open chats, send messages, and interact with messaging features in a structured UI.

### 2. Conversations
Users can create or continue private conversations, retrieve conversation data, and manage participants through backend endpoints.

### 3. Messages
Supports sending text messages, storing attachments, listing conversation messages, and handling message deletion logic.

### 4. Unread Tracking
Unread states are tracked through the recipients layer, allowing the application to count and mark unseen messages accurately.

### 5. Attachments
Users can upload files inside conversations, and the application stores attachment metadata and file paths as part of the message body.

### 6. Dashboard
A lightweight dashboard summarizes:
- total conversations
- available contacts
- unread items
- shared attachments

---

## Technical Highlights

This project demonstrates practical experience in:

- Laravel authentication and protected routing
- REST-style API endpoints for messaging workflows
- Eloquent relationships for conversations, messages, participants, and recipients
- File upload handling inside chat systems
- Unread message tracking logic
- Soft delete behavior for messages
- Real-time broadcasting integration
- Laravel + Vue.js frontend/backend interaction
- Building portfolio-ready product presentation flows

---

## Architecture Notes

The project uses a conversation-based structure:

- **Users** participate in conversations
- **Conversations** contain messages
- **Messages** belong to a sender and can be text or attachments
- **Recipients** track read/unread state per user
- **Broadcast events** are used for live interaction updates

This structure helps simulate practical messaging workflows in a clean Laravel application.

---

## Tech Stack

- **Backend:** PHP, Laravel
- **Database:** MySQL
- **Frontend:** Vue.js, JavaScript, Blade
- **Real-Time:** Laravel Broadcasting, Echo, Pusher-compatible flow
- **Authentication:** Laravel Auth
- **File Handling:** Laravel Storage
- **Architecture Style:** Laravel backend + Vue-powered chat interaction

---

## API Endpoints

Some of the core messaging routes include:

- `GET /api/conversations`
- `GET /api/conversations/{conversation}`
- `PUT /api/conversations/{conversation}/read`
- `POST /api/conversations/{conversation}/participants`
- `DELETE /api/conversations/{conversation}/participants`
- `GET /api/conversations/{id}/messages`
- `POST /api/messages`
- `DELETE /api/messages/{id}`

---

## Screenshots

Add project screenshots here, for example:

- Home page
- Dashboard
- Messenger workspace
- Active conversation
- Attachment message
- Conversation list with unread state

---

## Demo Account

You can seed the project with demo data and use a demo account for presentation screenshots.

Example:

- **Email:** `omar@sannad-demo.test`
- **Password:** `password`

---

## Installation

```bash
git clone https://github.com/omarmusallam/laravel-messenger.git
cd laravel-messenger
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm run build
php artisan serve
