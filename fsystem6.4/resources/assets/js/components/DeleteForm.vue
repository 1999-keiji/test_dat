<template>
  <form ref="form" :action="routeAction" method="POST">
    <button v-if="isLargeButton" class="btn btn-lg btn-danger pull-right" type="button" v-on:click="deleteDate($event)">
      <i class="fa fa-trash"></i>&nbsp;削除
    </button>
    <button v-else class="btn btn-sm btn-danger" type="button" v-on:click="deleteDate($event)">削除</button>
    <input name="_token" type="hidden" :value="csrf">
    <input name="_method" type="hidden" value="DELETE">
  </form>
</template>

<script>
export default {
  props: {
    routeAction: {
      type: String,
      required: true
    },
    isLargeButton: {
      type: Boolean,
      default: false
    }
  },
  data: () => {
    return {
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
  },
  methods: {
    deleteDate: function (event) {
      if (! confirm('選択したデータを削除しますか？')) {
        return false
      }

      event.target.disabled = true
      this.$refs.form.submit()
    }
  }
}
</script>
