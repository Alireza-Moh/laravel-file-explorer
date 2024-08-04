
# Laravel File Explorer

![Laravel File Explorer image](docs/laravel-file-explorer-merged-demo.png)



Laravel File Explorer is a package for easy file management in Laravel apps, offering features like browsing, uploading, and deleting files.





## Features

- Frontend made with Vuejs 3
- Light/dark mode toggle
- Utilizes Laravel Flysystem standards for file system operations
- Supports Local, FTP, S3, Dropbox, and other storage options
- Enables selective disk interaction for precise file management
- Supports File System Operations:
    - Create and manage files and directories
    - Rename files and directories
    - Multi-upload functionality
    - Download files
    - Intuitive image preview
    - Video player
    - Code editor (Codemirror) for quick edits and customization
    - Laravel events
    - ACL

## Installation

Install Laravel File Explorer with composer

```bash
  composer require alireza-moh/laravel-file-explorer
```
Publish configuration file<br>
```bash
  php artisan vendor:publish --tag=lfx.config
```
Download the frontend into your project

https://github.com/Alireza-Moh/laravel-file-explorer-frontend

```bash
  npm i laravel-file-explorer
```
Add the FileExplorer component to the vue app
```js
import LaravelFileExplorer from "laravel-file-explorer";
import "laravel-file-explorer/dist/style.css";

app.use(LaravelFileExplorer);
```
Use the component inside your vue component
```javascript
  <LaravelFileExplorer :setting="{baseUrl: 'http://laravel-wrapper.localhost:8084/api/laravel-file-explorer/'}"/>
```

## Enable ACL
The Laravel File Explorer with ACL (Access Control List) lets you control what each user can do with files.
You can give permissions to each user like creating, reading, updating, deleting, uploading, and downloading files.

Setup: [ACL DOC](docs/CONFIGURATION.md)
