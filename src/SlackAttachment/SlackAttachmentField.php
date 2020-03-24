<?php
namespace DealerInspire\SlackNotifications\SlackAttachment;

use DealerInspire\SlackNotifications\Contracts\SlackObject;
use DealerInspire\SlackNotifications\Contracts\SlackAttachmentType;

class SlackAttachmentField implements SlackObject, SlackAttachmentType
{
    private $title;
    private $value;
    private $short = false;

    public static function getNew()
    {
        return new self();
    }

    public static function createFromArray(array $config = []) : SlackAttachmentType
    {
        $field = new self();

        $field->withTitle($config['title'] ?? null);
        $field->withValue($config['value'] ?? null);
        $field->isShort((bool) $config['short'] ?? false);
        
        return $field;
    }

    public function withTitle(?string $title = null) : self
    {
        $this->title = $title;
        return $this;
    }
    
    public function withValue(?string $value = null) : self
    {
        $this->value = $value;
        return $this;
    }

    public function isShort(?bool $isShort = true) : self
    {
        $this->short = $isShort;
        return $this;
    }

    public function getPayload() : array
    {
        $payload = [];

        if (isset($this->title)) {
            $payload['title'] = $this->title;
        }
        if (isset($this->value)) {
            $payload['value'] = $this->value;
        }
        if (isset($this->short)) {
            $payload['short'] = $this->short;
        }

        return $payload;
    }
}
