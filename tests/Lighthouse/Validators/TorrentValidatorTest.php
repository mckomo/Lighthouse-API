<?php

namespace Tests\Lighthouse\Validators;

use Lighthouse\Validators\TorrentValidator;
use Tests\Support\EntitySampler;

class TorrentValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TorrentValidator
     */
    private $validator;

    public function setUp()
    {
        $this->validator = new TorrentValidator();
    }

    public function test_succeeds_with_valid_entity()
    {
        $validEntity = $this->getValidTorrent();

        $result = $this->validator->isValid($validEntity);

        $this->assertTrue($result);
    }

    public function test_fails_with_null()
    {
        $result = $this->validator->isValid(null);

        $this->assertFalse($result);
    }

    /**
     * @dataProvider getNullAndEmptyString
     */
    public function test_fails_with_empty_name($invalidName)
    {
        $brokenEntity = $this->getValidTorrent();
        $brokenEntity->name = $invalidName;

        $result = $this->validator->isValid($brokenEntity);

        $this->assertFalse($result);
    }

    public function test_fails_with_invalid_UTF8_name()
    {
        $hexName = 'c4b4c3bcc5a174c3adc3b12042c3adc3a962c3ab7220eda0bdedb184eda0bdedb18ceda0bdedb293eda0bdedb298'; // Ĵüštíñ Bíébër ��������%
        $brokenEntity = $this->getValidTorrent();
        $brokenEntity->name = $this->convertToString($hexName);

        $result = $this->validator->isValid($brokenEntity);

        $this->assertFalse($result);
    }

    /**
     * @dataProvider getNullAndEmptyString
     */
    public function test_fails_with_empty_category($invalidCategory)
    {
        $brokenEntity = $this->getValidTorrent();
        $brokenEntity->category = $invalidCategory;

        $result = $this->validator->isValid($brokenEntity);

        $this->assertFalse($result);
    }

    /**
     * @dataProvider getNullAndNegativeNumber
     */
    public function test_fails_with_negative_size($invalidSize)
    {
        $brokenEntity = $this->getValidTorrent();
        $brokenEntity->size = $invalidSize;

        $result = $this->validator->isValid($brokenEntity);

        $this->assertFalse($result);
    }

    public function test_fails_with_invalid_magnet_link()
    {
        $brokenEntity = $this->getValidTorrent();
        $brokenEntity->magnetLink = 'magnet:?xt=urn:btih:BAD_HASH&dn=&tr=udp%3A%2F%2Ftracker.publicbt.com%3A80';

        $result = $this->validator->isValid($brokenEntity);

        $this->assertFalse($result);
    }

    public function test_fails_with_upload_time_format_other_than_ISO8601()
    {
        $brokenEntity = $this->getValidTorrent();
        $brokenEntity->uploadedAt = 'Sat, 27 Jun 2015 18:50:58 +00:00';

        $result = $this->validator->isValid($brokenEntity);

        $this->assertFalse($result);
    }

    public function test_fails_with_upload_at_timezone_other_than_UTC()
    {
        $brokenEntity = $this->getValidTorrent();
        $brokenEntity->uploadedAt = '2015-06-27T18:50:58+02:00';

        $result = $this->validator->isValid($brokenEntity);

        $this->assertFalse($result);
    }

    public function test_fails_with_negative_seed_count()
    {
        $brokenEntity = $this->getValidTorrent();
        $brokenEntity->seedCount = -10;

        $result = $this->validator->isValid($brokenEntity);

        $this->assertFalse($result);
    }

    public function test_fails_with_negative_peer_count()
    {
        $brokenEntity = $this->getValidTorrent();
        $brokenEntity->peerCount = -10;

        $result = $this->validator->isValid($brokenEntity);

        $this->assertFalse($result);
    }

    public function test_returns_validation_error()
    {
        $brokenEntity = $this->getValidTorrent();
        $brokenEntity->peerCount = -10;

        $this->validator->isValid($brokenEntity, $errors);
        $errorCount = count($errors);

        $this->assertEquals(1, $errorCount);
    }

    public function test_appends_validation_errors()
    {
        $brokenEntity = $this->getValidTorrent();
        $brokenEntity->uploadedAt = '2015-05-27 15:00:00';
        $brokenEntity->seedCount = -21;
        $brokenEntity->peerCount = -31;

        $this->validator->isValid($brokenEntity, $errors);
        $errorCount = count($errors);

        $this->assertEquals(3, $errorCount);
    }

    public function getNullAndEmptyString()
    {
        return [[null], ['']];
    }

    public function getNullAndNegativeNumber()
    {
        return [[null], [-10]];
    }

    private function getValidTorrent()
    {
        return EntitySampler::sampleTorrent();
    }

    private function convertToString($hex)
    {
        $string = '';

        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            $string .= chr(hexdec($hex[$i].$hex[$i + 1]));
        }

        return $string;
    }
}
