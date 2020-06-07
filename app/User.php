<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
//
use \App\CentralSetting;

class User extends Authenticatable {

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    
//    ******************************************************************* CENTRAL SETTING  *****
    /**
     * Define relation USER - CENTRAL SETTING
     * @return type
     */
    public function settings() {
        return $this->hasMany('\App\CentralSetting', 'userId', 'id');
    }

    /**
     * Get single or all Central Setting
     * 
     * @param string $actionType
     * 
     * @return array of PreOpCentralSetting
     */
    public function getSettings($actionType = null) {
        if ($actionType) {
            $collection = $this->settings;
            $actionValue = false;
            foreach ($collection as $details) {

                if ($details->actionType == $actionType) {
                    $actionValue = json_decode($details->actionValue, JSON_FORCE_OBJECT);
                }
            }

            return $actionValue;
        } else {
            return false;
        }
    }

    /**
     * Get single or all Central Setting
     * 
     * @param string $actionType
     * 
     * @return Boolean
     */
    public function setSettings($actionType = null, $actionValue = null) {
        if (empty($actionType || $actionValue)) {

            return false;
        }
        $sqlWhere = [
            ['userId', '=', $this->id],
            ['actionType', '=', $actionType],
        ];
        $centralSettin = CentralSetting::where($sqlWhere)->first();
        if ($centralSettin instanceof CentralSetting) {
            /**
             * Update flow
             */
            $centralSettin->actionValue = json_encode($actionValue, JSON_FORCE_OBJECT);

            $centralSettin->save();
        } else {
            /**
             * Insert flow
             */
            try {
                $centralSettin = new CentralSetting();
                $centralSettin->userId = $this->id;
                $centralSettin->actionType = $actionType;
                $centralSettin->actionValue = json_encode($actionValue, JSON_FORCE_OBJECT);

                $centralSettin->save();
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
    }

    //    **************************************************************** CENTRAL SETTING END  *****
}
