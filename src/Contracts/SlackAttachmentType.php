<?php
namespace Skybluesofa\SlackNotifications\Contracts;

interface SlackAttachmentType
{
    public static function createFromArray(array $config = []) : self;
}
