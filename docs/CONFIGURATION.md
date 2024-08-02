# Laravel File Explorer Package Configuration

## Configuration Options

Below are the configuration options available in the package.

### Disks

**Key:** `disks`  
**Type:** `array`  
**Description:** Specifies the list of available disks by providing their names from `config/filesystems`.  
**Default:** `["public"]`

### Default Disk

**Key:** `default_disk_on_loading`  
**Type:** `string`  
**Description:** Sets the default disk that the file explorer will load on initialization.  
**Default:** `"public"`

### Default Directory

**Key:** `default_directory_on_loading`  
**Type:** `string | null`  
**Description:** Sets the default directory from the default disk to load on initialization. If set to `null`, it will auto-select the first directory from the list.  
**Default:** `null`

### Allowed File Extensions

**Key:** `allowed_file_extensions`  
**Type:** `array`  
**Description:** Specifies the allowed file extensions for uploads. Set to `null` for no restrictions.  
**Default:** `['json', 'txt']`

### Maximum Allowed File Size

**Key:** `max_allowed_file_size`  
**Type:** `int | null`  
**Description:** Sets the maximum allowed file size for uploads. Set to `null` for no restrictions.  
**Default:** `null`

### Middlewares

**Key:** `middlewares`  
**Type:** `array`  
**Description:** Specifies middlewares applied to the file explorer, for example, `['web', 'auth']`.  
**Default:** `["web"]`

### Route Prefix

**Key:** `route_prefix`  
**Type:** `string`  
**Description:** Sets the route prefix for the file explorer.  
**Default:** `"api/laravel-file-explorer"`

### Filename Hashing on Upload

**Key:** `hash_file_name_when_uploading`  
**Type:** `boolean`  
**Description:** Indicates whether to hash filenames when uploading new files.  
**Default:** `true`

### Modified File Time Format

**Key:** `modified_file_time_format`  
**Type:** `string`  
**Description:** Specifies the time format for showing the `last_modified` time of files.  
**Default:** `'Y-m-d H:i:s'`
