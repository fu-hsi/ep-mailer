<?php

/**
 * epMailer
 *
 * @author      Fu-Hsi
 * @copyright   2014 Fu-Hsi
 * @package     epMailer
 * @version     1.0.0
 * @link        https://github.com/fu-hsi/ep-mailer
 * @license     http://opensource.org/licenses/MIT MIT License
 * 
 */

/**
 *
 * @package epMailer
 * @author Fu-Hsi
 * @since 1.0.0
 */
class epMailer
{

    const ENC_7BIT = '7bit';

    const ENC_8BIT = '8bit';

    const ENC_BASE64 = 'base64';

    const ENC_QUOTED_PRINTABLE = 'quoted-printable';

    private $headers = array(
        'MIME-Version' => '1.0',
        'X-Mailer' => 'epMailer'
    );

    /**
     *
     * @var array
     */
    private $recipients = array(
        'To' => array(),
        'Cc' => array(),
        'Bcc' => array()
    );

    /**
     *
     * @var string
     */
    private $subject = '';

    /**
     *
     * @var string
     */
    private $body = '';

    /**
     *
     * @var string
     */
    private $encoding;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->subject = '(no subject)';
        $this->encoding(self::ENC_QUOTED_PRINTABLE);
    }

    /**
     *
     * @param string $header            
     * @param string $value            
     */
    private function setHeader($header, $value)
    {
        $this->headers[$header] = $value;
    }

    /**
     *
     * @param string $string            
     * @return string
     */
    private function encodeString($string)
    {
        return '=?UTF-8?B?' . base64_encode($string) . '?=';
    }

    /**
     *
     * @param string $address            
     * @param string $name            
     * @return string
     */
    private function encodeAddress($address, $name = null)
    {
        if ($name) {
            return $this->encodeString($name) . ' <' . $address . '>';
        } else {
            return $address;
        }
    }

    /**
     *
     * @return string
     */
    private function encodeHeaders()
    {
        $headers = '';
        foreach ($this->headers as $header => $value) {
            $headers .= $header . ': ' . $value . "\r\n";
        }
        return rtrim($headers);
    }

    /**
     *
     * @return string
     */
    private function encodeBody()
    {
        switch ($this->encoding) {
            case self::ENC_BASE64:
                return chunk_split(base64_encode($this->body));
                break;
            
            case self::ENC_QUOTED_PRINTABLE:
                return quoted_printable_encode($this->body);
                break;
            
            case self::ENC_7BIT:
            case self::ENC_8BIT:
            default:
                return $this->body;
                break;
        }
    }

    /**
     * Set Content-Transfer-Encoding
     *
     * @param string $encoding            
     * @return string
     */
    public function encoding($encoding = null)
    {
        if ($encoding) {
            return $this->headers['Content-Transfer-Encoding'] = $this->encoding = $encoding;
        } else {
            return $this->encoding;
        }
    }

    /**
     * Add address
     *
     * @param string $address            
     * @param string $name            
     */
    public function addRecipient($address, $name = null)
    {
        $this->recipients['To'][] = $this->encodeAddress($address, $name);
    }

    /**
     * Add Carbon Copy address
     *
     * @param string $address            
     * @param string $name            
     */
    public function addCC($address, $name = null)
    {
        $this->recipients['Cc'][] = $this->encodeAddress($address, $name);
    }

    /**
     * Add Blind Carbon Copy address
     *
     * @param string $address            
     * @param string $name            
     */
    public function addBCC($address, $name = null)
    {
        $this->recipients['Bcc'][] = $this->encodeAddress($address, $name);
    }

    /**
     * Get or set the subject
     *
     * @param string $subject            
     * @return string
     */
    public function subject($subject = null)
    {
        if (is_null($subject))
            return $this->subject;
        else
            return $this->subject = $subject;
    }

    /**
     * Set From address
     *
     * @param string $address            
     * @param string $name            
     */
    public function from($address, $name = null)
    {
        if ($name) {
            $this->headers['From'] = $this->encodeAddress($address, $name);
        } else {
            $this->headers['From'] = $address;
        }
    }

    /**
     * Set Reply-To address
     *
     * @param string $address            
     * @param string $name            
     */
    public function replyTo($address, $name = null)
    {
        if ($name) {
            $this->headers['Reply-To'] = $this->encodeAddress($address, $name);
        } else {
            $this->headers['Reply-To'] = $address;
        }
    }

    /**
     * Get or set mail body
     *
     * @param string $body            
     * @return string
     */
    private function body($body = null)
    {
        if (is_null($body)) {
            return $this->body;
        } else {
            return $this->body = $body;
        }
    }

    /**
     * Get or set mail body and set Content-Type to text/plain
     *
     * @param string $body            
     * @return string
     */
    public function textBody($body = null)
    {
        $this->headers['Content-Type'] = 'text/plain; charset=utf-8; format=flowed';
        return $this->body($body ? wordwrap($body, 75, "\r\n") : $body);
    }

    /**
     * Get or set mail body and set Content-Type to text/html
     *
     * @param string $body            
     * @return string
     */
    public function htmlBody($body = null)
    {
        $this->headers['Content-Type'] = 'text/html; charset=utf-8';
        return $this->body($body);
    }

    /**
     * Send mail
     *
     * @return boolean
     */
    public function send()
    {
        if ($this->recipients['Cc']) {
            $this->setHeader('Cc', implode(', ', $this->recipients['Cc']));
        }
        
        if ($this->recipients['Bcc']) {
            $this->setHeader('Bcc', implode(', ', $this->recipients['Bcc']));
        }
        
        $to = implode(', ', $this->recipients['To']);
        $subject = $this->encodeString($this->subject);
        $body = $this->encodeBody();
        $headers = $this->encodeHeaders();
        
        return mail($to, $subject, $body, $headers);
    }
}

?>