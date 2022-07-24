<?php

namespace JPI;

use DateTime;

trait MeTrait {

    protected $startDate = null;
    protected $professionalStartDate = null;

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

    /**
     * @param bool $object
     * @return DateTime|string
     */
    public function getProfessionalStartDate(bool $object = true) {
        if (!$object) {
            return self::PROFESSIONAL_START_DATE;
        }

        if ($this->professionalStartDate === null) {
            $this->professionalStartDate = new DateTime(self::PROFESSIONAL_START_DATE);
        }

        return $this->professionalStartDate;
    }
}
