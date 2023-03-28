<template>
  <input
    :id="attrName"
    class="form-control text-right"
    :class="{'has-error': hasError}"
    :maxlength="maxLength"
    :name="attrName"
    type="text"
    :disabled="disabled"
    data-toggle="tooltip"
    :title="helpText"
    v-model="number"
    @focus="unformat"
    @blur="format">
</template>

<script>
const number_format = require('locutus/php/strings/number_format')

export default {
  props: ['value', 'attrName', 'hasError', 'disabled', 'maxLength', 'decimals', 'helpText'],
  data: function () {
    let number = this.value
    if (number !== '' && ! this.hasError) {
      number = number_format(number, this.decimals)
    }

    return {
      number: number
    }
  },
  watch: {
    value: function (number) {
      if (number !== '') {
        this.number = number_format(number, this.decimals)
      }
    }
  },
  methods: {
    unformat: function (event) {
      this.number = this.number
        .replace(/[Ａ-Ｚａ-ｚ０-９]/g, value => {return String.fromCharCode(value.charCodeAt(0)-0xFEE0)})
        .replace(/,/g, '')

      setTimeout(() => {
        event.target.select()
      }, 0)
    },
    format: function () {
      const number = this.number
      if (number === '') {
        this.$emit('update-value', number)
      }

      if (number !== '') {
        const replacedNumber = number.replace(/[Ａ-Ｚａ-ｚ０-９]/g, (value) => {return String.fromCharCode(value.charCodeAt(0)-0xFEE0)})
        this.number = number_format(replacedNumber, this.decimals)

        this.$emit('update-value', replacedNumber)
      }
    }
  }
}
</script>
