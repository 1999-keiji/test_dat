<style scoped>
  table.table-in-modal {
    margin-top: 1em;
  }
</style>

<template>
  <div>
    <button class="btn btn-sm btn-warning" type="button" @click="showModal = true">情報</button>
    <modal title="シミュレーション情報" class="text-left" effect="fade" v-model="showModal">
      <div class="form-horizontal basic-form">
        <div class="row">
          <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group simulation_info-form">
              <label for="master-address" class="col-md-5 col-sm-5 control-label">シミュレーション名</label>
              <div class="col-md-7 col-sm-7">{{ growthSimulation.simulation_name }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-10 col-sm-10 col-md-offset-1 col-sm-offset-1">
          <table class="table table-color-bordered table-more-condensed table-in-modal">
            <thead>
              <tr>
                <th></th>
                <th>播種</th>
                <th>収穫</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(growthSimulationItems, input_group) in growthSimulationItemLists" :key="input_group">
                <th>入力{{ input_group }}</th>
                <template v-for="gsi in growthSimulationItems">
                  <td v-if="gsi.growing_stage === growingStageList['播種']" :key="gsi.growing_stages_sequence_number">{{ gsi.date }}</td>
                  <td v-if="gsi.growing_stage === growingStageList['収穫']" :key="gsi.growing_stages_sequence_number">{{ gsi.date }}</td>
                </template>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div slot="modal-footer"></div>
    </modal>
  </div>
</template>

<script>
import VueStrap from 'vue-strap'

export default {
  components: {Modal: VueStrap.modal},
  props: {
    growthSimulation: {
      type: Object,
      required: true
    },
    growthSimulationItemLists: {
      type: [Array, Object],
      required: true
    },
    growingStageList: {
      type: Object,
      required: true
    }
  },
  data: () => {
    return {
      showModal: false
    }
  }
}
</script>
