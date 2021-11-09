<?php

namespace App\Service;

use phpDocumentor\Reflection\Types\Resource_;

class FeedReader
{
    private InfoPrinter $infoPrinter;
    private ActiveChecker $activeChecker;
    private FeedWriter $writer;
    private $parser;
    private array $offer;
    private ?string $pointer;
    private const BYTE_CHUNK = 4096;
    private const MODE = 'rb';


    public function __construct(FeedWriter $writer, ActiveChecker $activeChecker, InfoPrinter $infoPrinter)
    {
        $this->writer = $writer;
        $this->activeChecker = $activeChecker;
        $this->infoPrinter = $infoPrinter;
        $this->parser = xml_parser_create();
        xml_set_object($this->parser, $this);
        xml_set_element_handler($this->parser, "tagOpen", "tagClose");
        xml_set_character_data_handler($this->parser, "cdata");

    }

    public function __destruct()
    {
        xml_parser_free($this->parser);
        unset($this->parser);
    }

    public function parse(string $inputPath, string $outputPath = null): void
    {
        $this->infoPrinter->initReport();
        $this->writer->startDocument($outputPath);
        $stream = fopen($inputPath, self::MODE);
        while (($data = fread($stream, self::BYTE_CHUNK))) {
            xml_parse($this->parser, $data);
        }
        xml_parse($this->parser, '', true);
        fclose($stream);
        $this->writer->finishDocument();
    }

    /**
     * @throws \JsonException
     */
    private function handleOffer(): void
    {
        $openingTimes = json_decode($this->offer['opening_times'], true, 512, JSON_THROW_ON_ERROR);
        if (null === $openingTimes) {
            throw new \Exception('Cannot decode opening hours. Invalid json');
        }
        $isOpen = $this->activeChecker->isOpen($openingTimes);
        $this->infoPrinter->addCount($isOpen);
        $this->offer['is_active'] = var_export($isOpen, true);
        $this->writer->createOffer($this->offer);
    }

    private function tagOpen($parser, $name, $attributes): void
    {
        switch ($name) {
            case 'OFFER':
                $this->offer = [];
                break;
            case 'ID':
                $this->pointer = 'id';
                break;
            case 'NAME':
                $this->pointer = 'name';
                break;
            case 'CATEGORY':
                $this->pointer = 'category';
                break;
            case 'DESCRIPTION':
                $this->pointer = 'description';
                break;
            case 'PRICE':
                $this->pointer = 'price';
                break;
            case 'URL':
                $this->pointer = 'url';
                break;
            case 'IMAGE_URL':
                $this->pointer = 'image_url';
                break;
            case 'OPENING_TIMES':
                $this->pointer = 'opening_times';
                break;
            default:
                $this->pointer = null;
        }
    }

    private function tagClose($parser, $name): void
    {
        if ($name === 'OFFER') {
            $this->handleOffer();
        }
    }

    private function cdata($parser, $data): void
    {
        if (trim($data) !== '') {
            if (isset($this->offer[$this->pointer])) {
                $this->offer[$this->pointer] .= $data;
            } else {
                $this->offer[$this->pointer] = $data;
            }
        }
    }
}