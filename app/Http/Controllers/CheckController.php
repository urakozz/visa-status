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


use App\Components\Retriever;
use App\Components\StorageAdapter;
use Kozz\Laravel\Facades\Guzzle;

class CheckController extends Controller
{
    /**
     * @var Retriever
     */
    protected $retriever;


    public function __construct()
    {
        $this->retriever = new Retriever(Guzzle::getFacadeRoot(), new StorageAdapter(\Cache::getFacadeRoot()));
    }

    public function index($id = null)
    {
        $id = (int)$id;
        if (!$id) {
            return response("Empty id");
        }

        if ($data = $this->retriever->lookForId($id, true)) {
            return response($data);
        }

        return response("Id not found");


    }
}