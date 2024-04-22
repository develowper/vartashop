<template>

  <div class="flex    flex-col ">

    <div class="flex  ">
      <div @click.stop="!show? show=true:  !loading? edit({variation_id:productId,qty:inCart}):null "
           :class="  show ?'rounded-s-md':'rounded-md'"
           class="border grow   flex justify-center items-center   border-primary-500 text-primary-500 p-2  hover:bg-primary-400 hover:text-white">
        <ShoppingCartIcon v-if=" !show" :class="{'border-e pe-1':inCart}" class="w-7 h-6  "/>
        <div v-if="inCart && !show" class="mx-2 font-bold text-primary-700">{{ inCart }}</div>
        <div v-if=" show" class="mx-2 text-center" @click="">
          <div v-if="!loading"> {{ __('accept') }}</div>
          <LoadingIcon v-else class="w-6 h-6 fill-primary-500"/>
        </div>
      </div>
      <div v-if="show && !loading" @click.stop="inCart=inCart; show=false"
           class=" text-white flex   rounded-e-md bg-danger-600 hover:bg-danger-500 hover:text-white">
        <XMarkIcon class="w-8  mx-2"/>
      </div>
    </div>
    <Transition name="fade">
      <div class="   my-2 flex justify-center items-stretch text-primary-500 "
           v-show=" show">
        <div @click.stop="plus()"
             class=" items-center flex   border rounded-s border-primary-500 hover:bg-primary-500 hover:text-white">
          <PlusIcon
              class="w-6  mx-3 "/>
        </div>
        <input @click.stop type="number" min="0" v-model="inCart"
               class="  flex w-full shrink text-lg p-1 border text-center focus:border-primary-500 border-primary-500 focus:ring-primary-500">
        <div @click.stop="minus()"
             class="items-center flex  border rounded-e border-primary-500 hover:bg-primary-500 hover:text-white">
          <MinusIcon
              class="w-6 mx-3"/>
        </div>
      </div>
    </Transition>
  </div>

</template>

<script>
import {
  ChevronDownIcon,
  ChevronUpIcon,
  ShoppingCartIcon,
  PlusIcon,
  MinusIcon,
  XMarkIcon,
} from "@heroicons/vue/24/outline";
import TextInput from "@/Components/TextInput.vue";
import LoadingIcon from "@/Components/LoadingIcon.vue";


export default {
  name: "CartItemButton",
  data() {
    return {
      inCartOld: 0,
      inCart: 0,
      show: false,
      modal: null,
      loading: false,
      cart: this.$page.props.cart,
    }
  },
  expose: ['update:cart'],
  props: ['productId', 'inShop', 'link'],
  components: {
    TextInput,
    ChevronDownIcon,
    ChevronUpIcon,
    ShoppingCartIcon,
    PlusIcon,
    MinusIcon,
    LoadingIcon,
    XMarkIcon,
  },
  mounted() {
    // if (!window.Modal) {
    //   window.Modal = Modal;
    // initTE({Modal});
    // }

    this.setInCartQty();
    this.inCartOld = this.inCart;

    this.emitter.on('updateCart', (cart) => {
      this.cart = cart;
      this.setInCartQty();
    });
  },
  methods: {
    setInCartQty() {
      this.inCart = 0

      if (this.cart && this.cart.orders && this.cart.orders.length > 0)
        for (let ix in this.cart.orders) {
          for (let idx in this.cart.orders[ix].shipments) {
            for (let id in this.cart.orders[ix].shipments[idx].items) {
              if (this.cart.orders[ix].shipments[idx].items[id].cart_item.variation_id == this.productId) {
                this.inCart = this.cart.orders[ix].shipments[idx].items[id].cart_item.qty;
                this.inCart = this.inCart ? parseFloat(this.inCart) : 0;
                break;
              }
            }
          }
        }
    }
    ,
    isInt(value) {
      return (typeof value === 'number' &&
          isFinite(value) &&
          Math.floor(value) === value);

    }
    ,
    plus() {
      if (this.isInt(this.inCart))
        this.inCart++;
      else this.inCart = 0;
    }
    ,
    minus() {
      if (this.isInt(this.inCart) && this.inCart > 1)
        this.inCart--;
      else this.inCart = 0;
    }
    ,
    edit(params) {
      this.loading = true;
      window.axios.patch(this.link || route('cart.update'), params,
          {})
          .then((response) => {
            if (response.data && response.data.message) {
              this.showToast('success', response.data.message);
            }

            if (response.data.cart) {
              this.updateCart(response.data.cart)
            }

            this.show = false;
          })

          .catch((error) => {
            this.error = this.getErrors(error);
            if (error.response && error.response.data) {

            }
            this.showToast('danger', this.error);

          })
          .finally(() => {
            // always executed
            this.setInCartQty();
            this.loading = false;

          });
    }
    ,

  }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>