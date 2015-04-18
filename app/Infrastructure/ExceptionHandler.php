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

namespace App\Infrastructure;

use Exception;
use Psr\Log\LoggerInterface;
use Illuminate\Http\Response;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;

class ExceptionHandler implements ExceptionHandlerContract
{

    /**
     * The log implementation.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $log;

    /**
     * Create a new exception handler instance.
     *
     * @param \Psr\Log\LoggerInterface $log
     */
    public function __construct(LoggerInterface $log)
    {

        $this->log = $log;

    }

    /**
     * Report or log an exception.
     *
     * @param \Exception $e
     * @return void
     */
    public function report(Exception $e)
    {

        $this->log->error((string) $e);

    }

    /**
     * Render an exception into a response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $e)
    {

        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());

        return new Response($whoops->handleException($e), $e->getStatusCode(), $e->getHeaders());

    }

    /**
     * Render an exception to the console.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Exception $e
     * @return void
     */
    public function renderForConsole($output, Exception $e)
    {

        $output->writeln((string) $e);

    }

}