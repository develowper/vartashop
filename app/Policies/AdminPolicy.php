<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\AdminFinancial;
use App\Models\Agency;
use App\Models\AgencyFinancial;
use App\Models\Car;
use App\Models\City;
use App\Models\Driver;
use App\Models\Order;
use App\Models\Pack;
use App\Models\Product;
use App\Models\Repository;
use App\Models\RepositoryOrder;
use App\Models\Setting;
use App\Models\Shipping;
use App\Models\ShippingMethod;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserFinancial;
use App\Models\Variation;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
{
    use HandlesAuthorization;


    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function before(Admin $admin, $ability)
    {

        if ($admin->role == 'god') {
            return true;
        }

    }

    public function view(Admin $admin, $item, $abort = true, $option = null)
    {

        if ($admin->status == 'inactive') {
            $message = __("user_is_inactive");

        }
        if ($admin->status == 'block') {
            $message = __("user_is_blocked");
        }
        if (empty($message) && $item)
            switch ($item) {
                case     Agency::class :
                    $res = $admin->hasAccess('view_agency');
                    break;
                case    Repository::class:
                    $res = $admin->hasAccess('view_repository');
                    break;
                case    ShippingMethod::class:
                    $res = $admin->hasAccess('view_shipping-method');
                    break;
                case    Pack::class:
                    $res = $admin->hasAccess('view_pack');
                    break;
                case    Product::class:
                    $res = $admin->hasAccess('view_product');
                    break;
                case    Variation::class:
                    $res = $admin->hasAccess('view_variation');
                    break;
                case    User::class:
                    $res = $admin->hasAccess('view_user');
                    break;
            }

        if ($abort && empty($res))
            return abort(403, $message ?? __("access_denied"));
        if (!empty($res))
            return true;
        return false;
    }

    public function create(Admin $admin, $item, $abort = true, $option = null)
    {

        if ($admin->status == 'inactive') {
            $message = __("user_is_inactive");

        }
        if ($admin->status == 'block') {
            $message = __("user_is_blocked");
        }
        if (empty($message) && $item)
            switch ($item) {
                case     Agency::class :
                    $res = $admin->hasAccess('create_agency');
                    break;
                case    Repository::class:
                    $res = $admin->hasAccess('create_repository');
                    break;
                case    ShippingMethod::class:
                    $res = $admin->hasAccess('create_shipping-method');
                    break;
                case    Pack::class:
                    $res = $admin->hasAccess('create_pack');
                    break;
                case    Product::class:
                    $res = $admin->hasAccess('create_product');
                    break;
                case    RepositoryOrder::class:
                    $res = $admin->hasAccess('create_repository_order');
                    break;
                case    Variation::class:
                    $res = $admin->hasAccess('create_variation');
                    break;
                case    Driver::class:
                    $res = $admin->hasAccess('create_driver');
                    break;
                case    Car::class:
                    $res = $admin->hasAccess('create_car');
                    break;
                case    Shipping::class:
                    $res = $admin->hasAccess('create_shipping');
                    break;
                case    Ticket::class:
                    $res = $admin->hasAccess('create_ticket');
                    break;
                case    Admin::class:
                    $res = $admin->hasAccess('create_admin');
                    break;
            }

        if ($abort && empty($res))
            return abort(403, $message ?? __("access_denied"));
        if (!empty($res))
            return true;
        return false;
    }

    public function edit(Admin $admin, $item, $abort = true, $option = null)
    {
        if (!$item) {
            $message = __("item_not_found");

        }
        if ($admin->status == 'inactive') {
            $message = __("user_is_inactive");

        }
        if ($admin->status == 'block') {
            $message = __("user_is_blocked");
        }

        if ($item && $item->status == 'block') {
            $message = __("item_is_blocked");
        }
        if (empty($message) && $item)
            switch ($item) {
                case  $item instanceof Agency  :
                    $res = $admin->hasAccess('edit_agency');
                    if ($res) {
                        $myAgency = Agency::find($admin->agency_id);
                        if (!$myAgency)
                            $res = false;
                        elseif ($myAgency->level == '1')
                            $res = $myAgency->id == $item->id || in_array($myAgency->province_id, $item->access ?? []);
                        elseif ($myAgency->level == '2')
                            $res = $myAgency->id == $item->id || in_array($myAgency->id, $item->access ?? []);
                        elseif ($myAgency->level == '3')
                            $res = $myAgency->id == $item->id;

                    }

                    break;
                case  $item instanceof Repository  :
                    $res = $admin->hasAccess('edit_repository');

                    break;
                case  $item instanceof ShippingMethod  :
                    $res = $admin->hasAccess('edit_shipping-method');
                    break;

                case  $item instanceof Pack  :
                    $res = $admin->hasAccess('edit_pack');
                    break;
                case   $item instanceof Product :
                    $res = $admin->hasAccess('edit_product');
                    break;
                case   $item instanceof Variation :
                    $res = $admin->hasAccess('edit_variation');
                    break;
                case   $item instanceof RepositoryOrder :
                    $res = $admin->hasAccess('edit_repository_order');
                    if ($res)
                        break;
                    $agencyIds = $admin->allowedAgencies(Agency::find($admin->agency_id))->pluck('id');
                    $res = in_array($item->from_agency_id, $agencyIds->toArray());
                    break;
                case   $item instanceof Driver :
                    $res = $admin->hasAccess('edit_driver');
                    break;
                case   $item instanceof Car :
                    $res = $admin->hasAccess('edit_car');
                    break;
                case   $item instanceof Order :
                    $res = $admin->hasAccess('edit_order');
                    break;
                case   $item instanceof Shipping :
                    $res = $admin->hasAccess('edit_shipping');
                    break;
                case   $item instanceof Ticket :
                    $res = $admin->hasAccess('edit_ticket');
                    break;
                case   $item instanceof User :
                    $res = $admin->hasAccess('edit_user');
                    break;
                case   $item instanceof Admin :
                    $res = $admin->hasAccess('edit_admin');
                    break;
                case   $item instanceof Setting :
                    $res = $admin->hasAccess('edit_setting');
                    break;
                case   $item instanceof UserFinancial :
                case   $item instanceof AdminFinancial :
                case   $item instanceof AgencyFinancial :
                    $res = $admin->hasAccess('edit_financial');
                    break;
            }

        if ($abort && empty($res))
            return abort(403, $message ?? __("access_denied"));
        if (!empty($res))
            return true;

        return false;
    }
}
