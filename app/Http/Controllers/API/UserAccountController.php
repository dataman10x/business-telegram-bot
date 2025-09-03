<?php
namespace App\Http\Controllers\API;


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Classes\Ability;
use App\Classes\Parser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exceptions\SmartException;
use App\Exceptions\SmartResponse;
use App\Models\BotInputs;
use App\Models\User;
use App\Models\VisitCounters;
use BasementChat\Basement\Enums\MessageType;
use DateTime;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response as FacadesResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserAccountController extends Controller
{
    private $inProduction;
    private $parser;

    public function __construct()
    {
        $this->inProduction = app()->isProduction();
        $this->parser = new Parser;
    }

    public function register(string $id, string $firstname, mixed $username = null)
    {
        $hashId = $this->parser->encoder($id);

        // register if user does not exist
        $user = User::find($hashId);
        $response = false;
        if(is_null($user)) {
            $getUsername = !is_null($username)? $username: null;
            $getEmail = $hashId . '@'. env('SESSION_DOMAIN') . '.com';
            if($this->inProduction) {
                $getEmail = $hashId . '@'. env('SESSION_DOMAIN');
            }

            $baseRole = [config('constants.user_roles.user')];

            try {
                $save = new User;
                $save->id = $hashId;
                $save->name = $firstname;
                $save->username = $getUsername;
                $save->roles = $baseRole;
                $save->email = $getEmail;

                if($save->save()) {
                    $saveI = new BotInputs;
                    $saveI->id = $hashId;
                    $saveI->is_active = false;
                    $saveI->save();

                    if($saveI->save()) {
                        $saveV = new VisitCounters;
                        $saveV->id = $hashId;
                        $saveV->one_time = 1;
                        $saveV->daily = 1;
                        $saveV->monthly = 1;
                        $saveV->yearly = 1;
                        $saveV->last_date = $this->parser->dateNow();

                        if($saveV->save()) {
                            $response = true;
                        }
                    }
                }
            } catch (\Throwable $th) {
                Log::error($th);
            }
        }

        return $response;
    }

    public function info(string $id)
    {
        $hashId = $this->parser->encoder($id);
        return User::find($hashId);
    }

    public function setUserInputTrue($userId, $inputType = null)
    {
        try {
            $hashId = $this->parser->encoder($userId);
            $botInput = BotInputs::find($hashId);

            if(!is_null($botInput)) {
                $data = [
                    'type' => $inputType,
                    'is_active' => true
                ];
                $botInput->update($data);
            }
        } catch (\Throwable $th) {
            Log::error($th);
        }
    }

    public function setUserInputFalse($userId)
    {
        try {
            $hashId = $this->parser->encoder($userId);
            $botInput = BotInputs::find($hashId);

            if(!is_null($botInput)) {
                $data = [
                    'type' => null,
                    'is_active' => false
                ];
                $botInput->update($data);
            }
        } catch (\Throwable $th) {
            Log::error($th);
        }
    }

    public function updateTotalVisits($userId)
    {
        try {
            $hashId = $this->parser->encoder($userId);
            $visits = VisitCounters::find($hashId);

            if(!is_null($visits)) {
                $daily = $visits->daily;
                $monthly = $visits->monthly;
                $yearly = $visits->yearly;
                $lastDate = $visits->last_date;
                $today = $this->parser->dateNow();

                if($this->parser->diffHours($lastDate) > 24) {
                    $daily = $daily + 1;
                }

                if($this->parser->diffDays($lastDate) > 30) {
                    $monthly = $monthly + 1;
                }

                if($this->parser->diffDays($lastDate) > 365) {
                    $yearly = $yearly + 1;
                }


                $data = [
                    'daily' => $daily,
                    'monthly' => $monthly,
                    'yearly' => $yearly,
                    'last_date' => $today
                ];

                $visits->update($data);
            }
        } catch (\Throwable $th) {
            Log::error($th);
        }
    }

    public function updateDemoVisits($userId)
    {
        try {
            $hashId = $this->parser->encoder($userId);
            $visits = VisitCounters::find($hashId);

            if(!is_null($visits)) {
                $demo = $visits->demo;
                $lastDate = $visits->last_date;
                $today = $this->parser->dateNow();

                if($this->parser->diffHours($lastDate) > 24 || $demo == 0) {
                    $demo = $demo + 1;
                }

                $data = [
                    'demo' => $demo,
                    'last_date' => $today
                ];

                $visits->update($data);
            }
        } catch (\Throwable $th) {
            Log::error($th);
        }
    }
}
