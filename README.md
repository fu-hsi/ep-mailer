epMailer
========

epMailer class support:
- 7bit, 8bit, quoted-printable and base64 content transfer encoding.
- Plain text and html content type (UTF-8).

```php
<?php

    include 'epMailer.php';

    $mail = new epMailer();
    $mail->encoding(epMailer::ENC_QUOTED_PRINTABLE);  // Default encoding

    $mail->subject('Subject');
    $mail->textBody('Text body'); // or htmlBody()

    $mail->addRecipient('john.smith@example.com', 'John Smith');
    $mail->addCC('fred.bloggs@example.com');

    $mail->send();

?>
```
