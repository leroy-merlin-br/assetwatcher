<?php namespace LeroyMerlin\AssetWatcher;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App, Config;

class AssetWatcherCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'asset:watch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Watches for changes in assets and takes specific actions';

    /**
     * An array with a bunch of useless emojicons that is going to be printed when the watcher is running.
     * @var [type]
     */
    protected $eyes = array('(⚆ _ ⚆)', '(◕〝◕)', '( ° ͜ʖ °)', 'ಠ_ಠ', '*(۞_۞)*', '(⊙ _ ⊙)');

    /**
     * Array that contains all the files that are being watched
     * @var array
     */
    public $files = array();

    /**
     * Callback for when a file has changed
     * @var Closure
     */
    public $updateClosure = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $eye = rand(1, count($this->eyes)) - 1;

        $this->comment("Initializing asset watcher");

        if (! Config::get('assetwatcher::files_to_watch', [])) {

            $this->error("The 'files_to_watch' configuration is empty!");
            $this->error("Make sure that you published the configuration files of the package and that you've filled the 'files_to_watch' config correctly.");
            return 1;
        }

        $this->info($this->eyes[$eye]." Watching files...\n");

        $this->updateAndWatch();
    }

    public function updateAndWatch()
    {
        $watcher = $this->makeResourceWatcher();

        while (true) {// ¯\_(ツ)_/¯
            foreach(Config::get('assetwatcher::files_to_watch', []) as $pattern => $closure) {

                $allFiles = $this->findFiles($pattern);
                $newFiles = array_diff($allFiles, $this->files);

                foreach ($newFiles as $file) {
                    $watcher->watch($file)->onModify($this->updateFileCallback($closure, $file));

                    // If `$this->files` is not empty, then update file.
                    if ($newFiles && !empty($this->files)) {
                        $cleanFilename = substr($file, strlen(app_path())+1);
                        $this->info(date('H:i:s')." - $cleanFilename has been created.");
                        $closure($file);
                    }
                }

                $this->files = array_merge($newFiles, $this->files);
            }

            $watcher->startWatch(500000, 1000000*4);
        }
    }

    protected function updateFileCallback($closure, $file)
    {
        $thisCommand = $this;

        $updateClosure = function($file) use ($thisCommand, $closure) {
            $cleanFilename = substr($file->getPath(), strlen(app_path())+1);

            $thisCommand->info(date('H:i:s')." - $cleanFilename has been modified.");
            $closure($file->getPath());
        };

        return $updateClosure;
    }

    protected function findFiles($term)
    {
        if(! strstr($term, app_path()))
            $term = app_path().'/'.$term;

        $files = glob($term);

        foreach (glob(dirname($term).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
        {
            $files = array_merge($files, $this->findFiles($dir.'/'.basename($term)));
        }

        return $files;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array();
    }

    public function makeResourceWatcher()
    {
        $files   = new \Illuminate\Filesystem\Filesystem;
        $tracker = new \JasonLewis\ResourceWatcher\Tracker;
        $watcher = new \JasonLewis\ResourceWatcher\Watcher($tracker, $files);

        return $watcher;
    }

}
