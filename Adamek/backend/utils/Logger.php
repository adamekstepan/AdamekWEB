<?php

class Logger {
    private static string $logFile = 'C:/xampp/htdocs/Adamek/backend/logs/app.log';

    public static function info(string $message): void {
        self::writeLog("INFO", $message);
    }

    public static function warning(string $message): void {
        self::writeLog("WARNING", $message);
    }

    public static function error(string $message): void {
        self::writeLog("ERROR", $message);
    }

    private static function writeLog(string $level, string $message): void {
        $timestamp = date("Y-m-d H:i:s");
        $line = "[$timestamp] [$level] $message" . PHP_EOL;
        file_put_contents(self::$logFile, $line, FILE_APPEND);
    }
}
