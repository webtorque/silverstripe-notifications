<?php
use \libphonenumber\PhoneNumberUtil;
use \libphonenumber\NumberParseException;
use \libphonenumber\PhoneNumberFormat;
use \libphonenumber\PhoneNumberType;

/**
 * Notification provider used to send an SMS Message Media via an Email message.
 *
 * If `giggsey/libphonenumber-for-php` is available. The mobile phone number will be validated before trying to send
 * the SMS.
 *
 * @link http://www.messagemedia.com/sms-api/smtp
 *
 */
class NotificationSmsMessageMediaProvider implements NotificationProviderInterface
{

    /**
     * @var string
     */
    protected $destinationDomain;

    /**
     * @var string
     */
    protected $fromEmail;

    /**
     * @var string
     */
    protected $phoneNumberField;

    /**
     * @var string
     */
    protected $defaultCountry;

    /**
     * Test flag to force the use of Basic Phone Parsing even if if the PhoneNumberUtil library is installed.
     * @var boolean
     */
    private $forceBasicPhoneParsing = false;

    /**
     * Instanciate this NotificationSmsMessageMediaProvider.
     * @param string $destinationDomain Domain to use for the email message sent to Message Media. e.g.: `e2s.pcsms.us`.
     * @param string $fromEmail         The email address that will be used in the email's FROM header. If left blank,
     *                                  will default.
     * @param string $phoneNumberField  Fieldname that contains the mobile number on the Member object.
     * @param string $defaultCountry    Default country code to use when analyzing a mobile phone number.
     */
    public function __construct(
        $destinationDomain,
        $fromEmail = '',
        $phoneNumberField = 'MobileNumber',
        $defaultCountry = 'US'
    ) {
        $this->destinationDomain = $destinationDomain;

        if (!$fromEmail) {
            $fromEmail = null;
        }
        $this->fromEmail = $fromEmail;

        $this->phoneNumberField = $phoneNumberField;
        $this->defaultCountry = $defaultCountry;
    }

    /**
     * @inheritDoc
     * @param  ParsedNotificationInterface $notification    Notification to send.
     * @param  Member                      $member          User meant to receive the message.
     * @param  mixed                       $callToActionURL Relative or absolute URL to an action specific to the notice.
     * @return NotificationDelivery
     */
    public function send(ParsedNotificationInterface $notification, Member $member, $callToActionURL = false)
    {
        $phone = $this->getPhoneNumber($member);

        $subject = $this->buildMessage($notification->getShortMessage(), $callToActionURL);
        $to = "$phone@{$this->destinationDomain}";

        $email = Email::create()
            ->setTo($to)
            ->setFrom($this->fromEmail)
            ->setSubject($subject);

        try {
            $email->send();
        } catch (Exception $ex) {
            $failure = new NotificationFailureException('Notification Email-to-SMS delivery failed.');
            throw $failure->setMember($member)->setPrevious($ex);
        }

        return new NotificationDelivery('DELIVERED', 'SMS');
    }

    /**
     * Build an SMS message by appending the call to action URL if provided. The call to action URL will be made
     * absolute if it isn't already.
     * @param  string $message         Basic SMS message.
     * @param  string $callToActionURL URL to append to the message if any.
     * @return string
     */
    protected function buildMessage($message, $callToActionURL)
    {
        if ($callToActionURL) {
            // Make sure we have an absolute URL.
            if (Director::is_absolute_url($callToActionURL)) {
                $url = $callToActionURL;
            } else {
                $url = Director::absoluteURL($callToActionURL, true);
            }

            // Append the URL at the end of the message
            $message .= ' ' . $url;
        }

        return $message;
    }

    /**
     * Try to get a mobile phone number for a member.
     * @param  Member $member Member for which the phone number should be retrieved.
     * @throws NotificationFailureException If the phone number is invalid.
     * @return string
     */
    protected function getPhoneNumber(Member $member)
    {
        $fieldname = $this->phoneNumberField;
        if (empty($member->$fieldname)) {
            throw new NotificationFailureException('Invalid phone number field.');
        }

        $phone = trim($member->$fieldname);
        if (!$this->forceBasicPhoneParsing && class_exists(PhoneNumberUtil::class)) {
            return $this->advancedPhoneParsing($phone);
        } else {
            return $this->basicPhoneParsing($phone);
        }
    }

    /**
     * Basic phone validation in case PhoneNumberUtil is not available. This does not validate the phone beyond
     * removing all non-digit characters and making sure the phone string is non-empty.
     * @param string $phone Phone number string to parse.
     * @throws NotificationFailureException If the phone number is invalid.
     * @return string
     */
    protected function basicPhoneParsing($phone)
    {
        $phone = preg_replace('/\D/', "", $phone);
        if (empty($phone)) {
            throw new NotificationFailureException('Invalid phone number field.');
        } else {
            return $phone;
        }
    }

    /**
     * Advanced Phone parsing that uses `PhoneNumberUtil`. It will try to parse the phone number. It will attempt to
     * determine the right country code and to identifiy if the number is for a mobile phone or a landline.
     * @param  string $phone Phone number string to parse.
     * @throws NotificationFailureException If the phone number is invalid.
     * @return string
     */
    protected function advancedPhoneParsing($phone)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        // Parse the phone number
        try {
            $proto = $phoneUtil->parse($phone, $this->defaultCountry);
        } catch (NumberParseException $e) {
            throw new NotificationFailureException('Could not parse phone number.');
        }

        // Validity.
        if (!$phoneUtil->isValidNumber($proto)) {
            throw new NotificationFailureException('Phone number is invalid.');
        }

        if (!in_array(
            $phoneUtil->getNumberType($proto),
            [
                PhoneNumberType::MOBILE,
                PhoneNumberType::FIXED_LINE_OR_MOBILE,
                PhoneNumberType::PERSONAL_NUMBER,
                PhoneNumberType::PAGER,
                PhoneNumberType::UNKNOWN,
            ]
        )) {
            throw new NotificationFailureException('Phone number is not for a mobile.');
        }

        // convert phone number to string.
        $phone = $phoneUtil->format($proto, PhoneNumberFormat::E164);
        $phone = ltrim($phone, '+');

        return $phone;
    }

    /**
     * Set the value `ForceBasicPhoneParsing` flag. The default behavior is to use `giggsey/libphonenumber-for-php`
     * library if it's installed. Using this flag you can test the logic for when the library is missing. This is meant
     * for testing only.
     * @param boolean $value Value of the flag.
     * @return void
     */
    public function setForceBasicPhoneParsing($value)
    {
        $this->forceBasicPhoneParsing = (boolean)$value;
    }
}
