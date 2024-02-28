<?php

declare(strict_types=1);

namespace Miko\LaravelLatte;

use Latte\Engine;


class LatteEngine implements \Illuminate\Contracts\View\Engine
{
    
    private $latte;
    
    public function __construct(Engine $latte)
    {
        $this->latte = $latte;
    }

   /**
     * Get the evaluated contents of the view.
     *
     * @param  string  $path
     * @param  array  $data
     * @return string
     */
    public function get($path, array $data = [])
    {
        return $this->latte->renderToString($path, $data);
    }


}
