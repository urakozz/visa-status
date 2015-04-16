<?php
/**
 * PHP Version 5
 *
 * @category  H24
 * @package
 * @author    "Yury Kozyrev" <yury.kozyrev@home24.de>
 * @copyright 2015 Home24 GmbH
 * @license   Proprietary license.
 * @link      http://www.home24.de
 */

namespace App\Components;


use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;

class Retriever
{
    /**
     * @var StorageAdapter
     */
    protected $redis;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var \Smalot\PdfParser\Parser
     */
    protected $parser;

    /**
     * @var array
     */
    protected $results = [];

    /**
     * @var string
     */
    protected $url = "http://www.germania.diplo.de/contentblob/4048654/Daten/5336833/1entschiedenevisumantraege.pdf";

    public function __construct(Client $client, StorageAdapter $redis)
    {
        $this->redis  = $redis;
        $this->client = $client;
        $this->parser = new \Smalot\PdfParser\Parser();
    }

    public function retrieve()
    {
        /** @var Response $data */
        $data = $this->client->get($this->url);
        $pdf  = $this->parser->parseContent($data->getBody()->getContents());

        $lines      = preg_split('/[\n\r]/iu', $pdf->getText());
        $linePossib = 0;
        foreach ($lines as $num => $line) {
            if (0 === $linePossib && false !== mb_strpos($line, "mitzubringende Dokumente")) {
                $linePossib = $num;
                continue;
            }
            if (0 === $linePossib) {
                continue;
            }
            if (preg_match("/^\d{7}\s\s/u", $line)) {
                $idl = preg_replace("/^([0-9]{7})\s.*$/u", "$1", $line);
                $this->addResult($idl, $line);
                continue;
            }
            if (preg_match("/^([0-9]{7})\s\((\d{7})\).*$/u", $line, $matches)) {
                $this->addResult($matches[1], $line);
                $this->addResult($matches[2], $line);
            }
        }
        return $this;
    }

    public function getResults()
    {
        return $this->results;
    }

    public function save()
    {
        foreach ($this->getResults() as $key => $line) {
            $this->redis->set($key, $line);
        }
        return $this;
    }

    public function lookForId($id, $force = false)
    {
        $id = (int)$id;
        if ($data = $this->redis->get($id)) {
            return $data;
        }
        if($force){
            $this->retrieve()->save();
        }
        return isset($this->results[$id])
            ? $this->results[$id]
            : null;
    }


    protected function addResult($id, $line)
    {
        $this->results[(int) $id] = $line;
    }

}