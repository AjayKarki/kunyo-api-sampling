<?php

namespace Foundation\Mixins;

/**
 * Trait Emitter
 *
 * LiveWire message emitter
 *
 * @package Foundation\Mixins
 */
trait Emitter
{

    public function emitSuccess($message)
    {
        $this->flash($message);
    }

    public function emitFailure($message)
    {
        $this->flash($message, 'error');
    }

    public function flash($message, $type = 'success')
    {
        $this->emit('alert', [ $type, $message, ]);
    }

}
