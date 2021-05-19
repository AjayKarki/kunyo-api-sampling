<?php

namespace Foundation\Models;

use Illuminate\Support\Str;
use Modules\Payment\Payment;
use Laravel\Passport\HasApiTokens;
use Foundation\Lib\Role as RoleConst;
use Neputer\Supports\Cache\Cacheable;
use Neputer\Supports\Mixins\usesUuid;
use Neputer\Supports\Mixins\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class User
 * @package Foundation\Models
 */
class User extends Authenticatable
{

    use HasApiTokens, HasRoles, Notifiable, SoftDeletes, Cacheable, usesUuid;

    protected $cacheKey = 'users';

    const DEFAULT_ROLE = 'super-admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable  = [
        'character_id', 'image', 'is_sms_enabled', 'is_email_enabled', 'email', 'password', 'first_name', 'middle_name',
        'last_name', 'phone_number', 'status', 'last_login', 'is_deactivated', 'views', 'email_verified_at',
        'is_verified', 'phone_verified_at', 'phone_is_verified', 'can_see_pickers', 'bypass_code_view', 'blacklist_reason', 'blacklist_reply',
    ];

    protected $dates = [ 'last_login', 'phone_verified_at', ];

    public function getUuidKey()
    {
        return 'character_id';
    }

    public function transactions()
    {
        return $this->hasMany(Payment::class);
    }

    public function getFullName()
    {
        return ucwords(join(' ', array_filter(
            [
                $this->first_name,
                $this->middle_name,
                $this->last_name,
            ]
        )));
    }

    public function getProfilePicture()
    {
        return get_image_url('user', str_replace('public/images/user/', '', $this->image ?? 'N/A'));
    }

    public function token()
    {
        return $this->hasOne('user_verifications', 'id', 'user_id');
    }

    /**
     * Check if Logged In User can write a review on ticket
     *
     * @param $ticket
     * @return bool
     */
    public function canWriteReviewOn($ticket)
    {
        foreach($ticket->reviews as $review){
            if($review->ticket_id == $ticket->id && $review->user_id == $this->id){
                return false;
            }
        }
        return true;
    }

    /**
     * KYC Details of the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function kyc()
    {
        return $this->hasOne(KYC::class);
    }

    public function isPhoneNumberVerified()
    {
        return $this->phone_is_verified;
    }

    public function isEmailVerified()
    {
        return $this->is_verified;
    }

    public function isNotVerified()
    {
        return !$this->phone_is_verified && !$this->is_verified;
    }

    public function isAdmin()
    {
        return self::hasAccess(static::DEFAULT_ROLE) || self::hasAccess(RoleConst::$current[RoleConst::ROLE_ADMIN]);
    }

    public function isManager()
    {
        return self::hasAccess(RoleConst::$current[RoleConst::ROLE_MANAGER]);
    }

    public function isCustomer()
    {
        return self::hasAccess(RoleConst::$current[RoleConst::ROLE_CUSTOMER]);
    }

    public function seeOnlyYours()
    {
        return !$this->can_see_pickers;
    }

    /**
     * Referral Created by the user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function referral()
    {
        return $this->hasOne(Referral::class);
    }

}
