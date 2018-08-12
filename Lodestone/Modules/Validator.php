<?php

namespace Lodestone\Modules;

use Lodestone\Modules\Exceptions;

class Validator
{
    const HTTP_OK = 200;
    const HTTP_PERM_REDIRECT = 308;
    const HTTP_SERVICE_NOT_AVAILABLE = 503;
    const HTTP_NOT_FOUND = 404;

    public $object;
    public $name;

    /**
     * @var int
     */
    public $id;

    /**
     * Is not allowed for a singleton
     */
    protected function __clone() {}

    /**
   * @param $object
   * @param $name
   * @param $id integer (optional)
   * @return $this
   */
    public function check($object, $name, $id = null)
    {
        $this->object = $object;
        $this->name = $name;
        $this->id = $id;
        return $this;
    }

    /**
     * @return $this
     */
    public function isNotEmpty()
    {
        if (empty($this->object)) {
            throw Exceptions::emptyValidation($this);
        }

        return $this;
    }
    
    /**
     * A deleted character produces a 404 error
     *
     * @return $this
     */
    public function isFound()
    {
        if ($this->object == self::HTTP_NOT_FOUND) {
            throw new Exceptions(sprintf('Page %s was not found (404)', $this->id), self::HTTP_NOT_FOUND);
        }

        return $this;
    }

    /**
     * When the lodestone is on maintenance, it returns 503 for all pages
     *
     * @return $this
     */
    public function isNotMaintenance()
    {
        if ($this->object == self::HTTP_SERVICE_NOT_AVAILABLE) {
            throw new Exceptions('Lodestone not available', self::HTTP_SERVICE_NOT_AVAILABLE, $previous);
        }

        return $this;
    }

    /**
     * 2XX and 3XX Status codes are for successful connections or redirects (so no error)
     *
     * @see https://de.wikipedia.org/wiki/HTTP-Statuscode
     * @return $this
     */
    public function isNotHttpError()
    {
        if ($this->object < self::HTTP_OK || $this->object > self::HTTP_PERM_REDIRECT) {
            throw new Exceptions('Requested page '.$this->id.' is not available');
        }

        return $this;
    }
}
