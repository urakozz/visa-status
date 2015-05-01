<?php namespace App\Console\Commands;

use App\Components\Retriever;
use App\Components\StorageAdapter;
use Illuminate\Console\Command;
use Kozz\Laravel\Facades\Guzzle;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CheckUpdates extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    protected $retriever;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->retriever = new Retriever(Guzzle::getFacadeRoot(), new StorageAdapter(\LRedis::getFacadeRoot()));
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->retriever->retrieve()->save();
        $results = count($this->retriever->getResults());
        $test    = date("Y-m-d H:i:s") . " - Got " . $results . " results\n";
        $this->comment($test);
        file_put_contents("tmp.log", $test, FILE_APPEND);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
//			['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
//			['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }

}
