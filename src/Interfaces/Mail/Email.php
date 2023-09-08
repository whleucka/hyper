<?php

namespace Nebula\Interfaces\Mail;

interface Email
{
    public function init(): void;
    public function send(
        string $subject,
        string $body,
        ?string $plain_text = null,
        array $to_addresses = [],
        array $cc_addresses,
        array $bcc_addresses,
        array $attachments
    ): bool;
}
