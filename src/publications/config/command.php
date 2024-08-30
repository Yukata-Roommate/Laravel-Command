<?php

return [
    /*----------------------------------------*
     * Logging
     *----------------------------------------*/

    "logging" => [
        "enable"    => env("YR_COMMAND_LOGGING_ENABLE", false),
        "directory" => env("YR_COMMAND_LOGGING_DIRECTORY", "command"),
    ],
];
