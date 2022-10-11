<?php

namespace OxidSolutionCatalysts\Adyen\Core;

use OxidEsales\Eshop\Core\Header;
use OxidEsales\Eshop\Core\Registry;

class Response
{
    private const HEADER_OK = [
        'code' => 200,
        'status' => 'OK'
    ];

    private const HEADER_UNAUTHORIZED = [
        'code' => 401,
        'status' => 'Unauthorized '
    ];

    private const HEADER_FORBIDDEN = [
        'code' => 403,
        'status' => 'Forbidden '
    ];

    private const HEADER_NOT_FOUND = [
        'code' => 404,
        'status' => 'Not Found'
    ];

    private const HEADER_NOT_ALLOWED = [
        'code' => 405,
        'status' => 'Method not Allowed'
    ];

    private const HEADER_INTERNAL_SERVER_ERROR = [
        'code' => 500,
        'status' => 'Internal Server Error'
    ];

    /**
     * @var array
     */
    protected array $data;

    /**
     * @var string
     */
    protected string $status;

    /**
     * @var int
     */
    protected int $code;

    /**
     * @param array $data
     * @return Response
     */
    public function setData(array $data): Response
    {
        return $this->setResponse(self::HEADER_OK, $data);
    }

    /**
     * @return Response
     */
    public function setGenericSuccess(): Response
    {
        return $this->setResponse(self::HEADER_OK);
    }

    /**
     * @return Response
     */
    public function setUnauthorized(): Response
    {
        return $this->setResponse(self::HEADER_UNAUTHORIZED);
    }

    /**
     * @return Response
     */
    public function setForbidden(): Response
    {
        return $this->setResponse(self::HEADER_FORBIDDEN);
    }

    /**
     * @return Response
     */
    public function setNotFound(): Response
    {
        return $this->setResponse(self::HEADER_NOT_FOUND);
    }

    /**
     * @return Response
     */
    public function setNotAllowed(): Response
    {
        return $this->setResponse(self::HEADER_NOT_ALLOWED);
    }

    /**
     * @return Response
     */
    public function setServerError(): Response
    {
        return $this->setResponse(self::HEADER_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param array $header
     * @param array|null $data
     * @return Response
     */
    private function setResponse(array $header, ?array $data = null): Response
    {
        $status = "{$header['code']} {$header['status']}";

        $this->data = $data ?? ['message' => $status];
        $this->code = (int)$header['code'];
        $this->status = $status;

        return $this;
    }

    /**
     * @throws \JsonException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function sendJson(): void
    {
        http_response_code($this->code);
        $header = Registry::get(Header::class);
        $header->setHeader('Cache-Control: no-cache');
        $header->setHeader('Content-Type: application/json');
        $header->setHeader('Status: ' . $this->status);

        Registry::getUtils()->showMessageAndExit(
            json_encode(
                $this->data,
                JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK
            )
        );
    }
}
