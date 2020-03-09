<?php
namespace Skybluesofa\SlackNotifications;

use Skybluesofa\SlackNotifications\SlackAttachment;
use Skybluesofa\SlackNotifications\Contracts\SlackObject;
use Exception;

/**
 * Send a notification to a Slack channel. Example below:
 *
 * $slackNotification = (new SlackNotification)
 *  ->toHook('abc\123\xyz')
 *  ->withUsername('Newt Scamander')
 *  ->withIcon('unicorn_face')
 *  ->toChannel('#mythical_beasts')
 *  ->withTitle('Testing Slack Notification')
 *  ->withText('Just checking to make sure the notification goes through')
 *  ->unfurlLinks(false)
 *  ->unfurlMedia(false)
 *  ->send();
 *
 * @return void
 */
class SlackNotification implements SlackObject
{
    const ENV_HOOK_VARIABLE = 'SLACK_HOOK_PATH';
    const ENV_USERNAME = 'SLACK_USERNAME';
    const ENV_ICON = 'SLACK_ICON';
    const ENV_CHANNEL = 'SLACK_CHANNEL';

    private $baseUrl = "https://hooks.slack.com/services/";
    private $hookPath;
    private $username;
    private $iconName;
    private $channel;
    private $title = "";
    private $text = "";
    private $unfurlLinks = false;
    private $unfurlMedia = false;
    private $attachments = [];

    public static function getNew()
    {
        return new self();
    }

    public function toHook($hookPath = null) : self
    {
        $this->hookPath = $hookPath;
        return $this;
    }
    
    public function withUsername($username = null) : self
    {
        $this->username = $username;
        return $this;
    }

    public function toChannel($channel = null) : self
    {
        $this->channel = $channel;
        return $this;
    }
    
    public function withIcon($iconName) : self
    {
        $this->iconName = str_replace("::", ":", ":".$iconName.":");
        return $this;
    }

    public function withTitle($title = null) : self
    {
        $this->title = $title;
        return $this;
    }

    public function withText($text = null) : self
    {
        $this->text = $text;
        return $this;
    }
    
    public function unfurlLinks($unfurl = true) : self
    {
        $this->unfurlLinks = (bool) $unfurl;
        return $this;
    }

    public function unfurlMedia($unfurl = true) : self
    {
        $this->unfurlMedia = (bool) $unfurl;
        return $this;
    }

    public function withAttachments($attachments) : self
    {
        if (is_array($attachments)) {
            foreach ($attachments as $attachment) {
                $this->addAttachment($attachment);
            }
        }
        return $this;
    }

    public function addAttachment($attachment) : self
    {
        if ($attachment) {
            if (is_array($attachment)) {
                $attachment = SlackAttachment::createFromArray($attachment);
            }
            if (is_a($attachment, SlackAttachment::class)) {
                array_push($this->attachments, $attachment);
            }
        }
        return $this;
    }

    public function send() : self
    {
        try {
            $postSlackCall = curl_init($this->getHookUrl());
            $payload = json_encode($this->getPayload());
            curl_setopt($postSlackCall, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($postSlackCall, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($postSlackCall, CURLOPT_CRLF, true);
            curl_setopt($postSlackCall, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($postSlackCall, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
                "Content-Length: " . strlen($payload)
            ]);
            $response = curl_exec($postSlackCall);
            curl_close($postSlackCall);
            if ($response!='ok') {
                throw new Exception($response);
            }
        } catch (Exception $exception) {
            $notificationTitle = $this->title ? $this->title : 'Slack Channel Notification';
            $this->log(
                vsprintf(
                    "%s: Unable to contact slack channel. %s",
                    [$notificationTitle, $exception->getMessage()]
                )
            );
        }
        return $this;
    }

    public function log() : self
    {
        $payloadText = "";
        if ($this->title) {
            $payloadText = $this->title;
        }
        if ($this->text) {
            if ($payloadText) {
                $payloadText .= ': ';
            }
            $payloadText .= $this->text;
        }
        error_log(
            vsprintf(
                "%s on %s",
                [$payloadText, $_SERVER['SERVER_NAME']]
            )
        );
        return $this;
    }


    public function getPayload() : array
    {
        $payloadText = "";
        if ($this->title) {
            $payloadText = "*".$this->title."* ";
        }
        if ($this->text) {
            $payloadText .= $this->text;
        }
        
        $attachments = [];
        foreach ($this->attachments as $att) {
            $attachments[] = $att->getPayload();
        }

        return [
            "username" => $this->getUsername(),
            "channel" => $this->getChannel(),
            "text" => $payloadText,
            'icon_emoji' => $this->getIconName(),
            'unfurl_links' => $this->unfurlLinks,
            'unfurl_media' => $this->unfurlMedia,
            'attachments' => $attachments,
        ];
    }

    private function getUsername() : ?string
    {
        if (!empty($this->username)) {
            return $this->username;
        }

        return getenv(self::ENV_USERNAME);
    }

    private function getChannel() : ?string
    {
        if (!empty($this->channel)) {
            return $this->channel;
        }

        return getenv(self::ENV_CHANNEL);
    }

    private function getIconName() : ?string
    {
        if (!empty($this->iconName)) {
            return $this->iconName;
        }

        return getenv(self::ENV_ICON);
    }

    private function getHookUrl() : string
    {
        $hookUrl = $this->baseUrl . $this->getHookPath();
        if ($hookUrl == $this->baseUrl) {
            throw new Exception('The Slack hook path is not been set. You can set it in your .env as ' . self::ENV_HOOK_VARIABLE . ' or use the "toHook" method in this class.');
        }

        return $hookUrl;
    }

    private function getHookPath() : ?string
    {
        if (!empty($this->hookPath)) {
            return $this->hookPath;
        }

        return getenv(self::ENV_HOOK_VARIABLE);
    }
}
