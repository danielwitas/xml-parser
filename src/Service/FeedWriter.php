<?php

namespace App\Service;


class FeedWriter
{
    private \XMLWriter $writer;
    private const OUTPUT_FILENAME = 'feed_out.xml';
    private const VERSION = '1.0';
    private const ENCODING = 'UTF-8';

    public function __construct(\XMLWriter $writer)
    {
        $this->writer = $writer;
    }

    public function startDocument($outputPath = null): void
    {
        $outputPath = $outputPath ?? __DIR__ . '/../../feeds/' . self::OUTPUT_FILENAME;
        $this->writer->openUri($outputPath);
        $this->writer->setIndent(true);
        $this->writer->startDocument(self::VERSION, self::ENCODING);
        $this->writer->startElement('offers');
    }

    public function finishDocument(): void
    {
        $this->writer->endElement();
        $this->writer->endDocument();
        $this->writer->flush();
    }

    public function createOffer(array $offer): void
    {
        $this->writer->startElement('offer');
        foreach ($offer as $name => $data) {
            $this->createOfferProperty($name, $data);
        }
        $this->writer->endElement();
    }

    public function createOfferProperty($name, $data): void
    {
        $this->writer->startElement($name);
        $this->writer->writeCdata($data);
        $this->writer->endElement();
    }
}