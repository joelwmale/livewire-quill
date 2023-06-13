<?php

namespace Joelwmale\LivewireQuill\Traits;

trait HasQuillEditor
{
    public function getListeners()
    {
        return array_merge($this->listeners, [
            'contentChanged',
        ]);
    }
}
