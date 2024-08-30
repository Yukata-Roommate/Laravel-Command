<?php

return [
    /*----------------------------------------*
     * Logging
     *----------------------------------------*/

    "logging" => [
        "enable"         => env("YR_COMMAND_LOGGING_ENABLE", false),
        "base_directory" => env("YR_COMMAND_LOGGING_BASE_DIRECTORY", storage_path("logs")),
        "directory"      => env("YR_COMMAND_LOGGING_DIRECTORY", "command"),
        "file"           => [
            "name_format" => env("YR_COMMAND_LOGGING_FILE_NAME_FORMAT", "Y-m-d"),
            "extension"   => env("YR_COMMAND_LOGGING_FILE_EXTENSION", "log"),
            "mode"        => env("YR_COMMAND_LOGGING_FILE_MODE", 0666),
            "owner"       => env("YR_COMMAND_LOGGING_FILE_OWNER", null),
            "group"       => env("YR_COMMAND_LOGGING_FILE_GROUP", null),
        ],
    ],
];
