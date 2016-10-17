<?php

namespace Stitch;

use SplFileObject;

/**
 *
 * @property string $filename
 * @property array  $settings
 *
 */
class Stitch extends SplFileObject
{

    /**
     *
     * The method for open file
     *
     */
    const   OPEN_READ_ONLY = "r",
        OPEN_READ_WRITE_PLUS = "r+",
        OPEN_WRITE_ONLY_CREATE = "w",
        OPEN_WRITE_READ_CREATE = "w+",
        OPEN_READ_ONLY_END_FILE = "a",
        OPEN_READ_WRITE_END_FILE = "a+",
        OPEN_CREATE_WRITE = "x",
        OPEN_CREATE_WRITE_READ = "x+",
        OPEN_WRITE_ONLY_CREATE_UNTRUNCATE = "c",
        OPEN_WRITE_READ_CREATE_UNTRUNCATE = "c+";
    /**
     * Name of file serialize
     * @var string
     *
     */
    private $filename;
    private $settings = [
        'csv' => [
            "delimiter" => ';',
            "enclosure" => '"',
            "escape"    => '"'
        ]
    ];

    ////////////////////////////////////////////////////////////////////////
    ////////////////////////////Constructor/////////////////////////////////
    ////////////////////////////////////////////////////////////////////////

    /**
     *
     * Create a new Stitch instance.
     *
     * @param string $filename
     * @param        self Constant|string $openStreamType
     * @param array  $settings
     */
    public function __construct($filename, $openStreamType = null, $settings = [])
    {

        if ($openStreamType == null) {
            $openStreamType = self::OPEN_READ_ONLY_END_FILE;
        }

        if ((bool)$settings) {
            $this->settings = $settings;
        }

        parent::__construct($filename, $openStreamType);
        parent::setCsvControl(
            $this->settings['csv']['delimiter'],
            $this->settings['csv']['enclosure'],
            $this->settings['csv']['escape']
        );
        $this->filename = $filename;
    }

    public static function instance($filename, $openStreamType = null, $settings = [])
    {
        return new static ($filename, $openStreamType, $settings);
    }

    public function addHeader(array $inputHeader = [])
    {
        return self::fputcsv(
            $inputHeader,
            $this->settings['csv']['delimiter'],
            $this->settings['csv']['enclosure']
        );
    }

    public function writeCollection($collection = [])
    {

        foreach ($collection as $lineItems) {
            $lineItems = $this->normalizeData($lineItems);
            self::writeContentLine($lineItems);
        }
    }

    public function writeContentLine(array  $lineItems = [])
    {
        $lineItems = $this->normalizeData($lineItems);

        return self::fputcsv($lineItems,
            $this->settings['csv']['delimiter'],
            $this->settings['csv']['enclosure']
        );
    }

    public function remove()
    {
        if (file_exists($this->filename)) {
            return unlink($this->filename);
        }
        return false;
    }

    private function normalizeData($lineItems)
    {
        if (is_string($lineItems)) {
            return $lineItems;
        }
        $lineItems = array_map(function ($var) {
            if (is_array($var)) {
                return json_encode($var);
            }
            if (is_object($var) && $var instanceof \JsonSerializable) {
                return json_encode($var);
            }
            if (is_object($var)) {
                return "[object Object]";
            }
            return $var;
        }, $lineItems);
        return $lineItems;
    }
}

