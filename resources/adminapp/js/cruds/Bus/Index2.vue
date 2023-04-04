<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-primary card-header-icon">
            <div class="card-icon">
              <i class="material-icons">assignment</i>
            </div>
            <h4 class="card-title">
              {{ $t('global.table') }}
              <strong>{{ $t('cruds.bu.title') }}</strong>
            </h4>
          </div>
          <div class="card-body">
            <router-link
              class="btn btn-primary"
              v-if="$can(xprops.permission_prefix + 'create')"
              :to="{ name: xprops.route + '.create' }"
            >
              <i class="material-icons">
                add
              </i>
              {{ $t('global.add') }}
            </router-link>
            <button
              type="button"
              class="btn btn-default"
              @click="fetchIndexData"
              :disabled="loading"
              :class="{ disabled: loading }"
            >
              <i class="material-icons" :class="{ 'fa-spin': loading }">
                refresh
              </i>
              {{ $t('global.refresh') }}
            </button>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-12">
                <div class="table-overlay" v-show="loading">
                  <div class="table-overlay-container">
                    <material-spinner></material-spinner>
                    <span>Loading...</span>
                  </div>
                </div>
                <ve-table
                  :columns="columns"
                  :table-data="data"
                  :total="total"
                  :query="query"
                  :xprops="xprops"
                  :pageSizeOptions="[10, 25, 50, 100]"
                />
                  
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped>
/* Import the CSS for the button components */
@import '~element-ui/lib/theme-chalk/button.css';

/* Define custom styles for the button elements */
.v-button {
  margin-right: 5px;
}
</style>

<script>
import { mapGetters, mapActions } from 'vuex'
import DatatableActions from '@components/Datatables/DatatableActions'
import TranslatedHeader from '@components/Datatables/TranslatedHeader'
import HeaderSettings from '@components/Datatables/HeaderSettings'
import GlobalSearch from '@components/Datatables/GlobalSearch'
import props from '../../mixins/prop'
import prop from '../../mixins/prop'

export default {
  mixins: [prop],
  components: {
    GlobalSearch,
    HeaderSettings,
    DatatableActions
  },
  data() {
    return {
      columns: [
        {
          title: 'ID',
          field: 'id',
          key: 'a'
        },
        {
          title: 'Name',
          field: 'name',
          key: 'b',
          align: 'left'
        },
        {
          title: 'Code',
          field: 'code',
          key: 'c'
        },
        {
            field: "",
            key: "e",
            title: "Action",
            width: "",
            center: "left",
            renderBodyCell: ({ row, column, rowIndex }, h) => {
                return (
                    <span>
                        <button
                            class="button-demo"
                            on-click={() => this.editRow(rowIndex)}
                        >
                            Edits
                        </button>
                        &nbsp;
                        <button
                            class="button-demo"
                            on-click={() => this.deleteRow(rowIndex)}
                        >
                            Delete
                        </button>
                    </span>
                );
            },
        },

      ],
      query: { sort: 'id', order: 'desc', limit: 100, s: '' },
      xprops: {
        module: 'BusIndex',
        route: 'bus',
        permission_prefix: 'bu_'
      }
    }
  },
  beforeDestroy() {
    this.resetState()
  },
  computed: {
    ...mapGetters('BusIndex', ['data', 'total', 'loading'])
  },
  // mounted() {
  //   this.fetchIndexData()
  // },
  watch: {
    query: {
      handler(query) {
        this.setQuery(query)
        this.fetchIndexData()
      },
      deep: true
    }
  },
  methods: {
    ...mapActions('BusIndex', ['fetchIndexData', 'setQuery', 'resetState']),
            editRow(rowIndex) {
                alert(`edit row number:${rowIndex}`);
            },
            deleteRow(rowIndex) {
              alert(`delete row number:${rowIndex}`);
            },
  }
}
</script>