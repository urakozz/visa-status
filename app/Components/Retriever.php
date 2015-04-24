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
    protected $pageUrl = "http://www.germania.diplo.de/Vertretung/russland/ru/02-mosk/1-visa/3-merkblaetter/nationale-visa/0-nationale-visa.html";

    public function __construct(Client $client, StorageAdapter $redis)
    {
        $this->redis  = $redis;
        $this->client = $client;
        $this->parser = new \Smalot\PdfParser\Parser();
    }

    public function retrieve()
    {
        $pdfUrl = $this->getPdfUrl();
        /** @var Response $data */
        $data = $this->client->get($pdfUrl);
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
            if (preg_match("/^\d{7}/u", $line)) {
                preg_match_all("/[0-9]{6,7}/u", $line, $matches);
                foreach($matches[0] as $idl){
                    $this->addResult($idl, $line);
                }
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

    protected function getPdfUrl()
    {
        $request = $this->client->createRequest('POST', $this->pageUrl);
        $response = $this->client->send($request);
        $html = $response->getBody()->getContents();
        $pos = mb_strpos($html, "1entschiedenevisumantraege.pdf");
        $possibleString = mb_substr($html, $pos - 100, 200);
        preg_match('/href\=\"(.*)\"/iu', $possibleString, $matches);
        $link = $matches[1];
        $pdfLink =  sprintf("%s://%s%s", $request->getScheme(), $request->getHost(), $link);
        return $pdfLink;
    }

}