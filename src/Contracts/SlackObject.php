<?php
namespace DealerInspire\SlackNotifications\Contracts;

interface SlackObject
{
    public function getPayload() : array;
}
