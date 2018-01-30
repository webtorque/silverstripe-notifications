<?php

/**
 * Sends an email notificaiton
 */
class NotificationService implements NotificationServiceInterface, NotificationAjaxServiceInterface
{
    use NotificationAjaxServiceTrait;

    /**
     * @var NotificationProviderInterface[]
     */
    private $providers;

    /**
     * @var NotificationParserInterface
     */
    private $parser;

    /**
     * NotificationSenderService constructor.
     * @param NotificationParserInterface     $parser    Will parse notification before they are sent.
     * @param NotificationProviderInterface[] $providers Array of providers to handle different methods of sending
     *                                                   the notification, set in yml.
     */
    public function __construct(NotificationParserInterface $parser, array $providers)
    {
        $this->parser = $parser;
        $this->providers = $providers;
    }

    /**
     * {@inheritDoc}
     * @param string            $type               Type of the notification.
     * @param array             $data               The provider used to parse the content.
     * @param Member|Member[]   $members            The person or persons to sent to.
     * @param mixed             $callToActionURL    Relative or absolute URL to an action specific to the notice.
     * @return NotificationResponseInterface        Information about the delivery of a notifcation.
     * @throws NotificationFailureException         Information about why a notification request has failed.
     */
    public function send($type, array $data, $members, $callToActionURL = false)
    {
        // If we receive a single Member, wrap it in an array.
        if ($members instanceof Member) {
            $members = [$members];
        }

        // Initialise the Notification.
        $response = new NotificationResponse();

        // Loop over all the members
        foreach ($members as $member) {
            $parsedNotification = $this->parser->parse($type, $data, $member, $callToActionURL);

            // Loop over all our providers and store the delviery status
            foreach ($this->providers as $provider) {
                try {
                    $delivery = $provider->send($parsedNotification, $member, $callToActionURL);
                    $response->addDelivery($delivery);
                } catch (NotificationFailureException $ex) {
                    $response->addFailure($ex);
                }
            }
        }

        return $response;
    }
}
