<?php

namespace YusufTogtay\Generator\Http\Controllers;

use YusufTogtay\Generator\Services\NodeParserService;
use YusufTogtay\Generator\Services\XmlParserService;


class SetupController
{
    /**
     * @var XmlParserService $xmlParser
     */
    protected $xmlParser;

    public function __construct(XmlParserService $xmlParser)
    {
        $this->xmlParser = $xmlParser;
    }

    /**
     * Display a Setup View.
     */
    public function index()
    {
        $xmlFilePath = config('laravel_generator.base_xml', base_path('/schemas/baseXml.drawio'));
        $this->xmlParser->setFilePath($xmlFilePath);
        try {
            $nodeValues = $this->xmlParser->getNodeValues();
            $parsedNodes = NodeParserService::parseNodes($nodeValues);
            dd($parsedNodes);
            return response()->json([
                'success' => true,
                'data' => $nodeValues,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
