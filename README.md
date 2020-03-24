# Slack Notifications

An easy, fluent implementation of a Slack notification system.

## Adding the Package to Your Project

### Requiring in Composer

```
composer require dealerinspire/slack-notifications
```

### Adding to Your Code

At the top of the class file where you wish to use:

```
use DealerInspire\SlackNotifications\SlackNotification;
```

## Basic Implementation

```
$slackNotification = (new SlackNotification)
    ->toHook('abc\123\xyz')
    ->withUsername('Newt Scamander')
    ->withIcon('unicorn_face')
    ->toChannel('#mythical_beasts')
    ->withTitle('Testing Slack Notification')
    ->withText('Just checking to make sure the notification goes through')
    ->unfurlLinks(false)
    ->unfurlMedia(false)
    ->send();
```

## With Simple Attachment

At the top of the class file where you wish to use:

```
use DealerInspire\SlackNotifications\SlackNotification;
use DealerInspire\SlackNotifications\SlackAttachment;
```

In your class code:

```
$slackAttachment = (new SlackAttachment)
    ->createFromArray([
        'fallback' => 'The Mythical Animal is a Unicorn',
        'title' => 'What are Mythical Animals?',
        'text' => 'Mythical animals are creatures that dont exist',
    ]);   

$slackNotification = (new SlackNotification)
    ->toHook('abc\123\xyz')
    ->withUsername('Newt Scamander')
    ->withIcon('unicorn_face')
    ->toChannel('#mythical_beasts')
    ->withTitle('Testing Slack Notification')
    ->withText('Just checking to make sure the notification goes through')
    ->unfurlLinks(false)
    ->unfurlMedia(false);
    ->withAttachments([$slackAttachment])
    ->send();
```

## With More Complex Attachment

At the top of the class file where you wish to use:

```
use DealerInspire\SlackNotifications\SlackNotification;
use DealerInspire\SlackNotifications\SlackAttachment;
use DealerInspire\SlackNotifications\SlackAttachment\SlackAttachmentAction;
use DealerInspire\SlackNotifications\SlackAttachment\SlackAttachmentField;
```

In your class code:

```
$unicornAttachmentField = (new SlackAttachmentField)
    ->createFromArray([
        'title' => 'Mythical Animal',
        'value' => 'Unicorn',
        'short' => true,
    ]);

$unicornAttachmentAction = (new SlackAttachmentAction)
    ->createFromArray([
        'text' => 'Find more about Unicorns',
        'url' => 'https://en.wikipedia.org/wiki/Unicorn',
    ]);

$slackAttachment = (new SlackAttachment)
    ->createFromArray([
        'fallback' => 'The Mythical Animal is a Unicorn',
        'title' => 'What are Mythical Animals?',
        'text' => 'Mythical animals are creatures that dont exist',
    ]);   
$slackAttachment->addField($unicornAttachmentField);
$slackAttachment->addAction($unicornAttachmentAction);

$slackNotification = (new SlackNotification)
    ->toHook('abc\123\xyz')
    ->withUsername('Newt Scamander')
    ->withIcon('unicorn_face')
    ->toChannel('#mythical_beasts')
    ->withTitle('Testing Slack Notification')
    ->withText('Just checking to make sure the notification goes through')
    ->unfurlLinks(false)
    ->unfurlMedia(false);
    ->withAttachments([$slackAttachment])
    ->send();
```

## Notification configured with an array

At the top of the class file where you wish to use:

```
use DealerInspire\SlackNotifications\SlackNotification;
```

In your class code:

```
$slackAttachmentConfiguration = [
    [
        'fallback' => 'The Mythical Animal is a Unicorn',
        'title' => 'What are Mythical Animals?',
        'text' => 'Mythical animals are creatures that dont exist',
        'actions' => [
            [
                'text' => 'Find more about Unicorns',
                'url' => 'https://en.wikipedia.org/wiki/Unicorn',
            ],
        ],
        'fields' => [
            [
                'title' => 'Mythical Animal',
                'value' => 'Unicorn',
                'short' => true,
            ],
        ],
    ],
];

$slackNotification = (new SlackNotification)
    ->toHook('abc\123\xyz')
    ->withUsername('Newt Scamander')
    ->withIcon('unicorn_face')
    ->toChannel('#mythical_beasts')
    ->withTitle('Testing Slack Notification')
    ->withText('Just checking to make sure the notification goes through')
    ->unfurlLinks(false)
    ->unfurlMedia(false);
    ->withAttachments($slackAttachmentConfiguration)
    ->send();
```

## Using Environmental Variables

You may also use environmental variables to default some settings. The environmental variables are contained within your project's .env file.

```
SLACK_HOOK_PATH=abc\123\xyz
SLACK_USERNAME=Newt Scamander
SLACK_ICON=unicorn_face
SLACK_CHANNEL=#mythical_beasts
```

When you use environmental variables this, a basic use case can become even simpler. Of course, you can always overwrite these settings with the methods shown in the examples above.

At the top of the class file where you wish to use:

```
use DealerInspire\SlackNotifications\SlackNotification;
```

In your class code:

```
(new SlackNotification)
    ->withTitle('Testing Slack Notification')
    ->withText('Just checking to make sure the notification goes through')
    ->send();
```

## Contributing
See the [CONTRIBUTING](CONTRIBUTING.md) guide.
