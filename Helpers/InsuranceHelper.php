<?php

namespace Alma\MonthlyPayments\Helpers;

use Alma\MonthlyPayments\Model\Data\InsuranceProduct;
use Alma\MonthlyPayments\Model\Exceptions\AlmaInsuranceProductException;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Model\Quote\Item;

class InsuranceHelper extends AbstractHelper
{
    const ALMA_INSURANCE_SKU = 'alma_insurance';

    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var Json
     */
    private $json;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Context $context
     * @param Json $json
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        ProductRepository $productRepository,
        Logger $logger,
        Json $json
    ) {
        parent::__construct($context);
        $this->json = $json;
        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
    }

    /**
     * Get alma_insurance data from model
     *
     * @param Item $quoteItem
     * @return string
     */
    public function getQuoteItemAlmaInsurance(Item $quoteItem): ?string
    {
        return $quoteItem->getAlmaInsurance();
    }

    /**
     * Set alma_insurance in DB
     *
     * @param Item $quoteItem
     * @param array $data
     * @return Item
     */
    public function setQuoteItemAlmaInsurance(Item $quoteItem, array $data): Item
    {
        return $quoteItem->setAlmaInsurance($this->json->serialize($data));
    }

    /**
     * @return InsuranceProduct|null
     */
    public function getInsuranceParamsInRequest(): ?InsuranceProduct
    {

        $insuranceId = $this->request->getParam('alma_insurance_id');
        $insuranceName = $this->request->getParam('alma_insurance_name');
        $insurancePrice = $this->request->getParam('alma_insurance_price');
        if ($insuranceId && $insuranceName && $insurancePrice) {
            return New InsuranceProduct((int)$insuranceId, $insuranceName, (int)substr($insurancePrice, 0, -1));
        }
        return null;
    }


    /**
     * @return Product|null
     */
    public function getAlmaInsuranceProduct(): ?Product
    {
        try {
            return $this->productRepository->get(InsuranceHelper::ALMA_INSURANCE_SKU);
        } catch (NoSuchEntityException $e) {
            $message = 'No alma Insurance product in Catalog - Use a product with sku : '. InsuranceHelper::ALMA_INSURANCE_SKU;
            $this->logger->error($message,[$e]);
           throw new AlmaInsuranceProductException($message,0, $e);
        }
    }
}
