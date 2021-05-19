<?php

namespace Foundation\Models;

use App\Foundation\Lib\Campaign;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Neputer\Supports\BaseModel as Model;

/**
 * Class EmailCampaign
 * @package Foundation\Models
 */
class EmailCampaign extends Model
{
    protected $table = 'campaigns';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'status'
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'type' => Campaign::TYPE_EMAIL
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('campaigns.type', Campaign::TYPE_EMAIL);
        });
    }

    /**
     * The Email Groups associated with this email campaign
     *
     * @return BelongsToMany
     */
    public function emailGroups()
    {
        return $this->belongsToMany(EmailGroup::class, 'campaign_group', 'campaign_id', 'group_id')->withTimestamps();
    }

    /**
     * The Email associated with this campaign.
     *
     * @return HasOne
     */
    public function email()
    {
        return $this->hasOne(Email::class);
    }

    /**
     * Get Email Lists in a campaign
     *
     * @return \Illuminate\Support\Collection
     */
    public function emailList()
    {
        $list = [];
        foreach ($this->emailGroups as $group){
            foreach ($group->emailLists as $email){
                $list[] = $email;
            }
        }
        return (new Collection($list));
    }

    /**
     * List of Links Clicked for this campaign
     *
     * @return HasMany
     */
    public function linkClicks()
    {
        return $this->hasMany(EmailCampaignLinkClick::class);
    }


}
