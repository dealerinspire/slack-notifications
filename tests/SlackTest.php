<?php
namespace Skybluesofa\SlackNotifications\Tests;

use Skybluesofa\SlackNotifications\SlackAttachment;
use Skybluesofa\SlackNotifications\SlackAttachment\SlackAttachmentAction;
use Skybluesofa\SlackNotifications\SlackAttachment\SlackAttachmentField;
use Skybluesofa\SlackNotifications\SlackNotification;
use PHPUnit\Framework\TestCase;

class SlackTest extends TestCase
{
    private $baseSlackNotification;
    private $baseAttachment;
    private $unicornAttachmentField;
    private $dragonAttachmentField;
    private $unicornAttachmentAction;
    private $dragonAttachmentAction;

    protected function setUp() : void
    {
        $this->baseSlackNotification = (new SlackNotification)->withUsername('PHPUnit')
            ->withIcon('unicorn_face')
            ->toChannel('#mythical_beasts')
            ->withTitle('Testing Slack Notification')
            ->withText('Just checking to make sure the notification goes through')
            ->unfurlLinks(false)
            ->unfurlMedia(false);

        $this->baseAttachment = (new SlackAttachment)->createFromArray([
            'fallback' => 'The Mythical Animal is a Unicorn',
            'title' => 'What are Mythical Animals?',
            'text' => 'Mythical animals are creatures that dont exist',
        ]);
        $this->unicornAttachmentField = (new SlackAttachmentField)->createFromArray([
            'title' => 'Mythical Animal',
            'value' => 'Unicorn',
            'short' => true,
        ]);
        $this->dragonAttachmentField = (new SlackAttachmentField)->withTitle('Mythical Animal')
            ->withValue('Dragon')
            ->isShort(true);
        $this->unicornAttachmentAction = (new SlackAttachmentAction)->createFromArray([
            'text' => 'Find more about Unicorns',
            'url' => 'https://en.wikipedia.org/wiki/Unicorn',
        ]);
        $this->dragonAttachmentAction = (new SlackAttachmentAction)->withText('Get the scoop on Dragons')
            ->withUrl('https://en.wikipedia.org/wiki/Dragon')
            ->withStyle('danger');
    }

    protected function tearDown(): void
    {
        $this->baseSlackNotification = null;
        $this->baseAttachment = null;
        $this->unicornAttachmentField = null;
        $this->dragonAttachmentField = null;
        $this->unicornAttachmentAction = null;
        $this->dragonAttachmentAction = null;
    }

    public function test_slack_notification()
    {
        $notificationPayload = $this->baseSlackNotification->getPayload();
        $validPayload = json_decode('{"username":"PHPUnit","channel":"#mythical_beasts","text":"*Testing Slack Notification* Just checking to make sure the notification goes through","icon_emoji":":unicorn_face:","unfurl_links":false,"unfurl_media":false,"attachments":[]}', true);
        $this->assertEquals($validPayload, $notificationPayload);
    }

    public function test_slack_attachment()
    {
        $attachmentPayload = $this->baseAttachment->getPayload();
        $validPayload = [
            'fallback' => 'The Mythical Animal is a Unicorn',
            'color' => '#666666',
            'title' => 'What are Mythical Animals?',
            'text' => 'Mythical animals are creatures that dont exist',
        ];
        $this->assertEquals($validPayload, $attachmentPayload);
    }

    public function test_slack_notification_with_attachment()
    {
        $notification = $this->baseSlackNotification;
        $notification->addAttachment([
            'fallback' => 'The Mythical Animal is a Unicorn',
            'title' => 'What are Mythical Animals?',
            'text' => 'Mythical animals are creatures that dont exist',
        ]);
        $notificationPayload = $notification->getPayload();
        $validPayload = json_decode('{"username":"PHPUnit","channel":"#mythical_beasts","text":"*Testing Slack Notification* Just checking to make sure the notification goes through","icon_emoji":":unicorn_face:","unfurl_links":false,"unfurl_media":false,"attachments":[{"fallback":"The Mythical Animal is a Unicorn","color":"#666666","text":"Mythical animals are creatures that dont exist","title":"What are Mythical Animals?"}]}', true);
        $this->assertEquals($validPayload, $notificationPayload);
    }

    public function test_slack_notification_attachment_with_field()
    {
        $attachment = $this->baseAttachment;
        $attachment->addField($this->unicornAttachmentField);
        $attachment->addField($this->dragonAttachmentField);

        $notification = $this->baseSlackNotification;
        $notification->withAttachments([$attachment]);

        $notificationPayload = $notification->getPayload();
        $validPayload = json_decode('{"username":"PHPUnit","channel":"#mythical_beasts","text":"*Testing Slack Notification* Just checking to make sure the notification goes through","icon_emoji":":unicorn_face:","unfurl_links":false,"unfurl_media":false,"attachments":[{"fallback":"The Mythical Animal is a Unicorn","color":"#666666","text":"Mythical animals are creatures that dont exist","title":"What are Mythical Animals?","fields":[{"title":"Mythical Animal","value":"Unicorn","short":true},{"title":"Mythical Animal","value":"Dragon","short":true}]}]}', true);
        $this->assertEquals($validPayload, $notificationPayload);
    }

    public function test_slack_notification_attachment_with_action()
    {
        $attachment = $this->baseAttachment;
        $attachment->addAction($this->unicornAttachmentAction);
        $attachment->addAction($this->dragonAttachmentAction);

        $notification = $this->baseSlackNotification;
        $notification->addAttachment($attachment);

        $notificationPayload = $notification->getPayload();
        $validPayload = json_decode('{"username":"PHPUnit","channel":"#mythical_beasts","text":"*Testing Slack Notification* Just checking to make sure the notification goes through","icon_emoji":":unicorn_face:","unfurl_links":false,"unfurl_media":false,"attachments":[{"fallback":"The Mythical Animal is a Unicorn","color":"#666666","text":"Mythical animals are creatures that dont exist","title":"What are Mythical Animals?","actions":[{"type":"button","text":"Find more about Unicorns","url":"https:\/\/en.wikipedia.org\/wiki\/Unicorn"},{"type":"button","text":"Get the scoop on Dragons","url":"https:\/\/en.wikipedia.org\/wiki\/Dragon","style":"danger"}]}]}', true);
        $this->assertEquals($validPayload, $notificationPayload);
    }

    public function test_slack_notification_attachment_with_fields_and_actions()
    {
        $attachment = $this->baseAttachment;
        $attachment->withActions([
            [
                'text' => 'Find more about Unicorns',
                'url' => 'https://en.wikipedia.org/wiki/Unicorn',
            ],
            $this->dragonAttachmentAction,
        ]);
        $attachment->withFields([
            [
                'title' => 'Mythical Animal',
                'value' => 'Unicorn',
                'short' => true,
            ],
            $this->dragonAttachmentField,
        ]);

        $notification = $this->baseSlackNotification;
        $notification->addAttachment($attachment);

        $notificationPayload = $notification->getPayload();
        $validPayload = json_decode('{"username":"PHPUnit","channel":"#mythical_beasts","text":"*Testing Slack Notification* Just checking to make sure the notification goes through","icon_emoji":":unicorn_face:","unfurl_links":false,"unfurl_media":false,"attachments":[{"fallback":"The Mythical Animal is a Unicorn","color":"#666666","text":"Mythical animals are creatures that dont exist","title":"What are Mythical Animals?","fields":[{"title":"Mythical Animal","value":"Unicorn","short":true},{"title":"Mythical Animal","value":"Dragon","short":true}],"actions":[{"type":"button","text":"Find more about Unicorns","url":"https:\/\/en.wikipedia.org\/wiki\/Unicorn"},{"type":"button","text":"Get the scoop on Dragons","url":"https:\/\/en.wikipedia.org\/wiki\/Dragon","style":"danger"}]}]}', true);
        $this->assertEquals($validPayload, $notificationPayload);
    }

    public function test_slack_notification_attachment_with_footer()
    {
        $attachment = $this->baseAttachment;
        $attachment->withFooter('Footer Text');

        $notification = $this->baseSlackNotification;
        $notification->addAttachment($attachment);

        $notificationPayload = $notification->getPayload();
        $validPayload = json_decode('{"username":"PHPUnit","channel":"#mythical_beasts","text":"*Testing Slack Notification* Just checking to make sure the notification goes through","icon_emoji":":unicorn_face:","unfurl_links":false,"unfurl_media":false,"attachments":[{"fallback":"The Mythical Animal is a Unicorn","color":"#666666","text":"Mythical animals are creatures that dont exist","title":"What are Mythical Animals?","footer":"Footer Text"}]}', true);
        $this->assertEquals($validPayload, $notificationPayload);
    }
}
