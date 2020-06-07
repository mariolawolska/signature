<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CentralSetting extends Model {

    const E_SIGNATURE = 'change-style-signature-in';
    const IN_PROCESS = 'in-process';
    const RE_E_SIGNATURE = 'change-style-signature-re-in';
    const CONTACT = 'You can not change you signature any more, please contact with admin';

    protected $table = 'centralSetting';

}
