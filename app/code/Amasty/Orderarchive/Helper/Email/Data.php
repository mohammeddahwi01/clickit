<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */


namespace Amasty\Orderarchive\Helper\Email;

use Magento\Framework\App\Helper\AbstractHelper;
use Amasty\Orderarchive\Helper\Data as HelperData;

class Data extends AbstractHelper
{
    /**
     * @var \Magento\Framework\Mail\TransportInterfaceFactory
     */
    protected $mailTransportFactory;

    /**
     * @var \Magento\Framework\Mail\Template\FactoryInterface
     */
    protected $templateFactory;

    /**
     * @var \Magento\Framework\Mail\MessageFactory
     */
    protected $messageFactory;

    /**
     * @var \Amasty\Orderarchive\Helper\Data
     */
    protected $helper;

    /**
     * @var array sender name and email
     */
    protected $sender;

    /**
     * @var \Magento\Framework\Mail\Message
     */
    protected $mailer;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Mail\TransportInterfaceFactory $mailTransportFactory
     * @param \Magento\Framework\Mail\Template\FactoryInterface $templateFactory
     * @param \Magento\Framework\Mail\MessageFactory $messageFactory
     * @param \Amasty\Orderarchive\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Mail\TransportInterfaceFactory $mailTransportFactory,
        \Magento\Framework\Mail\Template\FactoryInterface $templateFactory,
        \Magento\Framework\Mail\MessageFactory $messageFactory,
        \Amasty\Orderarchive\Helper\Data $helper,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    ) {
        parent::__construct($context);
        $this->mailTransportFactory = $mailTransportFactory;
        $this->templateFactory = $templateFactory;
        $this->messageFactory = $messageFactory;
        $this->helper = $helper;
        $this->dateTime = $dateTime;
        $this->initEmailData();
    }

    /**
     * @param $params
     * @return void
     */
    public function orderArchivedAfter($params)
    {
        $executeTime = $this->dateTime->gmtDate();
        if ($this->helper->getConfigValueByPath(\Amasty\Orderarchive\Helper\Data::CONFIG_PATH_EMAIL_ENABLE)) {
            $this->sendEmailArchiving(count($params['order']), $executeTime);
        }
    }

    protected function initEmailData()
    {
        $this->sender = [
            'name' => $this->helper->getConfigValueByPath('trans_email/ident_general/name'),
            'email' => $this->helper->getConfigValueByPath('trans_email/ident_general/email')
        ];
        $this->mailer = $this->messageFactory->create();
    }

    /**
     * @param $recipientEmail
     * @param $body
     */
    public function send($recipientEmail, $body)
    {
        $this->mailer
            ->addTo($recipientEmail)
            ->setFrom($this->sender['email'], $this->sender['name'])
            ->setMessageType(\Magento\Framework\Mail\MessageInterface::TYPE_HTML)
            ->setBody($body)
            ->setSubject($this->getEmailSubject());

        $mailTransport = $this->mailTransportFactory->create(['message' => clone $this->mailer]);

        $mailTransport->sendMessage();
    }

    /**
     * @param int|string $countOrders
     * @param string $dateStart
     */
    public function sendEmailArchiving($countOrders, $dateStart)
    {
        $recipientEmail =
            $this->helper->getConfigValueByPath(HelperData::CONFIG_PATH_EMAIL_RECIPIENT);
        $templateId = $this->helper->getConfigValueByPath(HelperData::CONFIG_PATH_EMAIL_TEMPLATE);

        /** @var /DateTime $dateStart */
        $dateStart = date_create_from_format('Y-m-d H:i:s', $dateStart);
        $dateEnd = date_create();
        $duration = $dateEnd->diff($dateStart);
        if (($duration->format('%i') == 0 && $duration->format('%s') == 0)) {
            $durationText = __('took less then a second');
        } elseif (($duration->format('%i') == 0 && $duration->format('%s') != 0)) {
            $durationText = $duration->format(' %s ' . __('second(s).'));
        } else {
            $durationText = $duration->format('%i' . __('minute(s)') . ' %s ' . __('second(s).'));
        }

        $params = [
            'datetime_start' => $dateStart->format('Y-m-d H:i:s'),
            'count_orders' => $countOrders,
            'duration' => $durationText,
        ];

        return $this->send($recipientEmail, $this->getEmailBody($params, $templateId));
    }

    /**
     * @param $params
     * @param $templateId
     * @return $this
     */
    protected function getEmailBody($params, $templateId)
    {
        $template = $this->templateFactory->get($templateId)
            ->setVars($params);

        return $template->processTemplate();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getEmailSubject()
    {
        return __('Orders have been archived.');
    }
}
