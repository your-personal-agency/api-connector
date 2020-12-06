<?php

namespace Ypa\Api\Connectors;

class WooCommerceConnector extends WordPressConnector
{
    /**
     * @var string
     */
    private $wooUrl = '';

    const CATEGORIES = 'categories';
    const PRODUCTS = 'products';
    const ORDER = 'orders';

    public function getCategories(int $pPerPage = 100)
    {
        $url = $this->wooUrl . self::PRODUCTS . '/' . self::CATEGORIES;
        $categories = $this->get($url . $this->getConsumerString() . '&per_page=' . $pPerPage);
        return $categories;
    }

    public function getCategory(string $pSlug)
    {
        if (empty($this->wooUrl)) {
            return $this->failedResponse(
                ErrorMessages::EMPTY_WOO_BASE_URL,
                400,
                ['method' => 'WooCommerceConnector->getCategory']
            );
        }
        $path = self::PRODUCTS . '/' . self::CATEGORIES;
        $queryString = '&slug=' . $pSlug;
        $categories = $this->get($path . $this->getConsumerString() . $queryString);
        return $categories;
    }


    public function getProducts(int $pPage = 1, int $pPerPage = 10)
    {
        if (empty($this->wooUrl)) {
            return $this->failedResponse(
                ErrorMessages::EMPTY_WOO_BASE_URL,
                400,
                ['method' => 'WooCommerceConnector->getProducts']
            );
        }
        $products = $this->get(self::PRODUCTS . $this->getConsumerString() . '&per_page=' . $pPerPage . '&page=' . $pPage);
        return $products;
    }

    public function getProductSearchResults(string $pTerm, int $pPage = 1, int $pPerPage = 10)
    {
        if (empty($this->wooUrl)) {
            return $this->failedResponse(
                ErrorMessages::EMPTY_WOO_BASE_URL,
                400,
                ['method' => 'WooCommerceConnector->getProductSearchResults']
            );
        }
        $path = self::PRODUCTS . $this->getConsumerString();
        $products = $this->get($path . '&search=' . $pTerm . '&per_page=' . $pPerPage . '&page=' . $pPage);
        return $products;
    }


    public function getProduct(string $pSlug)
    {
        if (empty($this->wooUrl)) {
            return $this->failedResponse(
                ErrorMessages::EMPTY_WOO_BASE_URL,
                400,
                ['method' => 'WooCommerceConnector->getProduct']
            );
        }
        $queryString = '&slug=' . $pSlug;
        $products = $this->get(self::PRODUCTS . $this->getConsumerString() . $queryString);

        return $products;
    }

    public function getOrdersForUser(int $pUserId, int $pPage = 1, int $pPerPage = 10)
    {
        if (empty($this->wooUrl)) {
            return $this->failedResponse(
                ErrorMessages::EMPTY_WOO_BASE_URL,
                400,
                ['method' => 'WooCommerceConnector->getOrdersForUser']
            );
        }
        $pagination = '&customer=' . $pUserId . '&page=' . $pPage . '&per_page=' . $pPerPage;
        return $this->get(self::ORDER . $this->getConsumerString() . $pagination, [
            'headers' => [
                'Accept' => 'application/json',
            ]
        ]);
    }

    public function getAllOrders(int $pPage = 1, int $pPerPage = 6)
    {
        if (empty($this->wooUrl)) {
            return $this->failedResponse(
                ErrorMessages::EMPTY_WOO_BASE_URL,
                400,
                ['method' => 'WooCommerceConnector->getAllOrders']
            );
        }
        $pagination = '&page=' . $pPage . '&per_page=' . $pPerPage;
        return $this->get(self::ORDER . $this->getConsumerString() . $pagination, [
            'headers' => [
                'Accept' => 'application/json',
            ]
        ]);
    }

    public function postOrder(array $pData)
    {
        $response = $this->post(self::ORDER . $this->getConsumerString(), [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'json' => $pData
        ]);
        return $response;
    }

    /**
     * getConsumerrString
     */
    protected function getConsumerString()
    {
        $key = env('WOO_CONSUMER_KEY');
        $secret = env('WOO_CONSUMER_SECRET');
        return "?consumer_key={$key}&consumer_secret={$secret}";
    }

    /**
     * Get WooCommerce API Url with trailing slash
     * @return string
     */
    protected function getWooCommerceUrl(): string
    {
        // add trailing slashs
        if (substr($this->wooUrl, -1) !== '/') {
            return $this->wooUrl . '/';
        }
        return $this->wooUrl;
    }

    /**
     * set WooCommerce API Url
     * @param string $pUrl
     */
    protected function setWooCommerceUrl(string $pUrl)
    {
        $this->wooUrl = $pUrl;
        $this->addConfig([
            'base_uri' => $this->getWooCommerceUrl()
        ]);
    }
}
