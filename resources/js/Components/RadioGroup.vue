<template>
  <div class="    flex justify-center my-3  text-primary-500  rounded bg-primary-100  ">
    <div v-for="item,idx in items" class="   grow justify-center   ">
      <input :checked="item==selected||null" v-model="selected" type="radio" :name="name" :id="`${name}-${item}`"
             :value="item" @change="$emit('update:modelValue', $event.target.value ) "
             class="peer hidden"/>
      <label :for="`${name}-${item}`" :class="idx==0? 'rounded-s' : idx==items.length-1? 'rounded-e':'rounded-0'"
             class="duration-300 transition-all     flex justify-center text-center cursor-pointer select-none   p-2  peer-checked:bg-primary-500 peer-checked:font-bold peer-checked:text-white">
        {{ __(item) }}
      </label>
    </div>

  </div>
</template>

<script>
export default {
  data() {
    return {
      selected: this.beforeSelected || this.items[0]
    }
  },
  props: ['name', 'items', 'beforeSelected',],
  emits: ['update:modelValue'],
}
</script>
