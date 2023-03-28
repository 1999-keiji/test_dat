<template>
  <v-select :options="options" :name="attrName" multiple clear-button :value="selected" :disabled="disabled"></v-select>
</template>

<script>
import VueStrap from 'vue-strap'

export default {
  components: {vSelect: VueStrap.select},
  props: ['attrName', 'options', 'selected', 'isDisabled'],
  data: function () {
    return {
      disabled: this.isDisabled === 1
    }
  }
}
</script>
