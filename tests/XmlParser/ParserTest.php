<?php

namespace XmlParser;

use App\Service\ActiveChecker;
use App\Service\FeedReader;
use App\Service\FeedWriter;
use App\Service\InfoPrinter;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function setUp(): void
    {
        $this->clearOutputFiles();
    }

    public function tearDown(): void
    {
        $this->clearOutputFiles();
    }

    public function clearOutputFiles(): void
    {
        $samples = [];
        $samples[] = __DIR__ . '/sample1_out.xml';
        $samples[] = __DIR__ . '/sample2_out.xml';
        $samples[] = __DIR__ . '/sample3_out.xml';
        $samples[] = __DIR__ . '/sample4_out.xml';
        foreach ($samples as $sample) {
            file_put_contents($sample, '');
        }
    }

    /**
     * @throws \Exception
     */
    public function testSampleOne(): void
    {
        $inputPath = __DIR__ . '/sample1.xml';
        $outputPath = __DIR__ . '/sample1_out.xml';

        $infoPrinter = new InfoPrinter();
        $activeChecker = new ActiveChecker();
        $activeChecker->setCheckTime('09-11-2021 12:34');
        $xmlWriter = new \XMLWriter();
        $feedWriter = new FeedWriter($xmlWriter);
        $feedReader = new FeedReader($feedWriter, $activeChecker, $infoPrinter);

        $feedReader->parse($inputPath, $outputPath);

        $outputXml = simplexml_load_string(file_get_contents($outputPath));
        $inputXml = simplexml_load_string(file_get_contents($inputPath));

        $this->assertEquals($inputXml->offer->id, $outputXml->offer->id);
        $this->assertEquals($inputXml->offer->name, $outputXml->offer->name);
        $this->assertEquals($inputXml->offer->category, $outputXml->offer->category);
        $this->assertEquals($inputXml->offer->description, $outputXml->offer->description);
        $this->assertEquals($inputXml->offer->price, $outputXml->offer->price);
        $this->assertEquals($inputXml->offer->url, $outputXml->offer->url);
        $this->assertEquals($inputXml->offer->image_url, $outputXml->offer->image_url);
        $this->assertEquals($inputXml->offer->opening_times, $outputXml->offer->opening_times);
        $this->assertEquals('true', $outputXml->offer->is_active);
    }

    public function testSampleTwo(): void
    {
        $inputPath = __DIR__ . '/sample2.xml';
        $outputPath = __DIR__ . '/sample2_out.xml';

        $infoPrinter = new InfoPrinter();
        $activeChecker = new ActiveChecker();
        $activeChecker->setCheckTime('13-11-2021 01:59');
        $xmlWriter = new \XMLWriter();
        $feedWriter = new FeedWriter($xmlWriter);
        $feedReader = new FeedReader($feedWriter, $activeChecker, $infoPrinter);

        $feedReader->parse($inputPath, $outputPath);

        $outputXml = simplexml_load_string(file_get_contents($outputPath));
        $inputXml = simplexml_load_string(file_get_contents($inputPath));

        $this->assertEquals($inputXml->offer->id, $outputXml->offer->id);
        $this->assertEquals($inputXml->offer->name, $outputXml->offer->name);
        $this->assertEquals($inputXml->offer->category, $outputXml->offer->category);
        $this->assertEquals($inputXml->offer->description, $outputXml->offer->description);
        $this->assertEquals($inputXml->offer->price, $outputXml->offer->price);
        $this->assertEquals($inputXml->offer->url, $outputXml->offer->url);
        $this->assertEquals($inputXml->offer->image_url, $outputXml->offer->image_url);
        $this->assertEquals($inputXml->offer->opening_times, $outputXml->offer->opening_times);
        $this->assertEquals('true', $outputXml->offer->is_active);
    }

    public function testSampleThree(): void
    {
        $inputPath = __DIR__ . '/sample3.xml';
        $outputPath = __DIR__ . '/sample3_out.xml';

        $infoPrinter = new InfoPrinter();
        $activeChecker = new ActiveChecker();
        $activeChecker->setCheckTime('13-11-2021 00:00');
        $xmlWriter = new \XMLWriter();
        $feedWriter = new FeedWriter($xmlWriter);
        $feedReader = new FeedReader($feedWriter, $activeChecker, $infoPrinter);
        $feedReader->parse($inputPath, $outputPath);

        $outputXml = simplexml_load_string(file_get_contents($outputPath));
        $inputXml = simplexml_load_string(file_get_contents($inputPath));

        $this->assertEquals($inputXml->offer->id, $outputXml->offer->id);
        $this->assertEquals($inputXml->offer->name, $outputXml->offer->name);
        $this->assertEquals($inputXml->offer->category, $outputXml->offer->category);
        $this->assertEquals($inputXml->offer->description, $outputXml->offer->description);
        $this->assertEquals($inputXml->offer->price, $outputXml->offer->price);
        $this->assertEquals($inputXml->offer->url, $outputXml->offer->url);
        $this->assertEquals($inputXml->offer->image_url, $outputXml->offer->image_url);
        $this->assertEquals($inputXml->offer->opening_times, $outputXml->offer->opening_times);
        $this->assertEquals('true', $outputXml->offer->is_active);
    }

    public function testSampleFour(): void
    {
        $inputPath = __DIR__ . '/sample4.xml';
        $outputPath = __DIR__ . '/sample4_out.xml';

        $infoPrinter = new InfoPrinter();
        $activeChecker = new ActiveChecker();
        $activeChecker->setCheckTime('08-11-2021 09:59');
        $xmlWriter = new \XMLWriter();
        $feedWriter = new FeedWriter($xmlWriter);
        $feedReader = new FeedReader($feedWriter, $activeChecker, $infoPrinter);
        $feedReader->parse($inputPath, $outputPath);

        $outputXml = simplexml_load_string(file_get_contents($outputPath));
        $inputXml = simplexml_load_string(file_get_contents($inputPath));

        $this->assertEquals($inputXml->offer->id, $outputXml->offer->id);
        $this->assertEquals($inputXml->offer->name, $outputXml->offer->name);
        $this->assertEquals($inputXml->offer->category, $outputXml->offer->category);
        $this->assertEquals($inputXml->offer->description, $outputXml->offer->description);
        $this->assertEquals($inputXml->offer->price, $outputXml->offer->price);
        $this->assertEquals($inputXml->offer->url, $outputXml->offer->url);
        $this->assertEquals($inputXml->offer->image_url, $outputXml->offer->image_url);
        $this->assertEquals($inputXml->offer->opening_times, $outputXml->offer->opening_times);
        $this->assertEquals('false', $outputXml->offer->is_active);
    }
}