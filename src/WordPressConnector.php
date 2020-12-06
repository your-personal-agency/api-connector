<?php

namespace Ypa\Api\Connectors;

class WordPressConnector extends AbstractConnector
{

    /**
     * @var string
     */
    private $wordPressUrl = '';

    /**
     * get Posts by posttype
     * @param string $pPostType
     * @param int $pPerPage
     * @return array
     */
    public function getPosts(string $pPostType, int $pPage = 1, int $pPerPage = 10): array
    {
        if (empty($this->wordPressUrl)) {
            return $this->failedResponse(
                ErrorMessages::EMPTY_WP_BASE_URL,
                400,
                ['post_type' => $pPostType, 'method' => 'WordPressConnector->getPosts']
            );
        }
        $pagination = '?page=' . $pPage . '&per_page=' . $pPerPage;
        return $this->get($pPostType . $pagination, [
            'headers' => [
                'Accept' => 'application/json',
            ]
        ]);
    }

    /**
     * get Single post by posttype
     * @param string $pPostType
     * @param string $pSlug
     * @return array
     */
    public function getSingle(string $pPostType, string $pSlug): array
    {
        if (empty($this->wordPressUrl)) {
            return $this->failedResponse(
                ErrorMessages::EMPTY_WP_BASE_URL,
                400,
                ['post_type' => $pPostType, 'method' => 'WordPressConnector->getSingle']
            );
        }
        return $this->get($pPostType . '?slug=' . $pSlug);
    }

    /**
     * Get WordPress API Url with trailing slash
     * @return string
     */
    protected function getWordPressUrl(): string
    {
        // add trailing slashs
        if (substr($this->wordPressUrl, -1) !== '/') {
            return $this->wordPressUrl . '/';
        }
        return $this->wordPressUrl;
    }

    /**
     * set WordPress API Url
     * @param string $pUrl
     */
    protected function setWordPressUrl(string $pUrl)
    {
        $this->wordPressUrl = $pUrl;

        $this->addConfig([
            'base_uri' => $this->getWordPressUrl()
        ]);
    }
}
