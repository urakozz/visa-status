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

namespace App\Http\Controllers;


use App\Components\ResultContainer\ResultContainer;
use App\Components\Retriever;
use App\Components\StorageAdapter;
use Kozz\Laravel\Facades\Guzzle;
use Predis\Connection\ConnectionException;

class CheckController extends Controller
{
    /**
     * @var Retriever
     */
    protected $retriever;


    public function __construct()
    {
        $this->retriever = new Retriever(Guzzle::getFacadeRoot(), new StorageAdapter(\LRedis::getFacadeRoot()));
    }

    public function index($id = null)
    {
        $id = (int) $id;
        try {
            if (!$id) {
                throw new \DomainException("Empty Id", 503);
            }
            $data = $this->retriever->lookForId($id);
            if (!$data) {
                throw new \DomainException("Id Not Found", 404);
            }
            $result = new ResultContainer($data);
        } catch (ConnectionException $e) {
            $result = new ResultContainer($e->getMessage(), $e->getCode());
        } catch (\DomainException $e) {
            $result = new ResultContainer($e->getMessage(), $e->getCode());
        } finally {
            $result->setId($id);
        }

        return view('result', ['data' => $result]);

    }

    public function brutal()
    {
        return response($this->retriever->lookForId(3132727, true));
    }
}