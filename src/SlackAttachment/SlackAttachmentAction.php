<?php
namespace DealerInspire\SlackNotifications\SlackAttachment;

use DealerInspire\SlackNotifications\Contracts\SlackObject;
use DealerInspire\SlackNotifications\Contracts\SlackAttachmentType;

class SlackAttachmentAction implements SlackObject, SlackAttachmentType
{
    const DEFAULT_TYPE = 'button';

    private $type;
    private $text;
    private $url;
    private $value;
    private $style;
    private $styles = ['primary', 'danger'];

    public static function getNew()
    {
        return new self();
    }

    public static function createFromArray(array $config = []) : SlackAttachmentType
    {
        $action = new self();

        $action->isType($config['type'] ?? null);
        $action->withText($config['text'] ?? null);
        $action->withUrl($config['url'] ?? null);
        $action->withValue($config['value'] ?? null);
        $action->withStyle($config['style'] ?? null);

        return $action;
    }

    public function isType(?string $type = null) : self
    {
        $this->type = $type ?: self::DEFAULT_TYPE;
        return $this;
    }
    
    public function withText(?string $text = null) : self
    {
        $this->text = $text;
        return $this;
    }

    public function withUrl(?string $url = null) : self
    {
        $this->url = $url;
        return $this;
    }

    public function withValue(?string $value = null) : self
    {
        $this->value = $value;
        return $this;
    }

    public function withStyle(?string $style = null) : self
    {
        if (!in_array($style, $this->styles)) {
            $style = null;
        }
        $this->style = $style;
        return $this;
    }

    public function getPayload() : array
    {
        $payload = [];

        $payload['type'] = $this->type ?: self::DEFAULT_TYPE;
        if (isset($this->text)) {
            $payload['text'] = $this->text;
        }
        if (isset($this->url)) {
            $payload['url'] = $this->url;
        }
        if (isset($this->value)) {
            $payload['value'] = $this->value;
        }
        if (isset($this->style)) {
            $payload['style'] = $this->style;
        }

        return $payload;
    }
}
