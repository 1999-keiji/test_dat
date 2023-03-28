<style scoped>
  input.bed:disabled {
    background-color: #ffffff;
    border-color: #ffffff;
  }
</style>

<template>
  <select class="bed" v-if="isFocusing" v-model="stageAndPattern" @blur="isFocusing = false" @change="hasChanged" :style="getStyle()">
    <option value=""></option>
    <optgroup v-for="fgs in bedStatusOptions" :key="fgs.growing_stage_sequence_number" :label="fgs.growing_stage_name">
      <option v-for="fcpi in fgs.factory_cycle_pattern_items" :key="fcpi.pattern" :value="{stage:fgs, pattern:fcpi}">
        {{ fcpi.pattern }}
      </option>
    </optgroup>
  </select>
  <input class="text-center bed" type="text" v-else @focus="isFocusing = true" :value="showValue()" :style="getStyle()" :disabled="hasFixed || displayOnlyFixed">
</template>

<script>
export default {
  props: {
    bedCoordination: {
      type: Object,
      required: true
    },
    factoryGrowingStages: {
      type: Array,
      required: true
    },
    bedStatusOptions: {
      type: Array,
      required: true
    },
    labelOfBed: {
      type: String,
      required: true
    },
    hasFixed: {
      type: Boolean,
      required: true
    }
  },
  data: function () {
    return {
      displayOnlyFixed: this.bedStatusOptions.length === 0,
      isFocusing: false,
      stageAndPattern: null
    }
  },
  created: function () {
    const stage = this.bedCoordination.stage,
      pattern = this.bedCoordination.pattern
    if (! (stage && pattern)) {
      return
    }

    for (const fgs of this.factoryGrowingStages) {
      for (const fcpi of fgs.factory_cycle_pattern_items) {
        if (fgs.growing_stage_sequence_number === stage && fcpi.pattern === pattern) {
          this.stageAndPattern = {'stage':fgs, 'pattern':fcpi}
        }
      }
    }
  },
  watch: {
    stageAndPattern: function (selected, prevSelected) {
      this.$emit('update-panel-allocation', this.bedCoordination, selected, prevSelected)
    }
  },
  methods: {
    showValue: function () {
      if (! this.stageAndPattern) {
        return ''
      }

      return this.labelOfBed === 'pattern' ?
        this.stageAndPattern.pattern.pattern :
        this.stageAndPattern.pattern.number_of_panels
    },
    getStyle: function () {
      if (! this.stageAndPattern) {
        return {}
      }

      const label_color = '#' + this.stageAndPattern.stage.label_color
      return {
        'background-color': label_color,
        'border-color': label_color
      }
    },
    hasChanged: function () {
      this.$emit('has-changed')
    }
  }
}
</script>
