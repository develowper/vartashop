<?php

namespace App\Policies;

use App\Http\Helpers\Variable;
use App\Models\Article;
use App\Models\Banner;
use App\Models\Business;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Podcast;
use App\Models\Project;
use App\Models\ProjectItem;
use App\Models\Site;
use App\Models\Text;
use App\Models\Ticket;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Video;
use Illuminate\Auth\Access\HandlesAuthorization;
use Termwind\Components\Dd;

class UserPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {

        if ($user->role == 'go') {
            return true;
        }

    }

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user, $type, $item = null, $abort = null)
    {
        if (!$user->is_active) {
            return abort(403, __("user_is_inactive"));
        }
        if ($user->is_block) {
            return abort(403, __("user_is_blocked"));
        }
        switch ($type) {
            case 'log'  :
                if (in_array($user->role, ['ad',]) || (optional($item)->user_id == $user->id))
                    return true;
                break;


        }

        if ($abort)
            return abort(403, __("access_denied"));
        else return false;

    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\User $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, $item)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, $item, $abort = true, $option = null)
    {

        if (!$user->is_active) {
            return abort(403, __("user_is_inactive"));
        }
        if ($user->is_block) {
            return abort(403, __("user_is_blocked"));
        }

        switch ($item) {
            case User::class  :
            case Notification::class  :
                $res = in_array($user->role, ['ad',]);
                break;
            case Site::class  :
            case Business::class  :
            case Podcast::class  :
            case Video::class  :
            case Banner::class  :
            case Article::class  :
            case Transfer::class  :
            case Text::class  :
                $res = in_array($user->role, ['us', 'ad',]);

                break;
        }

        if ($abort && empty($res))
            return abort(403, __("access_denied"));
        if (!empty($res))
            return true;
        return false;
    }


    public function edit(User $user, $item, $abort = true, $data = null)
    {

        if ($user->status == 'inactive') {
            return abort(403, __("user_is_inactive"));
        }
        if ($user->status == 'block') {
            return abort(403, __("user_is_blocked"));
        }

        if ($item && $item->status == 'block') {
            return abort(403, __("item_is_blocked"));
        }
        switch ($item) {
            case $item instanceof User  :
                $res = in_array($user->role, ['ad',]);
                break;
            case $item instanceof Text  :
            case $item instanceof Video  :
            case $item instanceof Podcast  :
            case $item instanceof Banner  :
                $res = $user->role == 'us' && optional($item)->owner_id == $user->id || in_array($user->role, ['ad',]) || optional($item->projectItem)->operator_id == $user->id;
                break;
            case $item instanceof Site  :
            case $item instanceof Business  :
            case $item instanceof Article  :
            case $item instanceof Notification  :
            case $item instanceof Transfer  :
                $res = $user->role == 'us' && optional($item)->owner_id == $user->id || in_array($user->role, ['ad',]);
                break;
            case $item instanceof Ticket  :
                $res = ($item->from_id == $user->id && $item->from_type == 'user') || ($item->to_id == $user->id && $item->to_type == 'user');

                break;
            case $item instanceof Order  :
                $res = $item->user_id == $user->id;

                break;
        }
        if ($abort && empty($res))
            return abort(403, __("access_denied"));
        if (!empty($res))
            return true;
        return false;
    }


    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\User $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\User $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\User $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, User $model)
    {
        //
    }
}
