<style scoped>
  input.bed:disabled {
    background-color: #ffffff;
    border-color: #ffffff;
  }
</style>

<template>
  <select class="bed" v-if="isFocusing" v-model="stageAndPattern" @blur="isFocusing = false" @change="hasChanged" :style="getStyle()">
    <option :value="emptyBed"></option>
    <optgroup v-for="fgs in bedStatusOptions" :key="fgs.growing_stage_sequence_number" :label="fgs.growing_stage_name">
      <option v-for="fcpi in fgs.factory_cycle_pattern_items" :key="fcpi.pattern" :value="{stage:fgs, pattern:fcpi}">
        {{ fcpi.pattern }}
      </option>
    </optgroup>
  </select>
  <input class="text-center bed" type="text" v-else @focus="isFocusing = true" :value="showValue()" :style="getStyle()" :disabled="displayOnlyFixed || ! canReplace">
</template>

<script>
export default {
  props: {
    bedCoordination: {
      type: Object,
      required: true
    },
    bedStatusOptions: {
      type: Array,
      required: true
    },
    labelOfBed: {
      type: String,
      required: true
    }
  },
  data: function () {
    const fixedStatus = this.bedCoordination.fixed_status
    const stageAndPattern = {
      'stage': {
        'growing_stage_sequence_number': fixedStatus.stage,
        'number_of_holes': fixedStatus.number_of_holes
      },
      'pattern': {
        'pattern': fixedStatus.pattern,
        'number_of_panels': 0
      }
    }
    const emptyBed = {
      'stage': {
        'growing_stage_sequence_number': 0,
        'number_of_holes': 0
      },
      'pattern': {
        'pattern': '',
        'number_of_panels': null
      }
    }

    const displayOnlyFixed = this.bedStatusOptions.length === 0
    return {
      isFocusing: false,
      fixedStatus: fixedStatus,
      fixedStageAndPattern: ! displayOnlyFixed ? stageAndPattern : null,
      stageAndPattern: ! displayOnlyFixed ? stageAndPattern : null,
      emptyBed: emptyBed,
      displayOnlyFixed: displayOnlyFixed,
      canReplace: false
    }
  },
  created: function () {
    if (this.displayOnlyFixed) {
      this.stageAndPattern = {
        'stage': {
          'growing_stage_sequence_number': this.bedCoordination.stage,
          'label_color': this.bedCoordination.label_color,
          'number_of_holes': this.bedCoordination.number_of_holes
        },
        'pattern': {
          'pattern': this.bedCoordination.pattern,
          'number_of_panels': this.bedCoordination.number_of_panels
        }
      }

      return
    }

    const stage = this.bedCoordination.stage,
      pattern = this.bedCoordination.pattern

    for (const fgs of this.bedStatusOptions) {
      for (const fcpi of fgs.factory_cycle_pattern_items) {
        if (fgs.growing_stage_sequence_number === stage && fcpi.pattern === pattern) {
          this.stageAndPattern =  {'stage':fgs, 'pattern':fcpi}
        }
        if (fgs.growing_stage_sequence_number === this.fixedStatus.stage && fcpi.pattern === this.fixedStatus.pattern) {
          this.canReplace = true
        }
      }
    }

    if (this.canReplace && stage === 0) {
      this.stageAndPattern = this.emptyBed
    }
    if (! this.canReplace && stage !== 0) {
      this.stageAndPattern = {
        'stage': {
          'growing_stage_sequence_number': this.fixedStatus.stage,
          'label_color': this.fixedStatus.label_color,
          'number_of_holes': this.fixedStatus.number_of_holes
        },
        'pattern': {
          'pattern': this.fixedStatus.pattern,
          'number_of_panels': this.fixedStatus.number_of_panels
        },
        'initizlized': true
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
        return {
          'border-color': this.bedCoordination.is_fixed ? '#ff0000' : ''
        }
      }

      const label_color = '#' + this.stageAndPattern.stage.label_color
      return {
        'background-color': label_color,
        'border-color': this.displayOnlyFixed ? label_color :'#ff0000'
      }
    },
    hasChanged: function () {
      this.$emit('has-changed')
    }
  }
}
</script>
