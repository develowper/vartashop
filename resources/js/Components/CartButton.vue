<template>
  <div class="flex items-center ">

    <Link :href="cartLink || route('checkout.cart')"
          class="flex mx-1 btn  border relative   font-medium
            focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-300 ease-in-out   p-2 rounded-lg  rounded-lg hover:bg-primary-400 hover:text-white">
      <ShoppingCartIcon class=" h-5 w-5"/>
      <div v-show="qty && qty>0"
           class="absolute px-2 text-sm bottom-[-.5rem] start-[-.5rem] border border-white  rounded-md text-white bg-rose-500">
        {{ qty }}

      </div>
    </Link>
  </div>
</template>

<script>
import {Link} from '@inertiajs/vue3';
import {
  UserIcon,
  ChevronDownIcon,
  ArrowRightOnRectangleIcon,
  ShoppingCartIcon,
} from "@heroicons/vue/24/outline";
import Image from "@/Components/Image.vue";
import mitt from 'mitt'

export const emitter = mitt()
export default {

  data() {
    return {
      user: this.$page.props.auth.user,
      cart: this.$page.props.cart,
      qty: 0,
    }
  },
  components: {
    Link,
    UserIcon,
    ChevronDownIcon,
    Image,
    ArrowRightOnRectangleIcon,
    ShoppingCartIcon,
  },
  props: ['link', 'cartLink'],
  setup(props) {


  },
  mounted() {
    this.update();

    this.emitter.on('updateCart', (cart) => {

      this.cart = cart;
      this.$page.props.cart = cart;
      this.setCartQty();
    });

    this.setCartQty();
  },
  computed: {},
  watch: {},
  methods: {
    setCartQty() {
      this.qty = 0;
      this.qty = this.cart ? this.cart.total_items : 0;

      return;
      if (this.cart && this.cart.shipments && this.cart.shipments.length > 0)
        for (let idx in this.cart.shipments) {
          for (let id in this.cart.shipments[idx].items) {

            if (this.cart.shipments[idx].items[id].cart_item.qty && this.cart.shipments[idx].items[id].cart_item.qty > 0)
              this.qty += parseInt(this.cart.shipments[idx].items[id].cart_item.qty);
            break;

          }
        }

      if (this.cart && this.cart.items && this.cart.items.length > 0)
        for (let idx in this.cart.items) {
          if (this.cart.items[idx].qty && this.cart.items[idx].qty > 0)
            this.qty += parseInt(this.cart.items[idx].qty);
        }
    },
    update(params) {
      this.isLoading(true);
      this.loading = true;
      window.axios.patch(this.link || route('cart.update'), params,
          {})
          .then((response) => {
            if (response.data && response.data.message && params) {
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
            this.loading = false;
            this.isLoading(false);
          });
    },

  }
}

</script>
<style lang="scss" scoped>

li > ul {
  transform: translatex(100%) scale(0)
}

li:hover {
  > ul {
    transform: translatex(101%) scale(1);
  }
}

//.dropdown {
//    > button {
//        transform: rotate(0deg);
//
//        &:hover {
//            svg {
//                transition: all 500ms;
//                transform: rotate(90deg)
//            }
//        }
//    }
//
//    ul {
//        &:hover {
//            svg {
//                transition: all 500ms;
//                transform: rotate(90deg)
//            }
//        }
//    }
//
//}


/* Below styles fake what can be achieved with the tailwind config
   you need to add the group-hover variant to scale and define your custom
   min width style.
     See https://codesandbox.io/s/tailwindcss-multilevel-dropdown-y91j7?file=/index.html
     for implementation with config file
*/
//.group:hover .group-hover\:scale-100 {
//    transform: scale(1)
//}
//
//.group:hover .group-hover\:-rotate-180 {
//    transform: rotate(180deg)
//}
//
//.scale-0 {
//    transform: scale(0)
//}
//
.min-w {
  min-width: 20rem !important;
}
</style>
