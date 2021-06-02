<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

class GitCommitComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $key = 'gitcommitcomposer';
        $ttl = now()->addHours(1);

        $commit = Cache::remember($key . '_commit', $ttl, function () {
            $commit = $this->getShellExec('git describe --always --tags --dirty');
            return $commit;
        });

        $branch = Cache::remember($key . '_branch', $ttl, function () {
            $branch = $this->getShellExec('git rev-parse --abbrev-ref HEAD');
            return $branch;
        });

        $view->with('gitcommitcomposer_branch', $branch);
        $view->with('gitcommitcomposer_commit', $commit);
    }

    public function getShellExec($command)
    {
        $old_cwd = getcwd();
        chdir(base_path());
        $result = shell_exec($command);
        chdir($old_cwd);
        return trim($result);
    }
}
