# IISPermissionHandler

This tool allows you to run a basic script to fix IIS permissions on any directories you specify within the ```extra``` field of your ```composer.json```

## Usage

Add the following in your root composer.json file

```json
{
    "require": {
        "boxuk/composer-iis-permissions-handler": "1.0.*@dev"
    },
    "scripts": {
        "post-install-cmd": [
            "BoxUK\\IISPermissionHandler\\ScriptHandler::fixPermissions"
        ],
        "post-update-cmd": [
            "BoxUK\\IISPermissionHandler\\ScriptHandler::fixPermissions"
        ]
    },
    "extra": {
        "iis-permission-fix-folders": ["app/cache", "app/logs", "vendor"] # Defaults to: app/cache, app/logs, vendor
    }
}
```

By default the permission handler will give minimal output. If you would like to receive more output (for debug for example), simple add the following to the ```extra``` section of your composer file.

```json
    "extra": {
        "iis-permission-fix-debug": "true"
    }
```