<?php
namespace Skybluesofa\SlackNotifications\Contracts;

interface SlackObject
{
    public function getPayload() : array;
}
