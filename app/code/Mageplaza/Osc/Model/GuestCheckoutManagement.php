<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) 2017 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Osc\Model;

use Magento\Quote\Api\CartRepositoryInterface;
use Mageplaza\Osc\Api\GuestCheckoutManagementInterface;
use Magento\Customer\Api\AccountManagementInterface;

/**
 * Class GuestCheckoutManagement
 * @package Mageplaza\Osc\Model
 */
class GuestCheckoutManagement implements GuestCheckoutManagementInterface
{
    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @type \Mageplaza\Osc\Api\CheckoutManagementInterface
     */
    protected $checkoutManagement;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * GuestCheckoutManagement constructor.
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Mageplaza\Osc\Api\CheckoutManagementInterface $checkoutManagement
     * @param CartRepositoryInterface $cartRepository
     * @param AccountManagementInterface $accountManagement
     */
    public function __construct(
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Mageplaza\Osc\Api\CheckoutManagementInterface $checkoutManagement,
        CartRepositoryInterface $cartRepository,
        AccountManagementInterface $accountManagement
    ) {

        $this->quoteIdMaskFactory   = $quoteIdMaskFactory;
        $this->checkoutManagement = $checkoutManagement;
        $this->cartRepository = $cartRepository;
        $this->accountManagement = $accountManagement;
    }

    /**
     * {@inheritDoc}
     */
    public function updateItemQty($cartId, $itemId, $itemQty)
    {
        /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->checkoutManagement->updateItemQty($quoteIdMask->getQuoteId(), $itemId, $itemQty);
    }

    /**
     * {@inheritDoc}
     */
    public function removeItemById($cartId, $itemId)
    {
        /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->checkoutManagement->removeItemById($quoteIdMask->getQuoteId(), $itemId);
    }

    /**
     * {@inheritDoc}
     */
    public function getPaymentTotalInformation($cartId)
    {
        /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->checkoutManagement->getPaymentTotalInformation($quoteIdMask->getQuoteId());
    }

    /**
     * {@inheritDoc}
     */
    public function updateGiftWrap($cartId, $isUseGiftWrap)
    {
        /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->checkoutManagement->updateGiftWrap($quoteIdMask->getQuoteId(), $isUseGiftWrap);
    }

    /**
     * {@inheritDoc}
     */
    public function saveCheckoutInformation(
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation,
        $customerAttributes = [],
        $additionInformation = []
    )
    {
        /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->checkoutManagement->saveCheckoutInformation(
            $quoteIdMask->getQuoteId(),
            $addressInformation,
            $customerAttributes,
            $additionInformation
        );
    }

    /**
     * {@inheritDoc}
     */
    public function saveEmailToQuote($cartId, $email)
    {
        /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->cartRepository->getActive($quoteIdMask->getQuoteId());
        $quote->setCustomerEmail($email);

        try {
            $this->cartRepository->save($quote);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isEmailAvailable($cartId, $customerEmail, $websiteId = null)
    {
        $this->saveEmailToQuote($cartId, $customerEmail);

        return $this->accountManagement->isEmailAvailable($customerEmail, $websiteId = null);
    }
}
