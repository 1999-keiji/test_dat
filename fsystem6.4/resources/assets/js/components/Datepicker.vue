<template>
  <datepicker :name="attrName" format="yyyy/MM/dd" :value="value" :disabled-days-of-week="disabledDaysOfWeek" v-model="value"></datepicker>
</template>

<script>
import VueStrap from 'vue-strap'

export default {
  components: {Datepicker: VueStrap.datepicker},
  props: ['attrName', 'date', 'disabledDaysOfWeek', 'allowEmpty'],
  data: function () {
    const date = this.date || ''
    return {
      value: date.replace(/-/g, '/')
    }
  },
  watch: {
    date: function () {
      this.value = this.date.replace(/-/g, '/')
    },
    value: function () {
      this.$emit('update-date', this.value)
    }
  },
  created: function () {
    if (! this.value && ! this.allowEmpty) {
      const zeroPad = (val) => {
        return ('00' + val).slice(-2)
      }

      const today = new Date()
      this.value = [today.getFullYear(), zeroPad(today.getMonth() + 1), zeroPad(today.getDate())].join('/')
    }
  }
}
</script>
