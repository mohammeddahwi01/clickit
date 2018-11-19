<?php

/**
 * @Author: Ngo Quang Cuong <bestearnmoney87@gmail.com>
 * @Creation time: 2017-06-12 10:02:19
 * @Last modified time: 2017-06-12 10:16:56
 * @link: http://www.giaphugroup.com
 *
 */

namespace LabUrbain\GoToCheckout\Plugin\Controller\Cart;
use Magento\Framework\Event\ObserverInterface;

// class Add implements ObserverInterface
// {
//     protected $_responseFactory;
//     protected $_url;
//
//     public function __construct(\Magento\Framework\App\ResponseFactory $responseFactory,
// \Magento\Framework\UrlInterface $url)
//     {
//         $this->_responseFactory = $responseFactory;
//         $this->_url = $url;
//     }
//
//     public function execute(\Magento\Framework\Event\Observer $observer)
//     {
//         $cartUrl = $this->_url->getUrl('checkout/cart/index');
//         $this->_responseFactory->create()->setRedirect($cartUrl)->sendResponse();
//         exit;
//     }
class Add
{
    protected $_url;
		protected $_logger;
    protected $request;

    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Request\Http $request,
				\Psr\Log\LoggerInterface $logger
    )
    {
        $this->_url = $url;
				$this->_logger = $logger;
        $this->request = $request;
    }

    public function aroundExecute(\Magento\Checkout\Controller\Cart\Add $subject, \Closure $proceed)
    {
        $returnValue = $proceed();
        // We need to check, does the request send from Ajax?
        if (!$this->request->isAjax()) {
            // get the url of the checkout page
            $checkoutUrl = $this->_url->getUrl('onestepcheckout');
            // set the url for redirecting
            $returnValue->setUrl($checkoutUrl);
						$this->_logger->log(\Psr\Log\LogLevel::DEBUG, "redirect has taken place and req is ajax");

        }
        return $returnValue;
    }
}
