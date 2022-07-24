<?php

namespace JPI;

use DateTime;

trait MeTrait {

    protected $startDate = null;

    /**
     * @param bool $object
     * @return DateTime|string
     */
    public function getStartDate(bool $object = true) {
        if (!$object) {
            return self::START_DATE;
        }

        if ($this->startDate === null) {
            $this->startDate = new DateTime(self::START_DATE);
        }

        return $this->startDate;
    }
}
