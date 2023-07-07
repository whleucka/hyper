<?php

namespace Nebula\Email;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class EmailerSMTP
{
    private bool $enabled = true;
    private bool $debug = true;
    private array $to_addresses = [];
    private array $cc_addresses = [];
    private array $bcc_addresses = [];
    private array $attachments = [];
    private string $subject = "";
    private string $body = "";
    private string $plain = "";
    private PHPMailer $mail;

    /**
     * @param array<int,mixed> $config
     */
    public function __construct(array $config)
    {
        extract($config);
        $this->enabled = $enabled;
        $this->debug = $debug;
        $this->mail = new PHPMailer($debug);

        // Server settings
        $this->configureServer($host, $username, $password, $port);

        // Mail settings
        $this->configureMail($from_address, $reply_to_address);
    }

    public function send(): bool
    {
        if (!$this->shouldSend()) {
            return false;
        }

        $this->addAddresses();
        $this->addAttachments();

        // Build message
        $this->mail->Subject = $this->subject;
        $this->mail->Body = $this->body;
        $this->mail->AltBody = $this->plain;

        return $this->mail->send();
    }

    /**
     * @param mixed $addresses
     */
    public function setTo(...$addresses): EmailerSMTP
    {
        $this->to_addresses = $this->filterValidEmailAddresses($addresses);
        return $this;
    }

    /**
     * @param mixed $addresses
     */
    public function setCC(...$addresses): EmailerSMTP
    {
        $this->cc_addresses = $this->filterValidEmailAddresses($addresses);
        return $this;
    }

    /**
     * @param mixed $addresses
     */
    public function setBCC(...$addresses): EmailerSMTP
    {
        $this->bcc_addresses = $this->filterValidEmailAddresses($addresses);
        return $this;
    }

    /**
     * @param mixed $attachments
     */
    public function setAttachments(...$attachments): EmailerSMTP
    {
        $this->attachments = array_map('trim', $attachments);
        return $this;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = trim($subject);
        return $this;
    }

    public function setBody(string $body): self
    {
        $this->body = trim($body);
        return $this;
    }
    /**
     * @param array<int,mixed> $data
     */
    public function setTemplate(string $path, array $data = []): EmailerSMTP
    {
        $this->body = twig($path, $data);
        return $this;
    }

    public function setPlain(string $text): self
    {
        $this->plain = trim($text);
        return $this;
    }

    private function configureServer(string $host, string $username, string $password, int $port): void
    {
        $this->mail->isSMTP();
        $this->mail->Host = $host;
        $this->mail->Username = $username;
        $this->mail->Password = $password;
        $this->mail->Port = $port;
        $this->mail->SMTPAuth = true;
        if ($this->debug) {
            $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
        }
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    }

    private function configureMail(string $from_address, string $reply_to_address): void
    {
        $this->mail->setFrom($from_address);
        $this->mail->addReplyTo($reply_to_address);
        $this->mail->isHTML(true);
        $this->mail->CharSet = "UTF-8";
    }

    private function shouldSend(): bool
    {
        return (
            !empty($this->to_addresses) &&
            $this->enabled &&
            trim($this->body) . trim($this->plain) !== ""
        );
    }

    private function addAddresses(): void
    {
        foreach ($this->to_addresses as $address) {
            $this->mail->addAddress($address);
        }
        foreach ($this->cc_addresses as $address) {
            $this->mail->addCC($address);
        }
        foreach ($this->bcc_addresses as $address) {
            $this->mail->addBCC($address);
        }
    }

    private function addAttachments(): void
    {
        foreach ($this->attachments as $attachment) {
            $this->mail->addAttachment($attachment);
        }
    }
    /**
     * @param array<int,mixed> $addresses
     */
    private function filterValidEmailAddresses(array $addresses): array
    {
        return array_filter($addresses, fn($address) => filter_var($address, FILTER_VALIDATE_EMAIL));
    }
}
