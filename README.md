
# Laravel File Explorer

![Laravel File Explorer image](docs/laravel-file-explorer-merged-demo.png)



Laravel File Explorer is a package for easy file management in Laravel apps, offering features like browsing, uploading, and deleting files.





## Features

- Frontend mae with Vuejs 3
- Light/dark mode toggle
- Utilizes Laravel Flysystem standards for file system operations
- Supports Local, FTP, S3, Dropbox, and other storage options
- Enables selective disk interaction for precise file management
- Supports File System Operations:
    - Create and manage files with ease
    - Organize content through directory creation
    - Rename files and directories
    - Enable multi-upload functionality
    - Download files
    - Intuitive image preview feature for quick visual assessment
    - Video player
    - Code editor for quick edits and customization
    - Backend events for monitoring




## Installation

Install Laravel File Explorer with composer

```bash
  composer require alireza-moh/laravel-file-explorer
```
Publish configuration file

```bash
  php artisan vendor:publish --tag=lfx.config
```
Download the frontend into your project

https://github.com/Alireza-Moh/laravel-file-explorer-frontend

```bash
  npm i laravel-file-explorer
```
Add the FileExplorer component to the vue app
```javascript
import LaravelFileExplorer from "laravel-file-explorer";
import "laravel-file-explorer/dist/style.css";

app.use(LaravelFileExplorer);
```
Use the component inside your vue component
```javascript
  <LaravelFileExplorer :setting="{baseUrl: 'http://laravel-wrapper.localhost:8084/api/laravel-file-explorer/'}"/>
```
