<?php

declare(strict_types=1);

namespace Diviky\Bright\Listeners;

use Diviky\Bright\Models\Models;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Str;

class EmailLogger
{
    /**
     * Handle the event.
     */
    public function handle(MessageSending $event): void
    {
        $message = $event->message;
        Models::emailLogs()::create([
            'id' => Str::uuid(),
            'from' => $this->formatAddressField($message, 'From'),
            'to' => $this->formatAddressField($message, 'To'),
            'cc' => $this->formatAddressField($message, 'Cc'),
            'bcc' => $this->formatAddressField($message, 'Bcc'),
            'subject' => $message->getSubject(),
            'body' => $message->getBody(),
            'headers' => (string) $message->getHeaders(),
            'attachments' => $message->getChildren() ? \implode("\n\n", $message->getChildren()) : null,
            'created_at' => carbon(),
            'updated_at' => carbon(),
        ]);
    }

    /**
     * Format address strings for sender, to, cc, bcc.
     *
     * @param $message
     * @param $field
     *
     * @return null|string
     */
    public function formatAddressField(\Swift_Message $message, string $field)
    {
        $headers = $message->getHeaders();
        if (!$headers->has($field)) {
            return;
        }

        $header = $headers->get($field);
        if (!isset($header)) {
            return null;
        }

        $mailboxes = $header->getFieldBodyModel();
        $strings = [];
        foreach ($mailboxes as $email => $name) {
            $mailboxStr = $email;
            if (null !== $name) {
                $mailboxStr = $name . ' <' . $mailboxStr . '>';
            }
            $strings[] = $mailboxStr;
        }

        return \implode(', ', $strings);
    }
}
