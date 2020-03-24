<?php
namespace DealerInspire\SlackNotifications;

use DealerInspire\SlackNotifications\SlackAttachment\SlackAttachmentAction;
use DealerInspire\SlackNotifications\SlackAttachment\SlackAttachmentField;
use DealerInspire\SlackNotifications\Contracts\SlackObject;
use DealerInspire\SlackNotifications\Contracts\SlackAttachmentType;

class SlackAttachment implements SlackObject, SlackAttachmentType
{
    private $fallback;
    public static $defaultColor = '#666666';
    private $color;
    private $text;
    private $pretext;
    private $author_name;
    private $author_link;
    private $author_icon;
    private $title;
    private $title_link;
    private $fields;
    private $actions;
    private $image_url;
    private $thumb_url;
    private $footer;
    private $footer_icon;
    private $timestamp;

    public static function getNew()
    {
        return new self();
    }

    public static function createFromArray($config = []) : SlackAttachmentType
    {
        $attachment = new SlackAttachment();
        $attachment->withFallback($config['fallback'] ?? null);
        $attachment->withColor($config['color'] ?? SlackAttachment::$defaultColor);
        $attachment->withText($config['text'] ?? null);
        $attachment->withPretext($config['pretext'] ?? null);
        $attachment->withAuthor(
            $config['author_name'] ?? null,
            $config['author_link'] ?? null,
            $config['author_icon'] ?? null
        );
        $attachment->withTitle(
            $config['title'] ?? null,
            $config['title_link'] ?? null
        );
        $attachment->withFields($config['fields'] ?? null);
        $attachment->withActions($config['actions'] ?? null);
        $attachment->withImageUrl($config['image_url'] ?? null);
        $attachment->withThumbUrl($config['thumb_url'] ?? null);
        $attachment->withFooter(
            $config['footer'] ?? null,
            $config['footer_icon'] ?? null
        );
        $attachment->withTimestamp($config['timestamp'] ?? false);
        return $attachment;
    }

    public function withFallback(string $fallback) : self
    {
        $this->fallback = $fallback;
        return $this;
    }
    
    public function withColor(?string $color = null) : self
    {
        $this->color = $color;
        return $this;
    }

    public function withText(?string $text = null) : self
    {
        $this->text = $text;
        return $this;
    }

    public function withPretext(?string $pretext = null) : self
    {
        $this->pretext = $pretext;
        return $this;
    }
    
    public function withAuthor(
        ?string $authorName = null,
        ?string $authorLink = null,
        ?string $authorIcon = null
    ) : self {
        $this->author_name = $authorName;
        $this->author_link = $authorLink;
        $this->author_icon = $authorIcon;

        return $this;
    }

    public function withTitle(?string $title = null, ?string $titleLink = null) : self
    {
        $this->title = $title;
        $this->title_link = $titleLink;

        return $this;
    }

    // Should be an array of SlackAttachmentField objects, arrays that can be converted into SlackAttachmentFields, or null
    public function withFields(?array $fields = null) : self
    {
        $this->fields = null;

        if (!is_array($fields)) {
            return $this;
        }

        foreach ($fields as $field) {
            $this->addField($field);
        }

        return $this;
    }

    // Should be a SlackAttachmentField object or an array that can be converted into a SlackAttachmentField
    public function addField($field = null) : self
    {
        if (empty($field)) {
            return $this;
        }

        if (is_array($field)) {
            $field = SlackAttachmentField::createFromArray($field);
        }
        if ($field instanceof SlackAttachmentField) {
            $this->fields[] = $field;
        }

        return $this;
    }

    // Should be an array or null
    public function withActions(?array $actions = null) : self
    {
        $this->actions = null;

        if (!is_array($actions)) {
            return $this;
        }

        foreach ($actions as $action) {
            $this->addAction($action);
        }

        return $this;
    }
    
    public function addAction($action = null) : self
    {
        if (empty($action)) {
            return $this;
        }

        if (is_array($action)) {
            $action = SlackAttachmentAction::createFromArray($action);
        }
        if ($action instanceof SlackAttachmentAction) {
            $this->actions[] = $action;
        }

        return $this;
    }

    public function withImageUrl(?string $imageUrl = null) : self
    {
        $this->image_url = $imageUrl;
        return $this;
    }

    public function withThumbUrl(?string $thumbUrl = null) : self
    {
        $this->thumb_url = $thumbUrl;
        return $this;
    }

    public function withFooter(?string $footer = null, ?string $footerIcon = null) : self
    {
        if (!is_null($footer)) {
            if (empty($footer)) {
                $footer = null;
            }
            $this->footer = $footer;
        }
        if (!is_null($footerIcon)) {
            if (empty($footerIcon)) {
                $footerIcon = null;
            }
            $this->footerIcon = $footerIcon;
        }
        return $this;
    }

    public function withTimestamp(?bool $timestamp = true) : self
    {
        $this->timestamp = null;
        if ($timestamp === true) {
            $this->timestamp = now();
        }
        return $this;
    }

    public function getPayload() : array
    {
        $payload = [];

        $payload['fallback'] = $this->fallback;
        $payload['color'] = $this->color ?: SlackAttachment::$defaultColor;
        if (isset($this->pretext)) {
            $payload['pretext'] = $this->pretext;
        }
        if (isset($this->text)) {
            $payload['text'] = $this->text;
        }
        if (isset($this->author_name)) {
            $payload['author_name'] = $this->author_name;
            if (isset($this->author_link)) {
                $payload['author_link'] = $this->author_link;
            }
            if (isset($this->author_icon)) {
                $payload['author_icon'] = $this->author_icon;
            }
        }
        if (isset($this->title)) {
            $payload['title'] = $this->title;
            if (isset($this->title_link)) {
                $payload['title_link'] = $this->title_link;
            }
        }
        if (isset($this->fields)) {
            $fields = [];
            foreach ($this->fields as $field) {
                $fields[] = $field->getPayload();
            }
            $payload['fields'] = $fields;
        }
        if (isset($this->actions)) {
            $actions = [];
            foreach ($this->actions as $action) {
                $actions[] = $action->getPayload();
            }
            $payload['actions'] = $actions;
        }
        if (isset($this->image_url)) {
            $payload['image_url'] = $this->image_url;
        }
        if (isset($this->thumb_url)) {
            $payload['thumb_url'] = $this->thumb_url;
        }
        if (isset($this->footer)) {
            $payload['footer'] = $this->footer;
            if (isset($this->footer_icon)) {
                $payload['footer_icon'] = $this->footer_icon;
            }
        }
        if (isset($this->timestamp) && $this->timestamp) {
            $payload['ts'] = now();
        }

        return $payload;
    }
}
