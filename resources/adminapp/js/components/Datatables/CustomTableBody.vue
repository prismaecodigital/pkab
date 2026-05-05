<template>
  <tbody>
    <template v-if="data.length">
      <template v-for="item in data">
        <tr :class="getRowClassValue(item)">
          <td v-if="shouldRenderSelection">
            <multi-select :selection="selection" :row="item" />
          </td>
          <td v-for="col in columns" :class="col.tdClass" :style="col.tdStyle">
            <component
              v-if="col.tdComp"
              :is="forDynCompIs(col.tdComp)"
              :row="item"
              :field="col.field"
              :value="item[col.field]"
              :nested="item.__nested__"
              v-bind="$props">
            </component>
            <template v-else>
              {{ item[col.field] }}
            </template>
          </td>
        </tr>
        <transition name="fade">
          <tr v-if="item.__nested__ && item.__nested__.visible">
            <td :colspan="colLen">
              <component
                :is="forDynCompIs(item.__nested__.comp)"
                :row="item"
                :nested="item.__nested__"
                v-bind="$props">
              </component>
            </td>
          </tr>
        </transition>
      </template>
    </template>
    <tr v-else-if="!leftFixed && !rightFixed">
      <td :colspan="colLen" class="text-center text-muted">
        ( {{ $i18nForDatatable('No Data') }} )
      </td>
    </tr>
  </tbody>
</template>

<script>
import MultiSelect from '@laraveldaily/vue2-datatable/src/Table/MultiSelect.vue'
import props from '../../mixins/prop'
import shouldRenderSelection from '@laraveldaily/vue2-datatable/src/_mixins/shouldRenderSelection'

export default {
  name: 'CustomTableBody',
  components: { MultiSelect },
  mixins: [props, shouldRenderSelection],
  computed: {
    colLen () {
      return this.columns.length + !!this.selection
    }
  },
  methods: {
    getRowClassValue(row) {
      if (this.xprops && typeof this.xprops.getRowClass === 'function') {
        return this.xprops.getRowClass(row)
      }
      return ''
    }
  }
}
</script>
