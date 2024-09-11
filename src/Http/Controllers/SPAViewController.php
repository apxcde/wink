<?php

namespace Wink\Http\Controllers;

use Wink\Wink;

class SPAViewController
{
    public function __invoke()
    {
        return view('wink::layout', [
            'winkScriptVariables' => Wink::scriptVariables(),
        ]);
    }
}
