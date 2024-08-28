<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static BOUTIQUIER()
 * @method static static ADMIN()
 * @method static static CLIENT()
 */
final class Role extends Enum
{
    const BOUTIQUIER = 'BOUTIQUIER';
    const ADMIN = 'ADMIN';
    const CLIENT = 'CLIENT';
}
