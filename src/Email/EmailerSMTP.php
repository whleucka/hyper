<?php

namespace Nebula\Email;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class EmailerSMTP
{
    private bool $enabled = true;
    private bool $debug = true;
    private PHPMailer $mail;
    private array $to_addresses = [];
    private array $cc_addresses = [];
    private array $bcc_addresses = [];
    private array $attachments = [];
    private string $subject = "";
    private string $body = "";
    private string $plain = "";

    public function __construct(array $config)
    {
        extract($config);
        $this->enabled = $enabled;
        $this->debug = $debug;
        $this->mail = new PHPMailer($debug);

        //Server settings
        $this->mail->isSMTP();
        $this->mail->Host = $host;
        $this->mail->Username = $username;
        $this->mail->Password = $password;
        $this->mail->Port = $port;
        $this->mail->SMTPAuth = true;
        if ($debug) {
            $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
        }
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

        // Mail settings
        $this->mail->setFrom($from_address);
        $this->mail->addReplyTo($reply_to_address);
        $this->mail->isHTML(true);
        $this->mail->CharSet = "UTF-8";
    }

    public function send(): bool
    {
        if (
            empty($this->to_addresses) ||
            !$this->enabled ||
            trim($this->body) . trim($this->plain) === ""
        ) {
            return false;
        }

        // Add the addresses
        foreach ($this->to_addresses as $address) {
            $this->mail->addAddress($address);
        }
        foreach ($this->cc_addresses as $address) {
            $this->mail->addCC($address);
        }
        foreach ($this->bcc_addresses as $address) {
            $this->mail->addBCC($address);
        }

        // Attachments
        foreach ($this->attachments as $attachment) {
            $this->mail->addAttachment($attachment);
        }

        // Build message
        $this->mail->Subject = $this->subject;
        $this->mail->Body = $this->body;
        $this->mail->AltBody = $this->plain;

        return $this->mail->send();
    }

    public function setTo(...$addresses): EmailerSMTP
    {
        $this->to_addresses = [];
        foreach ($addresses as $address) {
            if (filter_var($address, FILTER_VALIDATE_EMAIL)) {
                $this->to_addresses[] = $address;
            }
        }
        return $this;
    }

    public function setCC(...$addresses): EmailerSMTP
    {
        $this->cc_addresses = [];
        foreach ($addresses as $address) {
            if (filter_var($address, FILTER_VALIDATE_EMAIL)) {
                $this->cc_addresses[] = $address;
            }
        }
        return $this;
    }

    public function setBCC(...$addresses): EmailerSMTP
    {
        $this->bcc_addresses = [];
        foreach ($addresses as $address) {
            if (filter_var($address, FILTER_VALIDATE_EMAIL)) {
                $this->bcc_addresses[] = $address;
            }
        }
        return $this;
    }

    public function setAttachments(...$attachments): EmailerSMTP
    {
        $this->attachments = [];
        foreach ($attachments as $attachment) {
            $this->attachments[] = trim($attachment);
        }
        return $this;
    }

    public function setSubject(string $subject): EmailerSMTP
    {
        $this->subject = trim($subject);
        return $this;
    }

    public function setBody(string $body): EmailerSMTP
    {
        $this->body = trim($body);
        return $this;
    }

    public function setTemplate(string $path, array $data = []): EmailerSMTP
    {
        $this->body = twig($path, $data);
        return $this;
    }

    public function setPlain(string $text): EmailerSMTP
    {
        $this->plain = trim($text);
        return $this;
    }
}
