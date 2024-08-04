## Enable ACL

1. **Enable ACL in the configuration file:**

   Open `config/laravel-file-explorer.php` and set `acl_enabled` to `true`.

    ```php
    return [
        "acl_enabled" => true,

        // ... other configurations
    ];
    ```

2. **Publish the migration file:**

    ```bash
    php artisan vendor:publish --tag=lfx.migrations
    ```

3. **Run the migration:**

    ```bash
    php artisan migrate
    ```

4. **Add the `HasLaravelFileExplorerPermission` trait to your User model:**

    ```php
    namespace App\Models;
    
    use AlirezaMoh\LaravelFileExplorer\Models\Concerns\HasLaravelFileExplorerPermission;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    
    class User extends Authenticatable
    {
        use HasLaravelFileExplorerPermission;
    
        // ... other model properties and methods
    }
    ```

5. **You can assign permissions to users by using the `addPermissions` method.**

    ```php
        use App\Models\User;
        use AlirezaMoh\LaravelFileExplorer\Models\LaravelFileExplorerPermission;
        
        $user = User::find(1);
        $user->addPermissions();
    ```
