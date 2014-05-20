<?php

namespace Stitch;

use \SplFileObject;

/**
 *
 * @property string $filename
 * @property array  $settings
 *
 */
class Stitch extends SplFileObject {

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

        /**
         *
         * The method for open file
         *
         */
        const OPEN_READ_ONLY  = "r",
                OPEN_READ_WRITE_PLUS = "r+",
                OPEN_WRITE_ONLY_CREATE = "w",
                OPEN_WRITE_READ_CREATE = "w+",
                OPEN_READ_ONLY_END_FILE = "a",
                OPEN_READ_WRITE_END_FILE = "a+",
                OPEN_CREATE_WRITE         = "x",
                OPEN_CREATE_WRITE_READ    = "x+",
                OPEN_WRITE_ONLY_CREATE_UNTRUNCATE = "c",
                OPEN_WRITE_READ_CREATE_UNTRUNCATE = "c+" ;

        ////////////////////////////////////////////////////////////////////////
        ////////////////////////////Constructor/////////////////////////////////
        ////////////////////////////////////////////////////////////////////////

        /**
         *
         * Create a new Stitch instance.
         *
         * @param string    $filename
         * @param self Constante|string $openStreamType
         * @param array     $settings
         */
        public function __construct($filename,$openStreamType = null,  $settings = array()){

                if($openStreamType == null) {
                        $openStreamType =   self::OPEN_READ_ONLY_END_FILE;
                }

                if((bool) $settings){
                        $this->settings = $settings;
                }

                parent::__construct($filename, $openStreamType);
                $this->filename = $filename;
        }
        public static function  instance ($filename,$openStreamType = null,  $settings = array()){
                return new static ($filename,$openStreamType = null,  $settings = array());
        }

        public function addHeader(array $inputHeader = array()){
                return self::fputcsv(
                        $inputHeader,
                        $this->settings['csv']['delimiter'],
                        $this->settings['csv']['enclosure']
                );
        }
        public function writeContentLine(array  $lineItems = array()){
                return self::fputcsv($lineItems,
                        $this->settings['csv']['delimiter'],
                        $this->settings['csv']['enclosure']

                );
        }

        public function writeCollection($collection =array()){

                foreach($collection as $lineItems){
                        self::writeContentLine($lineItems);
                }
        }

        public function remove(){
                if(file_exists($this->filename)){
                        return unlink($this->filename);
                }
                return false;
        }
        public function fputcsv(array $fields)
        {
                parent::fputcsv($fields,
                        $this->settings['csv']['delimiter'],
                        $this->settings['csv']['enclosure']
                );
        }
}

