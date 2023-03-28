<template>
  <table class="table table-color-bordered">
    <tbody>
      <tr>
        <th>工場</th>
        <td>
          <select class="form-control" name="factory_code" v-model="factoryCode" @change="getFactorySpecies">
            <option value=""></option>
            <option v-for="f in factories" :key="f.factory_code" :value="f.factory_code">{{ f.factory_abbreviation }}</option>
          </select>
        </td>
        <th>工場品種</th>
        <td>
          <select class="form-control" name="factory_species_code" v-model="factorySpeciesCode" :disabled="disabledToSelectFactorySpecies">
            <option value=""></option>
            <option v-for="fs in factorySpeciesList" :key="fs.factory_species_code" :value="fs.factory_species_code">{{ fs.factory_species_name }}</option>
          </select>
        </td>
      </tr>
      <tr v-if="! fixedFlag">
        <th>シミュレーション名</th>
        <td>
          <input class="form-control ime-active" name="simulation_name" maxlength="50" :value="simulationName">
        </td>
        <th>作成者</th>
        <td>
          <input class="form-control ime-active" name="user_name" maxlength="50" :value="userName">
        </td>
      </tr>
      <tr v-if="fixedFlag">
        <th>シミュレーション名</th>
        <td>
          <input class="form-control ime-active" name="simulation_name" maxlength="50" :value="simulationName">
        </td>
        <th>確定日</th>
        <td>
          <datepicker-ja attr-name="fixed_at_begin" :date="fixedAtBegin" :allow-empty="true"></datepicker-ja> ～
          <datepicker-ja attr-name="fixed_at_end" :date="fixedAtEnd" :allow-empty="true"></datepicker-ja>
        </td>
      </tr>
      <tr v-if="fixedFlag">
        <th>作成者</th>
        <td>
          <input class="form-control ime-active" name="user_name" maxlength="50" :value="userName">
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  props: {
    factories: {
      type: Array,
      required: true
    },
    searchParams: {
      type: Object,
      required: true
    },
    oldParams: {
      type: Object,
      required: true
    },
    fixedFlag: {
      type: Boolean,
      required: true
    }
  },
  data: function () {
    return {
      factoryCode: this.searchParams.factory_code || this.oldParams.factory_code || null,
      factorySpeciesCode: this.searchParams.factory_species_code || this.oldParams.factory_species_code || null,
      factorySpeciesList: [],
      simulationName: this.searchParams.simulation_name || this.oldParams.simulation_name || null,
      userName: this.searchParams.user_name || this.oldParams.user_name || null,
      fixedAtBegin: this.searchParams.fixed_at_begin || this.oldParams.fixed_at_begin || '',
      fixedAtEnd: this.searchParams.fixed_at_end || this.oldParams.fixed_at_end || ''
    }
  },
  created: function () {
    if (this.factoryCode) {
      this.getFactorySpecies()
      this.factorySpeciesCode = this.searchParams.factory_species_code || this.oldParams.factory_species_code
    }
  },
  computed: {
    disabledToSelectFactorySpecies: function () {
      return this.factorySpeciesList.length === 0
    }
  },
  methods: {
    getFactorySpecies: function () {
      this.factorySpeciesCode = null
      this.factorySpeciesList = []
      if (! this.factoryCode) {
        return
      }

      axios.get('/api/get-factory-species', { params: { factory_code: this.factoryCode } })
        .then(response => {
          this.factorySpeciesList = response.data
          if (this.disabledToSelectFactorySpecies) {
            alert('工場品種が未登録の工場です。')
          }
        })
        .catch(() => { alert('通信エラーが発生しました。しばらくお待ちください。') })
    }
  }
}
</script>
