<?php

namespace BoxUK\IISPermissionHandler;

class ScriptHandler
{

    /**
     * @var string Path to perm fixing script
     */
    private static $permsScript = 'FolderPerms.ps1';

    /**
     * @var string Path to powershell executable
     */
    private static $powerShell = '%SystemRoot%\System32\WindowsPowerShell\v1.0\powershell.exe';

    /**
     * @param $event
     * @throws \RuntimeException
     */
    public static function fixPermissions($event)
    {

        // Only applicable to Windows
        if (!self::isWindows()) {
            return;
        }

        $options = self::getOptions($event);
        $directories = $options['iis-permission-fix-folders'];

        $command = self::getCommand($directories);

        echo 'Stopping IIS and setting file permissions on folders: ' . implode(", ", $directories) . "\n";

        if (null == $output = shell_exec($command)) {
            throw new \RuntimeException(sprintf(
                'An error occurred when executing the "%s" command.',
                escapeshellarg($command)
            ));
        }

        if (isset($options['iis-permission-fix-debug'])) {
            echo $output . "\n";
        }

        echo 'Sucessfully restarted IIS and set permissions on folders: ' . implode(", ", $directories) . "\n";
    }

    /**
     * @return bool True if windows, false otherwise
     */
    protected static function isWindows()
    {
        return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
    }

    /**
     * Get command to run our permissions fixer
     *
     * @param $directories
     * @return string
     * @throws \RuntimeException
     */
    protected static function getCommand($directories)
    {
        $permsScript = __DIR__ . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . self::$permsScript;

        foreach ($directories as $name => $dir) {
            if (!is_dir($dir)) {
                throw new \RuntimeException(sprintf('"%s" is not a valid directory.', escapeshellarg($dir)));
            }
        }

        return self::$powerShell . ' -ExecutionPolicy Bypass ' . $permsScript . ' ' . escapeshellarg(implode(" ", $directories));
    }

    /**
     * @param $event
     * @return array
     */
    protected static function getOptions($event)
    {
        $options = array_merge(
            array(
                'iis-permission-fix-folders' => array(
                    'app' . DIRECTORY_SEPARATOR . 'cache',
                    'app' . DIRECTORY_SEPARATOR . 'logs',
                    'vendor'
                )
            ),
            $event->getComposer()->getPackage()->getExtra()
        );

        $options['process-timeout'] = $event->getComposer()->getConfig()->get('process-timeout');

        return $options;
    }
}
