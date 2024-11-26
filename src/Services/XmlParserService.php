<?php

namespace YusufTogtay\Generator\Services;

use SimpleXMLElement;

class XmlParserService
{
    protected $xmlFilePath;

    /**
     * @var SimpleXMLElement|false|null
     */
    protected $xml;

    /**
     * XmlParserService constructor.
     * @param string|null $xmlFilePath
     */
    public function __construct(?string $xmlFilePath = null)
    {
        $this->xmlFilePath = $xmlFilePath;
    }

    /**
     * Dosya yolunu dinamik olarak ayarla.
     *
     * @param string $xmlFilePath
     * @return void
     */
    public function setFilePath(string $xmlFilePath): void
    {
        $this->xmlFilePath = $xmlFilePath;
    }

    public function getXml()
    {
        // XML dosyasını kontrol et ve yükle
        if (!$this->xmlFilePath || !file_exists($this->xmlFilePath)) {
            throw new \Exception("XML dosyası bulunamadı: {$this->xmlFilePath}");
        }

        $this->xml = simplexml_load_file($this->xmlFilePath);
        if ($this->xml === false) {
            throw new \Exception("XML dosyası yüklenemedi.");
        }
    }

    /**
     * Verilen XML dosyasından mxCell id'si 'node' ile başlayan öğelerin valuelarını döndürür.
     *
     * @param string $xmlFilePath
     * @return array
     */
    public function getNodeValues(): array
    {
        if (!$this->xml) {
            $this->getXml();
        }
        // mxCell id'si 'node' ile başlayanların valuelarını bul
        $nodeValues = [];
        foreach ($this->xml->xpath('//mxCell[starts-with(@id, "node")]') as $mxCell) {
            $nodeValues[] = (string) $mxCell->attributes()->value;
        }

        return $nodeValues;
    }

    function getNodeCellsFromXml(): array
    {
        if (!$this->xml) {
            $this->getXml();
        }

        // Sonuçları tutacak dizi
        $nodeCells = [];

        // Her bir mxCell elemanını kontrol et
        foreach ($this->xml->xpath('//mxCell[starts-with(@id, "node")]') as $mxCell) {
            // mxCell elemanını diziye ekle
            $nodeCells[] = $mxCell;
        }

        return $nodeCells;
    }
}
