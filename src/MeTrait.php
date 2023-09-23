<?php

declare(strict_types=1);

namespace JPI;

use DateTime;

trait MeTrait {

    protected ?DateTime $startDate = null;
    protected ?DateTime $professionalStartDate = null;

    public function getStartDate(bool $object = true): DateTime|string {
        if (!$object) {
            return self::START_DATE;
        }

        if ($this->startDate === null) {
            $this->startDate = new DateTime(self::START_DATE);
        }

        return $this->startDate;
    }

    public function getProfessionalStartDate(bool $object = true): DateTime|string {
        if (!$object) {
            return self::PROFESSIONAL_START_DATE;
        }

        if ($this->professionalStartDate === null) {
            $this->professionalStartDate = new DateTime(self::PROFESSIONAL_START_DATE);
        }

        return $this->professionalStartDate;
    }
}
