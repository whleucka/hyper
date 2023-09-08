# EmailSMTP Class

The `EmailSMTP` class is part of the Nebula framework and provides an implementation of the `Email` interface for sending SMTP-based emails using the PHPMailer library.

## Table of Contents
- [Usage](#usage)
- [Methods](#methods)
- [Helper Function](#helper-function)

### Usage

To use the `EmailSMTP` class, you should first create an instance of it. You can then initialize it and send emails using the provided methods.

```php
use Nebula\Mail\EmailSMTP;

// Create an EmailSMTP instance
$emailer = new EmailSMTP();

// Initialize the emailer (optional, if not already initialized)
$emailer->init();

// You can send template mail by passing the 
view string as the body parameter.
$template = latte('/my/email/template', ['var' => "Hello, world!"]);

// or Twig template
// $template = twig('/my/email/template', ['var' => "Hello, world!"]);

// Send an email
$emailer->send(
    'Email Subject',
    $template,
    'This is the plain text version of the email.',
    ['recipient@example.com'],
    ['cc@example.com'],
    ['bcc@example.com'],
    ['/path/to/attachment.pdf']
);
```

### Methods

#### `__construct()`

- Description: Initializes an instance of the `EmailSMTP` class.
- Usage: `$emailer = new EmailSMTP();`

#### `init()`

- Description: Initializes the emailer with SMTP configuration settings. It retrieves SMTP configuration from the application's configuration.
- Usage: `$emailer->init();`

#### `send(string $subject, string $body, ?string $plain_text = null, array $to_addresses = [], array $cc_addresses = [], array $bcc_addresses = [], array $attachments = [])`

- Description: Sends an email.
- Parameters:
  - `$subject` (string): The subject of the email.
  - `$body` (string): The HTML content of the email.
  - `$plain_text` (string|null): The plain text version of the email (optional).
  - `$to_addresses` (array): An array of recipient email addresses.
  - `$cc_addresses` (array): An array of CC (Carbon Copy) email addresses.
  - `$bcc_addresses` (array): An array of BCC (Blind Carbon Copy) email addresses.
  - `$attachments` (array): An array of file paths for email attachments.
- Returns: `true` if the email was sent successfully, `false` otherwise.

### Helper Function

A helper function `smtp()` is provided to simplify the process of initializing and using the `EmailSMTP` class. It returns an initialized instance of `EmailSMTP`.

#### `smtp(): EmailSMTP`

- Description: Returns an initialized `EmailSMTP` instance.
- Usage:
  ```php
  use Nebula\Mail\EmailSMTP;
  
  $emailer = smtp();
  
  // You can now use $emailer to send emails
  $emailer->send('Email Subject', '<p>Email Content</p>', null, ['recipient@example.com']);
  ```
