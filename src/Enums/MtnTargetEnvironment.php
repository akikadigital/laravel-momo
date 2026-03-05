<?php

namespace Akika\MoMo\Enums;

/**
 * MTN Target Environment Enum
 *
 * Defines the correct X-targetenvironment values for MTN country operations
 * and sandbox testing environment.
 *
 * @link https://momoapi.mtn.com/api-documentation/common-error
 */
enum MtnTargetEnvironment: string
{
    case Benin = 'mtnbenin';
    case Cameroon = 'mtncameroon';
    case Congo = 'mtncongo';
    case Ghana = 'mtnghana';
    case GuineaConakry = 'mtnguineaconakry';
    case IvoryCoast = 'mtnivorycoast';
    case Liberia = 'mtnliberia';
    case Nigeria = 'mtnnigeria';
    case Rwanda = 'mtnrwanda';
    case SouthAfrica = 'mtnsouthafrica';
    case SouthSudan = 'mtnsouthsudan';
    case Swaziland = 'mtnswaziland';
    case Uganda = 'mtnuganda';
    case Zambia = 'mtnzambia';
    case Sandbox = 'sandbox';
}
